<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class AdresseSyncer
{
    /**
     * Replace the addresses of any addressable model (User or Contact) with the provided
     * payload, dropping empty rows. Guarantees exactly one is_principale = true when at
     * least one address is kept.
     *
     * @param  array<int, array<string, mixed>>  $adresses
     */
    public function sync(Model $owner, array $adresses): void
    {
        $owner->adresses()->delete();

        $kept = [];
        foreach ($adresses as $row) {
            $rue = $row['rue'] ?? null;
            $numero = $row['numero_rue'] ?? null;
            $cp = $row['code_postal'] ?? null;
            $ville = $row['ville'] ?? null;
            $pays = $row['pays_code'] ?? null;

            if (blank($rue) && blank($numero) && blank($cp) && blank($ville) && blank($pays)) {
                continue;
            }

            $kept[] = [
                'rue' => $rue,
                'numero_rue' => $numero,
                'code_postal' => $cp,
                'ville' => $ville,
                'pays_code' => $pays ?: null,
                'is_principale' => (bool) ($row['is_principale'] ?? false),
            ];
        }

        if (empty($kept)) {
            return;
        }

        $hasPrincipal = false;
        foreach ($kept as $i => $row) {
            if ($row['is_principale'] && ! $hasPrincipal) {
                $hasPrincipal = true;
            } else {
                $kept[$i]['is_principale'] = false;
            }
        }
        if (! $hasPrincipal) {
            $kept[0]['is_principale'] = true;
        }

        foreach ($kept as $row) {
            $owner->adresses()->create($row);
        }
    }
}
