<?php

namespace App\Observers;

use App\Models\Adresse;

class AdresseObserver
{
    public function saving(Adresse $adresse): void
    {
        if (! $adresse->is_principale) {
            return;
        }

        if (! $adresse->addressable_type || ! $adresse->addressable_id) {
            return;
        }

        Adresse::query()
            ->where('addressable_type', $adresse->addressable_type)
            ->where('addressable_id', $adresse->addressable_id)
            ->when($adresse->exists, fn ($q) => $q->where('id', '!=', $adresse->id))
            ->update(['is_principale' => false]);
    }
}
