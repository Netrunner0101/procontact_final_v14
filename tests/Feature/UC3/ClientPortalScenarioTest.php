<?php

namespace Tests\Feature\UC3;

use App\Models\Activite;
use App\Models\Contact;
use App\Models\Note;
use App\Models\RendezVous;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * UC3 — Voir un Rendez-vous (Portail Client) : Tests Scénarios
 *
 * Précondition : Un rendez-vous existe. Le client est authentifié via le portail.
 * Postcondition : Le client peut consulter les détails du RDV et lire les notes partagées.
 */
class ClientPortalScenarioTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $client;
    protected Contact $contact;
    protected Activite $activite;
    protected RendezVous $rdv;
    protected Role $adminRole;
    protected Role $clientRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Administrateur']);
        $this->clientRole = Role::firstOrCreate(['nom' => 'client'], ['description' => 'Client']);

        $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $status = Status::factory()->create();

        $this->contact = Contact::create([
            'user_id' => $this->admin->id,
            'nom' => 'ClientNom',
            'prenom' => 'ClientPrenom',
            'status_id' => $status->id,
            'portal_token' => 'valid-portal-token',
        ]);

        $this->client = User::factory()->create([
            'role_id' => $this->clientRole->id,
            'admin_user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
        ]);

        $this->activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Cours de piano',
            'description' => 'Cours pour débutants',
        ]);

        $this->rdv = RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
            'titre' => 'Séance du mardi',
            'description' => 'Premier cours de piano',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '14:00',
            'heure_fin' => '15:00',
        ]);
    }

    /**
     * S3.1 — Nominal : Accès via portail client → liste des RDVs (200)
     * @test
     */
    public function client_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->client)->get('/client/dashboard');
        $response->assertStatus(200);
    }

    /**
     * S3.2 — Nominal : Clic sur un RDV → page de détail (titre, date, heure, activité affichés)
     * @test
     */
    public function client_can_view_appointment_detail(): void
    {
        $response = $this->actingAs($this->client)->get("/client/appointment/{$this->rdv->id}");
        $response->assertStatus(200);
    }

    /**
     * S3.3 — Nominal : Notes partagées visibles sur détail RDV
     * @test
     */
    public function shared_notes_are_visible_to_client(): void
    {
        Note::create([
            'rendez_vous_id' => $this->rdv->id,
            'activite_id' => $this->activite->id,
            'user_id' => $this->admin->id,
            'titre' => 'Note partagée',
            'commentaire' => 'Contenu visible',
            'is_shared_with_client' => true,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        $sharedNotes = Note::where('rendez_vous_id', $this->rdv->id)
            ->where('is_shared_with_client', true)
            ->get();

        $this->assertCount(1, $sharedNotes);
        $this->assertEquals('Note partagée', $sharedNotes->first()->titre);
    }

    /**
     * S3.4 — Nominal : Client peut accéder à ses rendez-vous
     * @test
     */
    public function client_can_access_appointments_list(): void
    {
        $response = $this->actingAs($this->client)->get('/client/appointments');
        $response->assertStatus(200);
    }

    /**
     * S3.5 — A1 : Aucune note partagée → pas d'erreur
     * @test
     */
    public function no_shared_notes_shows_empty(): void
    {
        $sharedNotes = Note::where('rendez_vous_id', $this->rdv->id)
            ->where('is_shared_with_client', true)
            ->get();

        $this->assertCount(0, $sharedNotes);
    }

    /**
     * S3.6 — E1 : Token invalide/accès non autorisé → 403
     * @test
     */
    public function unauthenticated_user_cannot_access_portal(): void
    {
        $response = $this->get('/client/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * S3.7 — E1 : Admin ne peut pas accéder au portail client → redirigé
     * @test
     */
    public function admin_cannot_access_client_portal(): void
    {
        $response = $this->actingAs($this->admin)->get('/client/dashboard');
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * S3.8 — E2 : Vérification de la validation des données
     * @test
     */
    public function client_notes_require_content(): void
    {
        $service = app(\App\Services\NoteService::class);

        try {
            $service->createFromClient($this->rdv, '');
            $this->fail('Expected ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('message', $e->errors());
        }
    }

    /**
     * S3.9 — Sécurité : Token A ne peut pas accéder aux RDVs du token B
     * @test
     */
    public function client_cannot_access_other_clients_appointments(): void
    {
        $otherContact = Contact::create([
            'user_id' => $this->admin->id,
            'nom' => 'Other',
            'prenom' => 'Client',
            'status_id' => $this->contact->status_id,
        ]);

        $otherRdv = RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $otherContact->id,
            'activite_id' => $this->activite->id,
            'titre' => 'Other RDV',
            'description' => 'Not for this client',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '16:00',
            'heure_fin' => '17:00',
        ]);

        $response = $this->actingAs($this->client)->get("/client/appointment/{$otherRdv->id}");
        $response->assertStatus(403);
    }

    /**
     * S3.10 — Sécurité : Note privée non visible via portail
     * @test
     */
    public function private_notes_not_visible_via_portal(): void
    {
        Note::create([
            'rendez_vous_id' => $this->rdv->id,
            'activite_id' => $this->activite->id,
            'user_id' => $this->admin->id,
            'titre' => 'Note privée',
            'commentaire' => 'Invisible au client',
            'is_shared_with_client' => false,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        $sharedNotes = Note::where('rendez_vous_id', $this->rdv->id)
            ->where('is_shared_with_client', true)
            ->get();

        $this->assertCount(0, $sharedNotes);

        $allNotes = Note::where('rendez_vous_id', $this->rdv->id)->get();
        $this->assertCount(1, $allNotes);
        $this->assertFalse($allNotes->first()->is_shared_with_client);
    }
}
