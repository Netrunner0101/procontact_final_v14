<?php

namespace App\Services;

use App\Models\Note;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NoteService
{
    public function isSharedWithClient(Note $note): bool
    {
        return (bool) $note->is_shared_with_client;
    }

    public function getSharedNotes(RendezVous $rendezVous)
    {
        return $rendezVous->notes()->where('is_shared_with_client', true)->get();
    }

    public function createFromClient(RendezVous $rendezVous, string $message): Note
    {
        $validator = Validator::make(['message' => $message], [
            'message' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Note::create([
            'rendez_vous_id' => $rendezVous->id,
            'activite_id' => $rendezVous->activite_id,
            'user_id' => $rendezVous->user_id,
            'titre' => 'Message client',
            'commentaire' => $message,
            'is_shared_with_client' => false,
            'date_create' => now(),
            'date_update' => now(),
        ]);
    }
}
