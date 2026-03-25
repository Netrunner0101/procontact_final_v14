<?php

namespace Tests\Feature\UC1;

use App\Models\Contact;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * UC1 — Créer un Contact : Tests Scénarios
 *
 * S1.1 Nominal — Création complète et redirection vers la liste
 * S1.2 A1 — Ajout dynamique de plusieurs téléphones/emails
 * S1.3 A2 — Annuler la création → retour liste sans données
 * S1.4 E1 — Champs obligatoires manquants → messages en rouge
 * S1.5 E2 — Email invalide → message d'erreur
 */
class ContactScenarioTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Status $status;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Administrateur']);
        $this->user = User::factory()->create(['role_id' => $adminRole->id]);
        $this->status = Status::factory()->create();
    }

    /**
     * S1.1 — Nominal : Création complète et redirection vers la liste avec message de succès
     * @test
     */
    public function nominal_creation_redirects_to_list_with_success(): void
    {
        $response = $this->actingAs($this->user)->post('/contacts', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'emails' => ['jean@example.com'],
            'phones' => ['+32477000000'],
            'status_id' => $this->status->id,
        ]);

        $response->assertRedirect(route('contacts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contacts', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * S1.2 — A1 : Ajout dynamique de plusieurs téléphones/emails
     * @test
     */
    public function creation_with_multiple_phones_and_emails(): void
    {
        $response = $this->actingAs($this->user)->post('/contacts', [
            'nom' => 'Multi',
            'prenom' => 'Contact',
            'emails' => ['first@example.com', 'second@example.com'],
            'phones' => ['+32477000001', '+32477000002'],
            'status_id' => $this->status->id,
        ]);

        $response->assertRedirect(route('contacts.index'));

        $contact = Contact::where('nom', 'Multi')->first();
        $this->assertNotNull($contact);
        $this->assertCount(2, $contact->emails);
        $this->assertCount(2, $contact->numeroTelephones);
    }

    /**
     * S1.3 — A2 : Annuler la création → retour liste sans données en DB
     * @test
     */
    public function cancel_creation_returns_to_list_without_data(): void
    {
        // Simply accessing the create page and going back to index
        $response = $this->actingAs($this->user)->get('/contacts/create');
        $response->assertStatus(200);

        // Navigate back without posting
        $response = $this->actingAs($this->user)->get('/contacts');
        $response->assertStatus(200);

        // No new contacts created
        $this->assertEquals(0, Contact::where('user_id', $this->user->id)->count());
    }

    /**
     * S1.4 — E1 : Champs obligatoires manquants → messages de validation affichés
     * @test
     */
    public function missing_required_fields_show_validation_errors(): void
    {
        $response = $this->actingAs($this->user)->post('/contacts', []);

        $response->assertSessionHasErrors(['nom', 'prenom', 'emails', 'phones']);
        $this->assertDatabaseMissing('contacts', ['user_id' => $this->user->id]);
    }

    /**
     * S1.5 — E2 : Email invalide → message d'erreur
     * @test
     */
    public function invalid_email_shows_validation_error(): void
    {
        $response = $this->actingAs($this->user)->post('/contacts', [
            'nom' => 'Test',
            'prenom' => 'User',
            'emails' => ['not-valid-email'],
            'phones' => ['+32477000000'],
            'status_id' => $this->status->id,
        ]);

        $response->assertSessionHasErrors(['emails.0']);
        $this->assertDatabaseMissing('contacts', ['nom' => 'Test']);
    }
}
