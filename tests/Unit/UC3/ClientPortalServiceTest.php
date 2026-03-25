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
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * UC3 — Voir un Rendez-vous (Portail Client) : Tests Unitaires
 *
 * Précondition : Un rendez-vous existe. L'indépendant a envoyé le magic-link au client.
 * Postcondition : Le client peut consulter les détails du RDV, lire les notes partagées et laisser un message.
 */
class ClientPortalServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Contact $contact;
    protected Status $status;
    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Administrateur']);
        Role::firstOrCreate(['nom' => 'client'], ['description' => 'Client']);
        $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $this->status = Status::factory()->create();
        $this->contact = Contact::create([
            'user_id' => $this->admin->id,
            'nom' => 'Client',
            'prenom' => 'Test',
            'status_id' => $this->status->id,
            'portal_token' => 'valid-token-123',
        ]);
    }

    /**
     * U3.1 — Token valide → retourne le Contact
     * @test
     */
    public function it_returns_contact_for_valid_token(): void
    {
        $service = app(PortalTokenService::class);
        $result = $service->validate('valid-token-123');

        $this->assertEquals($this->contact->id, $result->id);
    }

    /**
     * U3.2 — Token inexistant → lève InvalidTokenException
     * @test
     */
    public function it_throws_for_invalid_token(): void
    {
        $this->expectException(InvalidTokenException::class);

        app(PortalTokenService::class)->validate('not-a-real-token');
    }

    /**
     * U3.3 — Contact associé au token supprimé → lève InvalidTokenException
     * @test
     */
    public function it_throws_when_contact_is_deleted(): void
    {
        $this->contact->delete();

        $this->expectException(InvalidTokenException::class);

        app(PortalTokenService::class)->validate('valid-token-123');
    }

    /**
     * U3.4 — Retourne les RDVs du contact
     * @test
     */
    public function it_returns_appointments_for_contact(): void
    {
        $activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Test Activity',
            'description' => 'Test',
        ]);

        RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $activite->id,
            'titre' => 'RDV 1',
            'description' => 'Test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        $service = app(ClientPortalService::class);
        $rdvs = $service->getAppointments($this->contact);

        $this->assertCount(1, $rdvs);
        $this->assertEquals('RDV 1', $rdvs->first()->titre);
    }

    /**
     * U3.5 — Aucun RDV → retourne collection vide (pas d'erreur 500)
     * @test
     */
    public function it_returns_empty_collection_when_no_appointments(): void
    {
        $service = app(ClientPortalService::class);
        $rdvs = $service->getAppointments($this->contact);

        $this->assertCount(0, $rdvs);
        $this->assertTrue($rdvs->isEmpty());
    }

    /**
     * U3.6 — Note avec flag true → incluse dans les notes partagées
     * @test
     */
    public function it_includes_shared_notes(): void
    {
        $activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Activity',
            'description' => 'Test',
        ]);
        $rdv = RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $activite->id,
            'titre' => 'RDV',
            'description' => 'Test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        Note::create([
            'rendez_vous_id' => $rdv->id,
            'activite_id' => $activite->id,
            'user_id' => $this->admin->id,
            'titre' => 'Shared Note',
            'commentaire' => 'Visible to client',
            'is_shared_with_client' => true,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        $service = app(NoteService::class);
        $notes = $service->getSharedNotes($rdv);

        $this->assertCount(1, $notes);
    }

    /**
     * U3.7 — Note avec flag false → exclue des notes partagées
     * @test
     */
    public function it_excludes_private_notes_from_client_view(): void
    {
        $activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Activity',
            'description' => 'Test',
        ]);
        $rdv = RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $activite->id,
            'titre' => 'RDV',
            'description' => 'Test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        Note::create([
            'rendez_vous_id' => $rdv->id,
            'activite_id' => $activite->id,
            'user_id' => $this->admin->id,
            'titre' => 'Private',
            'commentaire' => 'Not visible',
            'is_shared_with_client' => false,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        Note::create([
            'rendez_vous_id' => $rdv->id,
            'activite_id' => $activite->id,
            'user_id' => $this->admin->id,
            'titre' => 'Shared',
            'commentaire' => 'Visible',
            'is_shared_with_client' => true,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        $service = app(NoteService::class);
        $notes = $service->getSharedNotes($rdv);

        $this->assertCount(1, $notes);
        $this->assertEquals('Shared', $notes->first()->titre);
    }

    /**
     * U3.8 — Message client valide → note créée avec is_shared_with_client = false
     * @test
     */
    public function it_creates_client_note_as_private(): void
    {
        $activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Activity',
            'description' => 'Test',
        ]);
        $rdv = RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $activite->id,
            'titre' => 'RDV',
            'description' => 'Test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        $service = app(NoteService::class);
        $note = $service->createFromClient($rdv, 'Bonjour, je confirme.');

        $this->assertFalse($note->is_shared_with_client);
        $this->assertDatabaseHas('notes', [
            'rendez_vous_id' => $rdv->id,
            'is_shared_with_client' => false,
            'commentaire' => 'Bonjour, je confirme.',
        ]);
    }

    /**
     * U3.9 — Message client vide → ValidationException
     * @test
     */
    public function it_rejects_empty_client_message(): void
    {
        $activite = Activite::create([
            'user_id' => $this->admin->id,
            'nom' => 'Activity',
            'description' => 'Test',
        ]);
        $rdv = RendezVous::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $activite->id,
            'titre' => 'RDV',
            'description' => 'Test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        $this->expectException(ValidationException::class);

        app(NoteService::class)->createFromClient($rdv, '');
    }

    /**
     * U3.10 — Token révoqué → InvalidTokenException
     * @test
     */
    public function it_throws_for_revoked_token(): void
    {
        $service = app(PortalTokenService::class);
        $service->revoke($this->contact);

        $this->expectException(InvalidTokenException::class);

        $service->validate('valid-token-123');
    }
}
