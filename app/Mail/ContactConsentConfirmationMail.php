<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactConsentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contact $contact;

    public User $admin;

    public string $contactEmail;

    public string $contactName;

    public string $adminName;

    public bool $consent;

    public string $version;

    public string $decidedAtIso;

    public string $decidedAtHuman;

    public string $ip;

    public string $signature;

    public string $privacyUrl;

    public string $termsUrl;

    public string $cookiesUrl;

    public function __construct(Contact $contact, User $admin, bool $consent, string $contactEmail, ?string $ip = null)
    {
        $this->contact = $contact;
        $this->admin = $admin;
        $this->consent = $consent;
        $this->contactEmail = $contactEmail;
        $this->ip = $ip ?? '—';
        $this->contactName = trim(($contact->prenom ?? '').' '.($contact->nom ?? '')) ?: $contactEmail;
        $this->adminName = trim(($admin->prenom ?? '').' '.($admin->nom ?? '')) ?: $admin->email;
        $this->version = config('app.rgpd_version', '2026-05');

        $decidedAt = $consent
            ? ($contact->gdpr_consent_signed_at ?? now())
            : ($contact->gdpr_consent_declined_at ?? now());
        $this->decidedAtIso = $decidedAt->toIso8601String();
        $this->decidedAtHuman = $decidedAt->format('Y-m-d H:i:s \U\T\C');

        $this->privacyUrl = route('legal.privacy');
        $this->termsUrl = route('legal.terms');
        $this->cookiesUrl = route('legal.cookies');

        $this->signature = hash('sha256', implode('|', [
            'contact',
            $contact->id,
            $contactEmail,
            $admin->id,
            $admin->email,
            $this->decidedAtIso,
            $consent ? 'true' : 'false',
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
            subject: $this->consent
                ? __('Consent recorded — Pro Contact')
                : __('Refusal recorded — Pro Contact'),
            cc: [$this->admin->email],
            bcc: $bcc,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-consent-confirmation');
    }

    public function attachments(): array
    {
        return [];
    }
}
