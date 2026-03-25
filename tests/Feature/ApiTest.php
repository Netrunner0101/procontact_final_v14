<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;
use App\Models\RendezVous;
use App\Models\Status;
use App\Models\Activite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $status;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role_id' => 1
        ]);
        
        $this->status = Status::factory()->create([
            'status_client' => 'Prospect'
        ]);
    }

    public function test_api_requires_authentication()
    {
        $response = $this->getJson('/api/contacts');
        $response->assertStatus(401);
    }

    public function test_api_contacts_endpoint_returns_user_contacts()
    {
        // Create contacts for current user
        $contacts = Contact::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        // Create contacts for other user
        $otherUser = User::factory()->create();
        Contact::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/contacts');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_api_can_create_contact()
    {
        $contactData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'telephone' => '0123456789',
            'status_id' => $this->status->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/contacts', $contactData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'nom' => 'Dupont',
                     'prenom' => 'Jean',
                     'email' => 'jean.dupont@example.com',
                 ]);

        $this->assertDatabaseHas('contacts', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_api_can_update_contact()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $updateData = [
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie.martin@example.com',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/contacts/{$contact->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'nom' => 'Martin',
                     'prenom' => 'Marie',
                 ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'nom' => 'Martin',
            'prenom' => 'Marie',
        ]);
    }

    public function test_api_can_delete_contact()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_api_appointments_endpoint_returns_user_appointments()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $activite = Activite::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create appointments for current user
        $appointments = RendezVous::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/rendez-vous');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_api_can_create_appointment()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $activite = Activite::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $appointmentData = [
            'titre' => 'Consultation API',
            'description' => 'Test via API',
            'date_heure' => now()->addDay()->format('Y-m-d H:i:s'),
            'duree' => 60,
            'lieu' => 'Cabinet test',
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
            'statut' => 'planifie',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/rendez-vous', $appointmentData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'titre' => 'Consultation API',
                     'description' => 'Test via API',
                 ]);

        $this->assertDatabaseHas('rendez_vous', [
            'titre' => 'Consultation API',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_api_validates_appointment_data()
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/rendez-vous', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'titre', 'date_heure', 'contact_id', 'activite_id'
                 ]);
    }

    public function test_api_cannot_access_other_user_data()
    {
        $otherUser = User::factory()->create();
        $otherContact = Contact::factory()->create([
            'user_id' => $otherUser->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson("/api/contacts/{$otherContact->id}");

        $response->assertStatus(403);
    }

    public function test_api_statistics_endpoint()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $activite = Activite::factory()->create([
            'user_id' => $this->user->id,
        ]);

        RendezVous::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/statistics');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'contacts_count',
                     'appointments_count',
                     'activities_count',
                     'monthly_stats',
                 ]);
    }

    public function test_api_export_functionality()
    {
        Contact::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/export/contacts');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
