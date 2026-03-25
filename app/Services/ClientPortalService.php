<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\RendezVous;

class ClientPortalService
{
    public function getAppointments(Contact $contact)
    {
        return RendezVous::where('contact_id', $contact->id)
            ->with(['activite', 'notes'])
            ->orderBy('date_debut', 'desc')
            ->get();
    }
}
