<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactManagementTest extends TestCase
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

    public function test_authenticated_user_can_view_contacts_index()
    {
        $response = $this->actingAs($this->user)->get('/contacts');
        $response->assertStatus(200);
        $response->assertViewIs('contacts.index');
    }

    public function test_authenticated_user_can_view_contact_creation_form()
    {
        $response = $this->actingAs($this->user)->get('/contacts/create');
        $response->assertStatus(200);
        $response->assertViewIs('contacts.create');
    }

    public function test_authenticated_user_can_create_contact()
    {
        $contactData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'telephone' => '0123456789',
            'adresse' => '123 Rue de la Paix',
            'ville' => 'Paris',
            'code_postal' => '75001',
            'pays' => 'France',
            'status_id' => $this->status->id,
            'notes' => 'Contact important',
        ];

        $response = $this->actingAs($this->user)->post('/contacts', $contactData);
        
        $response->assertRedirect('/contacts');
        $this->assertDatabaseHas('contacts', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_contact_creation_requires_required_fields()
    {
        $response = $this->actingAs($this->user)->post('/contacts', []);
        
        $response->assertSessionHasErrors(['nom', 'prenom', 'status_id']);
    }

    public function test_authenticated_user_can_view_contact_details()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)->get("/contacts/{$contact->id}");
        $response->assertStatus(200);
        $response->assertViewIs('contacts.show');
        $response->assertViewHas('contact', $contact);
    }

    public function test_authenticated_user_can_edit_own_contact()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)->get("/contacts/{$contact->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('contacts.edit');
    }

    public function test_authenticated_user_can_update_own_contact()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $updateData = [
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie.martin@example.com',
            'telephone' => '0987654321',
            'adresse' => '456 Avenue des Champs',
            'ville' => 'Lyon',
            'code_postal' => '69001',
            'pays' => 'France',
            'status_id' => $this->status->id,
            'notes' => 'Contact mis à jour',
        ];

        $response = $this->actingAs($this->user)->put("/contacts/{$contact->id}", $updateData);
        
        $response->assertRedirect("/contacts/{$contact->id}");
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie.martin@example.com',
        ]);
    }

    public function test_authenticated_user_can_delete_own_contact()
    {
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/contacts/{$contact->id}");
        
        $response->assertRedirect('/contacts');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_user_cannot_view_other_users_contacts()
    {
        $otherUser = User::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $otherUser->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)->get("/contacts/{$contact->id}");
        $response->assertStatus(403);
    }

    public function test_user_cannot_edit_other_users_contacts()
    {
        $otherUser = User::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $otherUser->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)->get("/contacts/{$contact->id}/edit");
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_contacts()
    {
        $response = $this->get('/contacts');
        $response->assertRedirect('/login');
    }

    public function test_contacts_are_filtered_by_user()
    {
        $otherUser = User::factory()->create();
        
        // Create contacts for current user
        Contact::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);
        
        // Create contacts for other user
        Contact::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)->get('/contacts');
        $response->assertStatus(200);
        
        // Should only see own contacts (3)
        $contacts = $response->viewData('contacts');
        $this->assertCount(3, $contacts);
    }
}
