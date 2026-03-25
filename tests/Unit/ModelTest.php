<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Contact;
use App\Models\RendezVous;
use App\Models\Activite;
use App\Models\Status;
use App\Models\Note;
use App\Models\Rappel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_has_correct_fillable_attributes()
    {
        $user = new User();
        
        $expectedFillable = [
            'nom', 'prenom', 'email', 'password', 'telephone', 
            'adresse', 'ville', 'code_postal', 'pays', 'role',
            'admin_user_id', 'google_id', 'apple_id', 'provider', 'avatar'
        ];
        
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    public function test_user_has_contacts_relationship()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $this->assertTrue($user->contacts->contains($contact));
        $this->assertInstanceOf(Contact::class, $user->contacts->first());
    }

    public function test_user_has_appointments_relationship()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $this->assertTrue($user->rendezVous->contains($appointment));
        $this->assertInstanceOf(RendezVous::class, $user->rendezVous->first());
    }

    public function test_user_role_helper_methods()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $client = User::factory()->create(['role_id' => 2]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isClient());
        
        $this->assertTrue($client->isClient());
        $this->assertFalse($client->isAdmin());
    }

    public function test_contact_belongs_to_user()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $this->assertEquals($user->id, $contact->user->id);
        $this->assertInstanceOf(User::class, $contact->user);
    }

    public function test_contact_belongs_to_status()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $this->assertEquals($status->id, $contact->status->id);
        $this->assertInstanceOf(Status::class, $contact->status);
    }

    public function test_contact_has_appointments_relationship()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $this->assertTrue($contact->rendezVous->contains($appointment));
    }

    public function test_appointment_belongs_to_user_contact_and_activity()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $this->assertEquals($user->id, $appointment->user->id);
        $this->assertEquals($contact->id, $appointment->contact->id);
        $this->assertEquals($activite->id, $appointment->activite->id);
        
        $this->assertInstanceOf(User::class, $appointment->user);
        $this->assertInstanceOf(Contact::class, $appointment->contact);
        $this->assertInstanceOf(Activite::class, $appointment->activite);
    }

    public function test_appointment_has_notes_relationship()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'rendez_vous_id' => $appointment->id,
        ]);

        $this->assertTrue($appointment->notes->contains($note));
        $this->assertInstanceOf(Note::class, $appointment->notes->first());
    }

    public function test_appointment_has_reminders_relationship()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $reminder = Rappel::factory()->create([
            'user_id' => $user->id,
            'rendez_vous_id' => $appointment->id,
        ]);

        $this->assertTrue($appointment->rappels->contains($reminder));
        $this->assertInstanceOf(Rappel::class, $appointment->rappels->first());
    }

    public function test_activity_belongs_to_user()
    {
        $user = User::factory()->create();
        $activite = Activite::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $activite->user->id);
        $this->assertInstanceOf(User::class, $activite->user);
    }

    public function test_activity_has_appointments_relationship()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $this->assertTrue($activite->rendezVous->contains($appointment));
    }

    public function test_note_belongs_to_user_and_appointment()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'rendez_vous_id' => $appointment->id,
        ]);

        $this->assertEquals($user->id, $note->user->id);
        $this->assertEquals($appointment->id, $note->rendezVous->id);
        
        $this->assertInstanceOf(User::class, $note->user);
        $this->assertInstanceOf(RendezVous::class, $note->rendezVous);
    }

    public function test_reminder_belongs_to_user_and_appointment()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $reminder = Rappel::factory()->create([
            'user_id' => $user->id,
            'rendez_vous_id' => $appointment->id,
        ]);

        $this->assertEquals($user->id, $reminder->user->id);
        $this->assertEquals($appointment->id, $reminder->rendezVous->id);
        
        $this->assertInstanceOf(User::class, $reminder->user);
        $this->assertInstanceOf(RendezVous::class, $reminder->rendezVous);
    }

    public function test_appointment_date_casting()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        
        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
            'date_heure' => '2024-12-25 10:30:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $appointment->date_heure);
        $this->assertEquals('2024-12-25 10:30:00', $appointment->date_heure->format('Y-m-d H:i:s'));
    }

    public function test_user_password_is_hidden()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
    }

    public function test_status_has_contacts_relationship()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create();
        
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $this->assertTrue($status->contacts->contains($contact));
        $this->assertInstanceOf(Contact::class, $status->contacts->first());
    }
}
