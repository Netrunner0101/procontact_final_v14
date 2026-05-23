<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RgpdConsentMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $acceptedAtIso;

    public string $acceptedAtHuman;

    public string $version;

    public bool $consent;

    public string $privacyUrl;

    public string $termsUrl;

    public string $cookiesUrl;

    public string $signature;

    public function __construct(User $user, bool $consent = true, ?string $version = null)
    {
        $this->user = $user;
        $this->consent = $consent;
        $this->version = $version ?? config('app.rgpd_version', '2026-05');

        $accepted = $user->terms_accepted_at ?? now();
        $this->acceptedAtIso = $accepted->toIso8601String();
        $this->acceptedAtHuman = $accepted->format('Y-m-d H:i:s \U\T\C');

        $this->privacyUrl = route('legal.privacy');
        $this->termsUrl = route('legal.terms');
        $this->cookiesUrl = route('legal.cookies');

        $this->signature = hash('sha256', implode('|', [
            $user->id,
            $user->email,
            $this->acceptedAtIso,
            $this->consent ? 'true' : 'false',
            $this->version,
            config('app.key'),
        ]));
    }

    public function envelope(): Envelope
    {
        $bcc = [];
        if ($auditEmail = config('app.rgpd_audit_email')) {
            $bcc[] = $auditEmail;
        }

        return new Envelope(
            subject: __('Your GDPR consent record — Pro Contact'),
            bcc: $bcc,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.rgpd-consent');
    }

    public function attachments(): array
    {
        return [];
    }
}
