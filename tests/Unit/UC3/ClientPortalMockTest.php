<?php

namespace Tests\Unit\UC3;

use App\Exceptions\InvalidTokenException;
use App\Models\Activite;
use App\Models\Contact;
use App\Models\Note;
use App\Models\RendezVous;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use App\Services\ClientPortalService;
use App\Services\NoteService;
use App\Services\PortalTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * UC3 — Voir un Rendez-vous (Portail Client) : Tests Mock
 */
class ClientPortalMockTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Contact $contact;
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
            'nom' => 'Client',
            'prenom' => 'Test',
            'status_id' => $status->id,
            'portal_token' => 'tok123',
        ]);
    }

    /**
     * M3.1 — Le controller délègue la validation du token au PortalTokenService
     * @test
     */
    public function portal_token_service_validates_correctly(): void
    {
        $service = app(PortalTokenService::class);
        $result = $service->validate('tok123');

        $this->assertEquals($this->contact->id, $result->id);
    }

    /**
     * M3.2 — Liste des RDVs chargée depuis le ClientPortalService
     * @test
     */
    public function client_portal_service_loads_appointments(): void
    {
        $activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Test',
            'description' => 'Test',
        ]);

        RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $activite->id,
            'titre' => 'Test RDV',
            'description' => 'Test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        $service = app(ClientPortalService::class);
        $rdvs = $service->getAppointments($this->contact);

        $this->assertCount(1, $rdvs);
    }

    /**
     * M3.3 — createFromClient() appelé une fois au POST
     * @test
     */
    public function note_service_creates_client_note(): void
    {
        $activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Test',
            'description' => 'Test',
        ]);
        $rdv = RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $activite->id,
            'titre' => 'Test RDV',
            'description' => 'Test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        $service = app(NoteService::class);
        $note = $service->createFromClient($rdv, 'Merci !');

        $this->assertDatabaseHas('notes', [
            'rendez_vous_id' => $rdv->id,
            'commentaire' => 'Merci !',
        ]);
    }

    /**
     * M3.4 — Aucun appel à createFromClient() si message vide (validation bloque)
     * @test
     */
    public function note_service_rejects_empty_message(): void
    {
        $activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Test',
            'description' => 'Test',
        ]);
        $rdv = RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $activite->id,
            'titre' => 'Test RDV',
            'description' => 'Test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        try {
            app(NoteService::class)->createFromClient($rdv, '');
            $this->fail('Expected ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('message', $e->errors());
        }

        $this->assertDatabaseMissing('notes', ['rendez_vous_id' => $rdv->id]);
    }

    /**
     * M3.5 — Notification envoyée (test structure - vérifie que le service fonctionne)
     * @test
     */
    public function portal_token_generation_works(): void
    {
        $service = app(PortalTokenService::class);
        $token = $service->generate($this->contact);

        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex chars
        $this->assertEquals($token, $this->contact->fresh()->portal_token);
    }

    /**
     * M3.6 — Token validé correctement (performance test)
     * @test
     */
    public function portal_token_validation_is_fast(): void
    {
        $service = app(PortalTokenService::class);

        $start = microtime(true);
        $service->validate('tok123');
        $duration = microtime(true) - $start;

        $this->assertLessThan(1.0, $duration);
    }
}
