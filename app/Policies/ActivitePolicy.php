<?php

namespace App\Policies;

use App\Models\Activite;
use App\Models\User;

class ActivitePolicy
{
    public function view(User $user, Activite $activite): bool
    {
        return $user->id === $activite->user_id;
    }

    public function update(User $user, Activite $activite): bool
    {
        return $user->id === $activite->user_id;
    }

    public function delete(User $user, Activite $activite): bool
    {
        return $user->id === $activite->user_id;
    }
}
