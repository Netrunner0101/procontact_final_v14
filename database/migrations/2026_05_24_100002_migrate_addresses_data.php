<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $pays = [
            ['code' => 'FR', 'nom' => 'France'],
            ['code' => 'BE', 'nom' => 'Belgique'],
            ['code' => 'LU', 'nom' => 'Luxembourg'],
            ['code' => 'CH', 'nom' => 'Suisse'],
            ['code' => 'CA', 'nom' => 'Canada'],
            ['code' => 'DE', 'nom' => 'Allemagne'],
            ['code' => 'NL', 'nom' => 'Pays-Bas'],
            ['code' => 'ES', 'nom' => 'Espagne'],
            ['code' => 'IT', 'nom' => 'Italie'],
            ['code' => 'PT', 'nom' => 'Portugal'],
            ['code' => 'GB', 'nom' => 'Royaume-Uni'],
            ['code' => 'US', 'nom' => 'États-Unis'],
        ];

        foreach ($pays as $p) {
            DB::table('pays')->updateOrInsert(
                ['code' => $p['code']],
                $p + ['created_at' => $now, 'updated_at' => $now]
            );
        }

        // Map free-text country names → ISO codes. Anything unknown stays NULL.
        $countryMap = [
            'france' => 'FR', 'fr' => 'FR',
            'belgique' => 'BE', 'belgium' => 'BE', 'be' => 'BE',
            'luxembourg' => 'LU', 'lu' => 'LU',
            'suisse' => 'CH', 'switzerland' => 'CH', 'ch' => 'CH',
            'canada' => 'CA', 'ca' => 'CA',
            'allemagne' => 'DE', 'germany' => 'DE', 'de' => 'DE',
            'pays-bas' => 'NL', 'netherlands' => 'NL', 'nl' => 'NL',
            'espagne' => 'ES', 'spain' => 'ES', 'es' => 'ES',
            'italie' => 'IT', 'italy' => 'IT', 'it' => 'IT',
            'portugal' => 'PT', 'pt' => 'PT',
            'royaume-uni' => 'GB', 'united kingdom' => 'GB', 'uk' => 'GB', 'gb' => 'GB',
            'états-unis' => 'US', 'etats-unis' => 'US', 'united states' => 'US', 'usa' => 'US', 'us' => 'US',
        ];

        $resolveCode = function (?string $name) use ($countryMap): ?string {
            if ($name === null || trim($name) === '') {
                return null;
            }

            return $countryMap[mb_strtolower(trim($name))] ?? null;
        };

        // Copy user addresses
        if (Schema::hasColumn('users', 'rue')) {
            DB::table('users')->orderBy('id')->chunk(200, function ($users) use ($now, $resolveCode) {
                foreach ($users as $user) {
                    if (! $user->rue && ! $user->numero_rue && ! $user->ville && ! $user->code_postal && ! $user->pays) {
                        continue;
                    }

                    DB::table('adresses')->insert([
                        'addressable_type' => \App\Models\User::class,
                        'addressable_id' => $user->id,
                        'rue' => $user->rue,
                        'numero_rue' => $user->numero_rue,
                        'code_postal' => $user->code_postal,
                        'ville' => $user->ville,
                        'pays_code' => $resolveCode($user->pays),
                        'is_principale' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
        }

        // Copy contact addresses (contacts.numero → adresses.numero_rue)
        if (Schema::hasColumn('contacts', 'rue')) {
            DB::table('contacts')->orderBy('id')->chunk(200, function ($contacts) use ($now, $resolveCode) {
                foreach ($contacts as $contact) {
                    if (! $contact->rue && ! $contact->numero && ! $contact->ville && ! $contact->code_postal && ! $contact->pays) {
                        continue;
                    }

                    DB::table('adresses')->insert([
                        'addressable_type' => \App\Models\Contact::class,
                        'addressable_id' => $contact->id,
                        'rue' => $contact->rue,
                        'numero_rue' => $contact->numero,
                        'code_postal' => $contact->code_postal,
                        'ville' => $contact->ville,
                        'pays_code' => $resolveCode($contact->pays),
                        'is_principale' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        DB::table('adresses')->whereIn('addressable_type', [
            \App\Models\User::class,
            \App\Models\Contact::class,
        ])->delete();
    }
};
