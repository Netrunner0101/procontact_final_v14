<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class PortalOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact,
        public string $code,
        public int $ttlMinutes,
    ) {}

    public function envelope(): Envelope
    {
        $locale = App::getLocale() === 'en' ? 'en' : 'fr';

        $subject = $locale === 'en'
            ? 'Your ProContact access code'
            : 'Votre code d\'accès ProContact';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.portal-otp',
            with: [
                'contact' => $this->contact,
                'code' => $this->code,
                'ttlMinutes' => $this->ttlMinutes,
                'locale' => App::getLocale() === 'en' ? 'en' : 'fr',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
