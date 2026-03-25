<?php

namespace App\Policies;

use App\Models\RendezVous;
use App\Models\User;

class RendezVousPolicy
{
    public function view(User $user, RendezVous $rendezVous): bool
    {
        return $user->id === $rendezVous->user_id;
    }

    public function update(User $user, RendezVous $rendezVous): bool
    {
        return $user->id === $rendezVous->user_id;
    }

    public function delete(User $user, RendezVous $rendezVous): bool
    {
        return $user->id === $rendezVous->user_id;
    }
}
