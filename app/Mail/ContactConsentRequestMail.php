<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactConsentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contact $contact;

    public User $admin;

    public string $consentUrl;

    public string $version;

    public function __construct(Contact $contact, User $admin, ?string $version = null)
    {
        $this->contact = $contact;
        $this->admin = $admin;
        $this->version = $version ?? config('app.rgpd_version', '2026-05');
        $this->consentUrl = route('contact.consent.show', ['token' => $contact->gdpr_consent_token]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Action required: confirm how your data is processed — Pro Contact'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-consent-request');
    }

    public function attachments(): array
    {
        return [];
    }
}
