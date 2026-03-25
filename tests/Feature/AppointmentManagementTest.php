<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;
use App\Models\RendezVous;
use App\Models\Activite;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class AppointmentManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $contact;
    protected $activite;
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
        
        $this->contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);
        
        $this->activite = Activite::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_authenticated_user_can_view_appointments_index()
    {
        $response = $this->actingAs($this->user)->get('/rendez-vous');
        $response->assertStatus(200);
        $response->assertViewIs('rendez-vous.index');
    }

    public function test_authenticated_user_can_view_appointment_creation_form()
    {
        $response = $this->actingAs($this->user)->get('/rendez-vous/create');
        $response->assertStatus(200);
        $response->assertViewIs('rendez-vous.create');
    }

    public function test_authenticated_user_can_create_appointment()
    {
        $appointmentData = [
            'titre' => 'Consultation médicale',
            'description' => 'Consultation de routine',
            'date_heure' => Carbon::tomorrow()->format('Y-m-d H:i'),
            'duree' => 60,
            'lieu' => 'Cabinet médical',
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
            'statut' => 'planifie',
        ];

        $response = $this->actingAs($this->user)->post('/rendez-vous', $appointmentData);
        
        $response->assertRedirect('/rendez-vous');
        $this->assertDatabaseHas('rendez_vous', [
            'titre' => 'Consultation médicale',
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_appointment_creation_requires_required_fields()
    {
        $response = $this->actingAs($this->user)->post('/rendez-vous', []);
        
        $response->assertSessionHasErrors([
            'titre', 'date_heure', 'contact_id', 'activite_id'
        ]);
    }

    public function test_authenticated_user_can_view_appointment_details()
    {
        $appointment = RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
        ]);

        $response = $this->actingAs($this->user)->get("/rendez-vous/{$appointment->id}");
        $response->assertStatus(200);
        $response->assertViewIs('rendez-vous.show');
        $response->assertViewHas('rendezVous', $appointment);
    }

    public function test_authenticated_user_can_edit_own_appointment()
    {
        $appointment = RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
        ]);

        $response = $this->actingAs($this->user)->get("/rendez-vous/{$appointment->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('rendez-vous.edit');
    }

    public function test_authenticated_user_can_update_own_appointment()
    {
        $appointment = RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
        ]);

        $updateData = [
            'titre' => 'Consultation mise à jour',
            'description' => 'Description mise à jour',
            'date_heure' => Carbon::tomorrow()->addDay()->format('Y-m-d H:i'),
            'duree' => 90,
            'lieu' => 'Nouveau lieu',
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
            'statut' => 'confirme',
        ];

        $response = $this->actingAs($this->user)->put("/rendez-vous/{$appointment->id}", $updateData);
        
        $response->assertRedirect("/rendez-vous/{$appointment->id}");
        $this->assertDatabaseHas('rendez_vous', [
            'id' => $appointment->id,
            'titre' => 'Consultation mise à jour',
            'statut' => 'confirme',
        ]);
    }

    public function test_authenticated_user_can_delete_own_appointment()
    {
        $appointment = RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/rendez-vous/{$appointment->id}");
        
        $response->assertRedirect('/rendez-vous');
        $this->assertDatabaseMissing('rendez_vous', ['id' => $appointment->id]);
    }

    public function test_user_cannot_view_other_users_appointments()
    {
        $otherUser = User::factory()->create();
        $otherContact = Contact::factory()->create([
            'user_id' => $otherUser->id,
            'status_id' => $this->status->id,
        ]);
        $otherActivite = Activite::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $otherUser->id,
            'contact_id' => $otherContact->id,
            'activite_id' => $otherActivite->id,
        ]);

        $response = $this->actingAs($this->user)->get("/rendez-vous/{$appointment->id}");
        $response->assertStatus(403);
    }

    public function test_appointment_date_cannot_be_in_past()
    {
        $appointmentData = [
            'titre' => 'Consultation passée',
            'description' => 'Test',
            'date_heure' => Carbon::yesterday()->format('Y-m-d H:i'),
            'duree' => 60,
            'lieu' => 'Test',
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
            'statut' => 'planifie',
        ];

        $response = $this->actingAs($this->user)->post('/rendez-vous', $appointmentData);
        $response->assertSessionHasErrors('date_heure');
    }

    public function test_appointment_status_updates_correctly()
    {
        $appointment = RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
            'statut' => 'planifie',
        ]);

        $response = $this->actingAs($this->user)->patch("/rendez-vous/{$appointment->id}/status", [
            'statut' => 'confirme'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rendez_vous', [
            'id' => $appointment->id,
            'statut' => 'confirme',
        ]);
    }

    public function test_appointments_are_filtered_by_user()
    {
        $otherUser = User::factory()->create();
        $otherContact = Contact::factory()->create([
            'user_id' => $otherUser->id,
            'status_id' => $this->status->id,
        ]);
        $otherActivite = Activite::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        
        // Create appointments for current user
        RendezVous::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'contact_id' => $this->contact->id,
            'activite_id' => $this->activite->id,
        ]);
        
        // Create appointments for other user
        RendezVous::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'contact_id' => $otherContact->id,
            'activite_id' => $otherActivite->id,
        ]);

        $response = $this->actingAs($this->user)->get('/rendez-vous');
        $response->assertStatus(200);
        
        // Should only see own appointments (3)
        $appointments = $response->viewData('rendezVous');
        $this->assertCount(3, $appointments);
    }
}
