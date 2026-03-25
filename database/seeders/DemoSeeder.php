<?php

namespace Database\Seeders;

use App\Models\Activite;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Note;
use App\Models\NumeroTelephone;
use App\Models\Rappel;
use App\Models\RendezVous;
use App\Models\Status;
use App\Models\User;
use App\Services\PortalTokenService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed statuses
        $this->call(StatusSeeder::class);

        // 2. Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@procontact.test'],
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'admin@procontact.test',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'provider' => 'email',
            ]
        );

        // 3. Create activities
        $coaching = Activite::create([
            'user_id' => $admin->id,
            'nom' => 'Coaching Personnel',
            'description' => 'Séances de coaching individuel pour le développement personnel et professionnel.',
            'email' => 'coaching@procontact.test',
            'numero_telephone' => '+32 2 123 45 67',
        ]);

        $consultation = Activite::create([
            'user_id' => $admin->id,
            'nom' => 'Consultation Médicale',
            'description' => 'Consultations médicales générales et spécialisées.',
            'email' => 'medical@procontact.test',
            'numero_telephone' => '+32 2 987 65 43',
        ]);

        $formation = Activite::create([
            'user_id' => $admin->id,
            'nom' => 'Formation Professionnelle',
            'description' => 'Formations en développement web, design et management.',
        ]);

        // 4. Create contacts with emails and phones
        $contacts = [];

        // Contact 1 — Marie Martin
        $marie = Contact::create([
            'user_id' => $admin->id,
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'rue' => 'Rue de la Loi',
            'numero' => '42',
            'ville' => 'Bruxelles',
            'code_postal' => '1000',
            'pays' => 'Belgique',
            'status_id' => Status::where('status_client', 'Client actif')->first()?->id,
        ]);
        Email::create(['contact_id' => $marie->id, 'user_id' => $admin->id, 'email' => 'marie.martin@example.com']);
        NumeroTelephone::create(['contact_id' => $marie->id, 'user_id' => $admin->id, 'numero_telephone' => '+32 475 12 34 56']);
        $marie->activites()->attach([$coaching->id, $consultation->id]);
        $contacts[] = $marie;

        // Contact 2 — Pierre Lefebvre
        $pierre = Contact::create([
            'user_id' => $admin->id,
            'nom' => 'Lefebvre',
            'prenom' => 'Pierre',
            'rue' => 'Avenue Louise',
            'numero' => '105',
            'ville' => 'Bruxelles',
            'code_postal' => '1050',
            'pays' => 'Belgique',
            'status_id' => Status::where('status_client', 'Prospect')->first()?->id,
        ]);
        Email::create(['contact_id' => $pierre->id, 'user_id' => $admin->id, 'email' => 'pierre.lefebvre@example.com']);
        Email::create(['contact_id' => $pierre->id, 'user_id' => $admin->id, 'email' => 'p.lefebvre@work.com']);
        NumeroTelephone::create(['contact_id' => $pierre->id, 'user_id' => $admin->id, 'numero_telephone' => '+32 476 98 76 54']);
        NumeroTelephone::create(['contact_id' => $pierre->id, 'user_id' => $admin->id, 'numero_telephone' => '+32 2 555 00 11']);
        $pierre->activites()->attach([$formation->id]);
        $contacts[] = $pierre;

        // Contact 3 — Sophie Dubois
        $sophie = Contact::create([
            'user_id' => $admin->id,
            'nom' => 'Dubois',
            'prenom' => 'Sophie',
            'ville' => 'Liège',
            'code_postal' => '4000',
            'pays' => 'Belgique',
            'status_id' => Status::where('status_client', 'Lead qualifié')->first()?->id,
        ]);
        Email::create(['contact_id' => $sophie->id, 'user_id' => $admin->id, 'email' => 'sophie.dubois@example.com']);
        NumeroTelephone::create(['contact_id' => $sophie->id, 'user_id' => $admin->id, 'numero_telephone' => '+32 477 11 22 33']);
        $sophie->activites()->attach([$coaching->id]);
        $contacts[] = $sophie;

        // 5. Create appointments
        $rdv1 = RendezVous::create([
            'user_id' => $admin->id,
            'contact_id' => $marie->id,
            'activite_id' => $coaching->id,
            'titre' => 'Séance de coaching — Marie',
            'description' => 'Première séance de coaching pour le développement personnel.',
            'date_debut' => now()->addDays(3)->format('Y-m-d'),
            'date_fin' => now()->addDays(3)->format('Y-m-d'),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        $rdv2 = RendezVous::create([
            'user_id' => $admin->id,
            'contact_id' => $marie->id,
            'activite_id' => $consultation->id,
            'titre' => 'Consultation médicale — Marie',
            'description' => 'Bilan de santé annuel.',
            'date_debut' => now()->addDays(7)->format('Y-m-d'),
            'date_fin' => now()->addDays(7)->format('Y-m-d'),
            'heure_debut' => '14:00',
            'heure_fin' => '14:30',
        ]);

        $rdv3 = RendezVous::create([
            'user_id' => $admin->id,
            'contact_id' => $sophie->id,
            'activite_id' => $coaching->id,
            'titre' => 'Coaching initial — Sophie',
            'description' => 'Évaluation initiale et définition des objectifs.',
            'date_debut' => now()->addDays(5)->format('Y-m-d'),
            'date_fin' => now()->addDays(5)->format('Y-m-d'),
            'heure_debut' => '09:00',
            'heure_fin' => '10:30',
        ]);

        // Past appointment
        $rdv4 = RendezVous::create([
            'user_id' => $admin->id,
            'contact_id' => $pierre->id,
            'activite_id' => $formation->id,
            'titre' => 'Formation Laravel — Pierre',
            'description' => 'Introduction à Laravel et Livewire.',
            'date_debut' => now()->subDays(5)->format('Y-m-d'),
            'date_fin' => now()->subDays(5)->format('Y-m-d'),
            'heure_debut' => '13:00',
            'heure_fin' => '17:00',
        ]);

        // 6. Create notes (some shared, some not)
        Note::create([
            'user_id' => $admin->id,
            'rendez_vous_id' => $rdv1->id,
            'titre' => 'Objectifs de coaching',
            'commentaire' => 'Marie souhaite travailler sur la gestion du stress et la prise de décision professionnelle.',
            'is_shared_with_client' => true,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        Note::create([
            'user_id' => $admin->id,
            'rendez_vous_id' => $rdv1->id,
            'titre' => 'Note interne',
            'commentaire' => 'Contacter Marie la veille pour confirmer la séance.',
            'is_shared_with_client' => false,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        Note::create([
            'user_id' => $admin->id,
            'rendez_vous_id' => $rdv3->id,
            'titre' => 'Bienvenue Sophie',
            'commentaire' => 'Nous sommes ravis de vous accueillir. Préparez une liste de vos objectifs.',
            'is_shared_with_client' => true,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        // 7. Create reminders
        Rappel::create([
            'user_id' => $admin->id,
            'rendez_vous_id' => $rdv1->id,
            'date_rappel' => now()->addDays(2)->setTime(9, 0),
            'frequence' => 'Une fois',
        ]);

        Rappel::create([
            'user_id' => $admin->id,
            'rendez_vous_id' => $rdv3->id,
            'date_rappel' => now()->addDays(4)->setTime(8, 0),
            'frequence' => 'Une fois',
        ]);

        // 8. Generate portal tokens for contacts with appointments
        $tokenService = new PortalTokenService();
        $tokenService->generate($marie);
        $tokenService->generate($sophie);

        // Reload to get tokens
        $marie->refresh();
        $sophie->refresh();

        $this->command->info('');
        $this->command->info('Demo data created successfully!');
        $this->command->info('');
        $this->command->info('Admin login: admin@procontact.test / password');
        $this->command->info('');
        $this->command->info('Portal links (magic-link):');
        $this->command->info('  Marie Martin: /portal/' . $marie->portal_token);
        $this->command->info('  Sophie Dubois: /portal/' . $sophie->portal_token);
    }
}
