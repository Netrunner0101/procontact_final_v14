<?php

namespace App\Services;

use App\Events\ContactCreated;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContactService
{
    public function create(User $user, array $data): Contact
    {
        $validator = Validator::make($data, [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|array|min:1',
            'email.*' => 'required|email|max:255',
            'telephone' => 'nullable|array',
            'telephone.*' => 'nullable|string|regex:/^[\+0-9\s\-\(\)]+$/',
            'rue' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:255',
            'status_id' => 'nullable|exists:statuses,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Check for duplicate email for this user
        if (isset($data['email'])) {
            foreach ($data['email'] as $email) {
                $exists = Contact::where('user_id', $user->id)
                    ->whereHas('emails', fn($q) => $q->where('email', $email))
                    ->exists();
                if ($exists) {
                    throw ValidationException::withMessages([
                        'email' => ["L'adresse email {$email} existe déjà pour un de vos contacts."],
                    ]);
                }
            }
        }

        return DB::transaction(function () use ($user, $data) {
            $contact = Contact::create([
                'user_id' => $user->id,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'rue' => $data['rue'] ?? null,
                'numero' => $data['numero'] ?? null,
                'ville' => $data['ville'] ?? null,
                'code_postal' => $data['code_postal'] ?? null,
                'pays' => $data['pays'] ?? null,
                'status_id' => $data['status_id'] ?? null,
            ]);

            // Save emails
            if (isset($data['email'])) {
                foreach ($data['email'] as $email) {
                    $contact->emails()->create(['email' => $email]);
                }
            }

            // Save phone numbers
            if (isset($data['telephone'])) {
                foreach ($data['telephone'] as $phone) {
                    $contact->numeroTelephones()->create(['numero_telephone' => $phone]);
                }
            }

            event(new ContactCreated($contact));

            return $contact;
        });
    }

    public function addPhone(Contact $contact, string $phone): void
    {
        $contact->numeroTelephones()->create(['numero_telephone' => $phone]);
    }

    public function addEmail(Contact $contact, string $email): void
    {
        $contact->emails()->create(['email' => $email]);
    }

    public function findByUser(User $user)
    {
        return Contact::where('user_id', $user->id)->get();
    }
}
