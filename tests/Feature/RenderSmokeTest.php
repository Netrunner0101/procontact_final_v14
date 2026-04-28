<?php

namespace Tests\Feature;

use App\Models\Activite;
use App\Models\Contact;
use App\Models\RendezVous;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenderSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_show_page_renders_two_separate_containers_and_no_stats_button(): void
    {
        $role = Role::firstOrCreate(['nom' => 'admin']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $activity = Activite::factory()->for($user)->create(['nom' => 'Garden Test']);

        $contact = Contact::factory()->for($user)->create();
        $activity->contacts()->attach($contact->id);

        RendezVous::factory()->for($user)->for($activity)->for($contact)->create([
            'titre' => 'Future RDV',
            'date_debut' => now()->addDay()->toDateString(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
            'statut' => 'scheduled',
        ]);

        RendezVous::factory()->for($user)->for($activity)->for($contact)->create([
            'titre' => 'Past RDV',
            'date_debut' => now()->subDay()->toDateString(),
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
            'statut' => 'completed',
        ]);

        $response = $this->actingAs($user)->get(route('activites.show', $activity));

        $response->assertOk();
        $response->assertSee('Garden Test');
        // Two separate containers, both visible at once (no tabs)
        $response->assertSee('id="appointments-heading"', false);
        $response->assertSee('id="stats-heading"', false);
        $response->assertDontSee('role="tablist"', false);
        // Both appointment lists are rendered
        $response->assertSee('Future RDV');
        $response->assertSee('Past RDV');
        // Status Distribution comes from the Stats container
        $response->assertSee('Status Distribution');
        // Quick Actions sidebar should no longer contain the removed Statistics button.
        $response->assertDontSee('block w-full bg-gray-600', false);
    }

    public function test_dashboard_renders_stats_at_top_and_appointments_toggle(): void
    {
        $role = Role::firstOrCreate(['nom' => 'admin']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $activity = Activite::factory()->for($user)->create(['nom' => 'My Garden']);
        $contact = Contact::factory()->for($user)->create();
        RendezVous::factory()->for($user)->for($activity)->for($contact)->create([
            'titre' => 'Visit Garden',
            'date_debut' => now()->addDay()->toDateString(),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
            'statut' => 'scheduled',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('My Garden');
        $response->assertSeeText('See appointments');
    }
}
