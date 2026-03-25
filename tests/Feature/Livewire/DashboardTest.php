<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Models\User;
use App\Models\Contact;
use App\Models\RendezVous;
use App\Models\Activite;
use App\Models\Note;
use App\Models\Rappel;
use App\Models\Role;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['nom' => Role::ADMIN], ['description' => 'Administrator']);
        Role::firstOrCreate(['nom' => Role::CLIENT], ['description' => 'Client']);

        $this->user = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
    }

    /** @test */
    public function dashboard_component_can_be_rendered()
    {
        $this->actingAs($this->user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertSee('Activit');
    }

    /** @test */
    public function dashboard_loads_statistics_correctly()
    {
        $this->actingAs($this->user);

        $status = Status::factory()->create();
        $activite = Activite::factory()->create(['user_id' => $this->user->id]);
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $status->id,
        ]);

        RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
            'date_debut' => now()->addDay(),
            'heure_debut' => '09:00'
        ]);

        $component = Livewire::test(Dashboard::class);

        $stats = $component->get('stats');
        $this->assertEquals(1, $stats['contacts']);
        $this->assertEquals(1, $stats['appointments']);
        $this->assertEquals(1, $stats['activities']);
    }

    /** @test */
    public function dashboard_loads_upcoming_appointments()
    {
        $this->actingAs($this->user);

        $status = Status::factory()->create();
        $activite = Activite::factory()->create(['user_id' => $this->user->id]);
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $status->id,
        ]);

        $appointment = RendezVous::factory()->create([
            'user_id' => $this->user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
            'date_debut' => now()->addDay(),
            'heure_debut' => '09:00',
            'titre' => 'Test Appointment'
        ]);

        $component = Livewire::test(Dashboard::class)
            ->call('loadUpcomingAppointments');

        $upcomingAppointments = $component->get('upcomingAppointments');
        $this->assertCount(1, $upcomingAppointments);
        $this->assertEquals('Test Appointment', $upcomingAppointments->first()->titre);
    }

    /** @test */
    public function dashboard_refresh_functionality_works()
    {
        $this->actingAs($this->user);

        Livewire::test(Dashboard::class)
            ->call('refreshStats')
            ->assertHasNoErrors();
    }
}
