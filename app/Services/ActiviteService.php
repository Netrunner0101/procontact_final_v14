<?php

namespace App\Services;

use App\Events\ActiviteCreated;
use App\Models\Activite;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ActiviteService
{
    public function create(User $user, array $data): Activite
    {
        $validator = Validator::make($data, [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'email' => 'nullable|email|max:255',
            'numero_telephone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Check duplicate name for user
        $exists = Activite::where('user_id', $user->id)
            ->where('nom', $data['nom'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nom' => ["Une activité avec ce nom existe déjà."],
            ]);
        }

        $createData = [
            'user_id' => $user->id,
            'nom' => $data['nom'],
            'description' => $data['description'],
            'email' => $data['email'] ?? null,
            'numero_telephone' => $data['numero_telephone'] ?? null,
        ];

        if (isset($data['image']) && $data['image']) {
            $createData['image'] = $data['image']->store('activites', 'public');
        }

        $activite = Activite::create($createData);

        event(new ActiviteCreated($activite));

        return $activite;
    }

    public function findByUser(User $user)
    {
        return Activite::where('user_id', $user->id)->get();
    }
}
