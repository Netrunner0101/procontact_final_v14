<?php

namespace App\Mail;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RendezVousNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $rendezVous;

    public function __construct(RendezVous $rendezVous)
    {
        $this->rendezVous = $rendezVous;
    }

    public function build()
    {
        return $this->subject('Confirmation de rendez-vous - ' . $this->rendezVous->titre)
                    ->view('emails.rendez-vous-notification')
                    ->with([
                        'rendezVous' => $this->rendezVous,
                        'contact' => $this->rendezVous->contact,
                        'activite' => $this->rendezVous->activite,
                    ]);
    }
}
