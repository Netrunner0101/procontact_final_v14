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
            'adresses' => 'nullable|array',
            'adresses.*.rue' => 'nullable|string|max:255',
            'adresses.*.numero_rue' => 'nullable|string|max:20',
            'adresses.*.ville' => 'nullable|string|max:255',
            'adresses.*.code_postal' => 'nullable|string|max:20',
            'adresses.*.pays_code' => 'nullable|string|size:2|exists:pays,code',
            'adresses.*.is_principale' => 'nullable|boolean',
            'status_id' => 'nullable|exists:statuses,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Check for duplicate email for this user
        if (isset($data['email'])) {
            foreach ($data['email'] as $email) {
                $exists = Contact::where('user_id', $user->id)
                    ->whereHas('emails', fn ($q) => $q->where('email', $email))
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

            $this->syncAdresses($contact, $data['adresses'] ?? []);

            event(new ContactCreated($contact));

            return $contact;
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $adresses
     */
    public function syncAdresses(Contact $contact, array $adresses): void
    {
        app(AdresseSyncer::class)->sync($contact, $adresses);
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
