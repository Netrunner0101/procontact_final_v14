<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RgpdAccountDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $email;

    public string $name;

    public string $deletedAtIso;

    public string $deletedAtHuman;

    public string $userHash;

    public function __construct(User $user)
    {
        $this->email = $user->email;
        $this->name = trim(($user->prenom ?? '').' '.($user->nom ?? '')) ?: $user->email;
        $deletedAt = now();
        $this->deletedAtIso = $deletedAt->toIso8601String();
        $this->deletedAtHuman = $deletedAt->format('Y-m-d H:i:s \U\T\C');
        $this->userHash = hash('sha256', $user->id.'|'.$user->email);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your Pro Contact account has been deleted'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.rgpd-account-deleted');
    }

    public function attachments(): array
    {
        return [];
    }
}
