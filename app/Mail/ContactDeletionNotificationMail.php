<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactDeletionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $contactEmail;

    public string $contactName;

    public string $adminName;

    public string $adminEmail;

    public string $deletedAtIso;

    public string $deletedAtHuman;

    public string $contactHash;

    public function __construct(Contact $contact, User $admin, string $contactEmail)
    {
        $this->contactEmail = $contactEmail;
        $this->contactName = trim(($contact->prenom ?? '').' '.($contact->nom ?? '')) ?: $contactEmail;
        $this->adminName = trim(($admin->prenom ?? '').' '.($admin->nom ?? '')) ?: $admin->email;
        $this->adminEmail = $admin->email;

        $deletedAt = now();
        $this->deletedAtIso = $deletedAt->toIso8601String();
        $this->deletedAtHuman = $deletedAt->format('Y-m-d H:i:s \U\T\C');

        $this->contactHash = hash('sha256', $contact->id.'|'.$contactEmail);
    }

    public function envelope(): Envelope
    {
        $bcc = [];
        if ($auditEmail = config('app.rgpd_audit_email')) {
            $bcc[] = $auditEmail;
        }

        return new Envelope(
            subject: __('Your data has been deleted — Pro Contact'),
            cc: [$this->adminEmail],
            bcc: $bcc,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-deletion-notification');
    }

    public function attachments(): array
    {
        return [];
    }
}
