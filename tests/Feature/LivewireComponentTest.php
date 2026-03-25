<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;
use App\Models\Status;
use App\Models\RendezVous;
use App\Models\Activite;
use App\Models\Role;
use App\Livewire\ContactManager;
use App\Livewire\AppointmentManager;
use App\Livewire\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireComponentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $status;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['nom' => Role::ADMIN], ['description' => 'Administrator']);
        Role::firstOrCreate(['nom' => Role::CLIENT], ['description' => 'Client']);

        $this->user = User::factory()->create([
            'role_id' => $adminRole->id
        ]);

        $this->status = Status::factory()->create([
            'status_client' => 'Prospect'
        ]);
    }

    public function test_dashboard_component_renders_correctly()
    {
        $this->actingAs($this->user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertSee('Activit');
    }

    public function test_contact_manager_component_renders_correctly()
    {
        $this->actingAs($this->user);

        Livewire::test(ContactManager::class)
            ->assertStatus(200)
            ->assertSee('Gestion des Contacts')
            ->assertSee('Nouveau Contact');
    }

    public function test_contact_manager_can_create_contact()
    {
        $this->actingAs($this->user);

        Livewire::test(ContactManager::class)
            ->call('openCreateModal')
            ->set('nom', 'Dupont')
            ->set('prenom', 'Jean')
            ->set('email', 'jean.dupont@example.com')
            ->set('telephone', '0123456789')
            ->set('status_id', $this->status->id)
            ->call('createContact')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'user_id' => $this->user->id,
        ]);

        // Email is stored in the emails relationship table
        $this->assertDatabaseHas('emails', [
            'email' => 'jean.dupont@example.com',
        ]);
    }

    public function test_contact_manager_validates_required_fields()
    {
        $this->actingAs($this->user);

        Livewire::test(ContactManager::class)
            ->call('openCreateModal')
            ->call('createContact')
            ->assertHasErrors(['nom', 'prenom', 'status_id']);
    }

    public function test_contact_manager_can_update_contact()
    {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        Livewire::test(ContactManager::class)
            ->call('openEditModal', $contact->id)
            ->set('nom', 'Martin')
            ->set('prenom', 'Marie')
            ->call('updateContact')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'nom' => 'Martin',
            'prenom' => 'Marie',
        ]);
    }

    public function test_contact_manager_can_delete_contact()
    {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        Livewire::test(ContactManager::class)
            ->call('openDeleteModal', $contact->id)
            ->call('deleteContact')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_contact_manager_search_functionality()
    {
        $this->actingAs($this->user);

        $contact1 = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
            'nom' => 'Dupont',
            'prenom' => 'Jean',
        ]);

        $contact2 = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
            'nom' => 'Martin',
            'prenom' => 'Marie',
        ]);

        Livewire::test(ContactManager::class)
            ->set('search', 'Dupont')
            ->assertSee('Jean')
            ->assertDontSee('Marie');
    }

    public function test_appointment_manager_component_renders_correctly()
    {
        $this->actingAs($this->user);

        Livewire::test(AppointmentManager::class)
            ->assertStatus(200)
            ->assertSee('Gestion des Rendez-vous')
            ->assertSee('Nouveau RDV');
    }

    public function test_appointment_manager_can_create_appointment()
    {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $activite = Activite::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::test(AppointmentManager::class)
            ->call('openCreateModal')
            ->set('titre', 'Consultation test')
            ->set('description', 'Test description')
            ->set('date_debut', now()->addDay()->format('Y-m-d'))
            ->set('heure_debut', '09:00')
            ->set('heure_fin', '10:00')
            ->set('lieu', 'Test lieu')
            ->set('contact_id', $contact->id)
            ->set('activite_id', $activite->id)
            ->set('statut', 'Programmé')
            ->call('createAppointment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rendez_vous', [
            'titre' => 'Consultation test',
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_appointment_manager_validates_required_fields()
    {
        $this->actingAs($this->user);

        Livewire::test(AppointmentManager::class)
            ->call('openCreateModal')
            ->set('date_debut', '')
            ->set('heure_debut', '')
            ->call('createAppointment')
            ->assertHasErrors(['titre', 'date_debut', 'contact_id', 'activite_id']);
    }

    public function test_appointment_manager_filter_by_status()
    {
        $this->actingAs($this->user);

        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $activite = Activite::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $appointment1 = RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
            'statut' => 'planifie',
            'titre' => 'Appointment 1',
        ]);

        $appointment2 = RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
            'statut' => 'confirme',
            'titre' => 'Appointment 2',
        ]);

        Livewire::test(AppointmentManager::class)
            ->set('statusFilter', 'planifie')
            ->assertSee('Appointment 1')
            ->assertDontSee('Appointment 2');
    }

    public function test_components_only_show_user_data()
    {
        $otherUser = User::factory()->create();
        $otherStatus = Status::factory()->create();

        // Create data for other user
        $otherContact = Contact::factory()->create([
            'user_id' => $otherUser->id,
            'status_id' => $otherStatus->id,
            'nom' => 'Other User Contact',
        ]);

        // Create data for current user
        $myContact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
            'nom' => 'My Contact',
        ]);

        $this->actingAs($this->user);

        Livewire::test(ContactManager::class)
            ->assertSee('My Contact')
            ->assertDontSee('Other User Contact');
    }
}
