<?php

namespace App\Services;

use App\Mail\PortalOtpMail;
use App\Models\ClientPortalAccessLog;
use App\Models\ClientPortalOtp;
use App\Models\ClientPortalToken;
use App\Models\ClientPortalTrustedDevice;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PortalAuthService
{
    public const COOKIE_NAME = 'portal_td';
    public const COOKIE_LIFETIME_DAYS = 60;
    public const OTP_TTL_MINUTES = 10;
    public const OTP_MAX_ATTEMPTS = 5;
    public const LOCKOUT_FAILED_THRESHOLD = 10;
    public const LOCKOUT_WINDOW_MINUTES = 60;
    public const LOCKOUT_DURATION_MINUTES = 60;

    /**
     * Resolve a contact from a raw portal token.
     * Looks up the new client_portal_tokens table first; falls back to the
     * legacy contacts.portal_token column (and back-fills the new table).
     */
    public function findContactByToken(string $token): ?Contact
    {
        if ($token === '') {
            return null;
        }

        $hash = $this->hashToken($token);

        $row = ClientPortalToken::where('token_hash', $hash)
            ->whereNull('revoked_at')
            ->first();

        if ($row) {
            $row->forceFill(['last_used_at' => now()])->save();
            return $row->contact;
        }

        $contact = Contact::where('portal_token', $token)->first();
        if ($contact) {
            ClientPortalToken::create([
                'contact_id' => $contact->id,
                'token_hash' => $hash,
                'last_used_at' => now(),
            ]);
        }

        return $contact;
    }

    /**
     * Issue a new portal token for a contact (raw token returned, hash stored).
     */
    public function issueToken(Contact $contact): string
    {
        $raw = bin2hex(random_bytes(32));

        ClientPortalToken::create([
            'contact_id' => $contact->id,
            'token_hash' => $this->hashToken($raw),
            'last_used_at' => null,
        ]);

        return $raw;
    }

    /**
     * Revoke all portal tokens for a contact.
     */
    public function revokeAllTokens(Contact $contact): void
    {
        ClientPortalToken::where('contact_id', $contact->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        $contact->update(['portal_token' => null]);
    }

    /**
     * Generate and email an OTP. Always silent on email mismatch.
     */
    public function issueOtp(Contact $contact, string $submittedEmail, Request $request): void
    {
        $submittedEmail = strtolower(trim($submittedEmail));

        if (!$this->emailMatchesContact($contact, $submittedEmail)) {
            $this->log($contact->id, 'otp_email_mismatch', $request, [
                'email_hash' => hash('sha256', $submittedEmail),
            ]);
            return;
        }

        if ($this->isContactLockedOut($contact)) {
            $this->log($contact->id, 'otp_blocked_lockout', $request);
            return;
        }

        ClientPortalOtp::where('contact_id', $contact->id)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        ClientPortalOtp::create([
            'contact_id' => $contact->id,
            'code_hash' => Hash::make($code),
            'email_hash' => hash('sha256', $submittedEmail),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            'ip_address' => $request->ip(),
        ]);

        Mail::to($submittedEmail)->queue(new PortalOtpMail(
            contact: $contact,
            code: $code,
            ttlMinutes: self::OTP_TTL_MINUTES,
        ));

        $this->log($contact->id, 'otp_sent', $request);
    }

    /**
     * Verify an OTP. On success returns true and the OTP is consumed.
     */
    public function verifyOtp(Contact $contact, string $submittedEmail, string $code, Request $request): bool
    {
        $submittedEmail = strtolower(trim($submittedEmail));
        $code = trim($code);

        if (!$this->emailMatchesContact($contact, $submittedEmail)) {
            $this->log($contact->id, 'otp_failed', $request, ['reason' => 'email_mismatch']);
            return false;
        }

        if ($this->isContactLockedOut($contact)) {
            $this->log($contact->id, 'otp_blocked_lockout', $request);
            return false;
        }

        $otp = ClientPortalOtp::where('contact_id', $contact->id)
            ->where('email_hash', hash('sha256', $submittedEmail))
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (!$otp) {
            $this->log($contact->id, 'otp_failed', $request, ['reason' => 'no_active_otp']);
            return false;
        }

        if ($otp->attempts >= self::OTP_MAX_ATTEMPTS) {
            $otp->update(['consumed_at' => now()]);
            $this->log($contact->id, 'otp_failed', $request, ['reason' => 'max_attempts']);
            return false;
        }

        $otp->increment('attempts');

        if (!Hash::check($code, $otp->code_hash)) {
            $this->log($contact->id, 'otp_failed', $request, ['reason' => 'wrong_code']);

            if ($this->countRecentFailures($contact) >= self::LOCKOUT_FAILED_THRESHOLD) {
                $this->log($contact->id, 'access_lockout', $request);
            }

            return false;
        }

        $otp->update(['consumed_at' => now()]);
        $this->log($contact->id, 'otp_success', $request);

        return true;
    }

    /**
     * Issue a trusted-device cookie. Returns the cookie object to attach
     * to the response.
     */
    public function issueTrustedDevice(Contact $contact, Request $request): \Symfony\Component\HttpFoundation\Cookie
    {
        $raw = bin2hex(random_bytes(32));

        ClientPortalTrustedDevice::create([
            'contact_id' => $contact->id,
            'cookie_hash' => $this->hashToken($raw),
            'user_agent_hash' => $this->hashUserAgent($request),
            'ip_address_first_seen' => $request->ip(),
            'last_used_at' => now(),
            'expires_at' => now()->addDays(self::COOKIE_LIFETIME_DAYS),
        ]);

        $this->log($contact->id, 'trusted_device_issued', $request);

        return Cookie::make(
            name: self::COOKIE_NAME,
            value: $raw,
            minutes: self::COOKIE_LIFETIME_DAYS * 24 * 60,
            path: '/portal',
            domain: null,
            secure: app()->isProduction(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );
    }

    /**
     * Validate the trusted-device cookie for a given contact.
     * Rotates the cookie on success: returns a new cookie to send back.
     */
    public function validateTrustedDevice(Contact $contact, Request $request): ?\Symfony\Component\HttpFoundation\Cookie
    {
        $raw = $request->cookie(self::COOKIE_NAME);
        if (!$raw || !is_string($raw)) {
            return null;
        }

        $row = ClientPortalTrustedDevice::where('contact_id', $contact->id)
            ->where('cookie_hash', $this->hashToken($raw))
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$row) {
            return null;
        }

        if ($row->user_agent_hash && $row->user_agent_hash !== $this->hashUserAgent($request)) {
            $row->update(['revoked_at' => now()]);
            $this->log($contact->id, 'trusted_device_ua_mismatch', $request);
            return null;
        }

        $newRaw = bin2hex(random_bytes(32));

        DB::transaction(function () use ($row, $newRaw, $request) {
            $row->update(['revoked_at' => now()]);
            ClientPortalTrustedDevice::create([
                'contact_id' => $row->contact_id,
                'cookie_hash' => $this->hashToken($newRaw),
                'user_agent_hash' => $this->hashUserAgent($request),
                'ip_address_first_seen' => $row->ip_address_first_seen,
                'last_used_at' => now(),
                'expires_at' => now()->addDays(self::COOKIE_LIFETIME_DAYS),
            ]);
        });

        $this->log($contact->id, 'trusted_device_used', $request);

        return Cookie::make(
            name: self::COOKIE_NAME,
            value: $newRaw,
            minutes: self::COOKIE_LIFETIME_DAYS * 24 * 60,
            path: '/portal',
            domain: null,
            secure: app()->isProduction(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );
    }

    /**
     * Revoke the current trusted device.
     */
    public function revokeTrustedDevice(Contact $contact, Request $request): void
    {
        $raw = $request->cookie(self::COOKIE_NAME);
        if ($raw && is_string($raw)) {
            ClientPortalTrustedDevice::where('contact_id', $contact->id)
                ->where('cookie_hash', $this->hashToken($raw))
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);
        }

        $this->log($contact->id, 'trusted_device_revoked', $request);
    }

    /**
     * Revoke all trusted devices for a contact (admin action).
     */
    public function revokeAllTrustedDevices(Contact $contact): void
    {
        ClientPortalTrustedDevice::where('contact_id', $contact->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    /**
     * Append a row to the access log.
     */
    public function log(?int $contactId, string $event, Request $request, array $metadata = []): void
    {
        ClientPortalAccessLog::create([
            'contact_id' => $contactId,
            'event' => $event,
            'ip_address' => $request->ip(),
            'user_agent_hash' => $this->hashUserAgent($request),
            'metadata' => $metadata ?: null,
            'created_at' => now(),
        ]);
    }

    private function isContactLockedOut(Contact $contact): bool
    {
        $lockout = ClientPortalAccessLog::where('contact_id', $contact->id)
            ->where('event', 'access_lockout')
            ->where('created_at', '>=', now()->subMinutes(self::LOCKOUT_DURATION_MINUTES))
            ->exists();

        return $lockout;
    }

    private function countRecentFailures(Contact $contact): int
    {
        return ClientPortalAccessLog::where('contact_id', $contact->id)
            ->where('event', 'otp_failed')
            ->where('created_at', '>=', now()->subMinutes(self::LOCKOUT_WINDOW_MINUTES))
            ->count();
    }

    private function emailMatchesContact(Contact $contact, string $submittedEmail): bool
    {
        return $contact->emails()
            ->whereRaw('LOWER(email) = ?', [$submittedEmail])
            ->exists();
    }

    public function hashToken(string $raw): string
    {
        return hash('sha256', $raw);
    }

    private function hashUserAgent(Request $request): string
    {
        return hash('sha256', (string) $request->userAgent());
    }

    /**
     * Purge expired/old portal-auth artifacts. Used by the daily scheduled command.
     */
    public function purgeExpired(): array
    {
        $now = now();
        $cutoffOtp = $now->copy()->subDay();
        $cutoffDevice = $now->copy()->subDays(30);
        $cutoffLog = $now->copy()->subMonths(12);

        $otps = ClientPortalOtp::where('expires_at', '<', $cutoffOtp)->delete();
        $devices = ClientPortalTrustedDevice::where('expires_at', '<', $cutoffDevice)->delete();
        $logs = ClientPortalAccessLog::where('created_at', '<', $cutoffLog)->delete();

        return [
            'otps' => $otps,
            'devices' => $devices,
            'logs' => $logs,
        ];
    }
}
