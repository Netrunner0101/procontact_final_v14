<?php

namespace App\Jobs;

use App\Mail\RendezVousNotification;
use App\Models\RendezVous;
use App\Services\PortalTokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public RendezVous $rendezVous,
        public ?string $recipientEmail = null,
        public array $ccEmails = [],
    ) {}

    public function handle(PortalTokenService $tokenService): void
    {
        $contact = $this->rendezVous->contact()->with('emails')->first();

        // Generate portal token if not exists
        if (!$contact->portal_token) {
            $tokenService->generate($contact);
            $contact->refresh();
        }

        $email = $this->recipientEmail ?? $contact->emails->first()?->email;

        if (!$email) {
            Log::warning("No email address found for contact #{$contact->id} on appointment #{$this->rendezVous->id}");
            return;
        }

        $mail = Mail::to($email);

        if (!empty($this->ccEmails)) {
            $mail->cc($this->ccEmails);
        }

        // Send a copy to the appointment owner so they keep a record of every
        // notification their clients receive.
        $ownerEmail = $this->rendezVous->user?->email;
        if ($ownerEmail) {
            $alreadyAddressed = strcasecmp($ownerEmail, $email) === 0
                || collect($this->ccEmails)->contains(fn ($cc) => strcasecmp($cc, $ownerEmail) === 0);

            if (! $alreadyAddressed) {
                $mail->bcc($ownerEmail);
            }
        }

        $mail->send(new RendezVousNotification($this->rendezVous));
    }
}
