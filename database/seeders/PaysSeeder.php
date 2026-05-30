<?php

namespace Database\Seeders;

use App\Models\Pays;
use Illuminate\Database\Seeder;

class PaysSeeder extends Seeder
{
    /**
     * Reference list of countries with their ISO 3166-1 alpha-2 code, French
     * name and telephone calling code (indicatif). Idempotent via updateOrCreate
     * so it can be re-run without duplicating rows.
     */
    public function run(): void
    {
        $pays = [
            ['code' => 'BE', 'nom' => 'Belgique', 'indicatif' => '+32'],
            ['code' => 'FR', 'nom' => 'France', 'indicatif' => '+33'],
            ['code' => 'LU', 'nom' => 'Luxembourg', 'indicatif' => '+352'],
            ['code' => 'NL', 'nom' => 'Pays-Bas', 'indicatif' => '+31'],
            ['code' => 'DE', 'nom' => 'Allemagne', 'indicatif' => '+49'],
            ['code' => 'CH', 'nom' => 'Suisse', 'indicatif' => '+41'],
            ['code' => 'AT', 'nom' => 'Autriche', 'indicatif' => '+43'],
            ['code' => 'GB', 'nom' => 'Royaume-Uni', 'indicatif' => '+44'],
            ['code' => 'ES', 'nom' => 'Espagne', 'indicatif' => '+34'],
            ['code' => 'PT', 'nom' => 'Portugal', 'indicatif' => '+351'],
            ['code' => 'IT', 'nom' => 'Italie', 'indicatif' => '+39'],
            ['code' => 'PL', 'nom' => 'Pologne', 'indicatif' => '+48'],
            ['code' => 'US', 'nom' => 'États-Unis', 'indicatif' => '+1'],
            ['code' => 'CA', 'nom' => 'Canada', 'indicatif' => '+1'],
            ['code' => 'MA', 'nom' => 'Maroc', 'indicatif' => '+212'],
            ['code' => 'TN', 'nom' => 'Tunisie', 'indicatif' => '+216'],
            ['code' => 'DZ', 'nom' => 'Algérie', 'indicatif' => '+213'],
            ['code' => 'TR', 'nom' => 'Turquie', 'indicatif' => '+90'],
            ['code' => 'RU', 'nom' => 'Russie', 'indicatif' => '+7'],
        ];

        foreach ($pays as $p) {
            Pays::updateOrCreate(['code' => $p['code']], $p);
        }
    }
}
