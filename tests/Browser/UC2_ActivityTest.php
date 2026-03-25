<?php

namespace Tests\Browser;

use App\Models\Role;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UC2_ActivityTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Helper: create an admin user for authenticated tests.
     */
    private function createAdminUser(): User
    {
        $role = Role::firstOrCreate(['nom' => 'admin']);

        return User::factory()->create([
            'role_id' => $role->id,
            'email' => 'admin-activity@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Nominal scenario: create an activity with nom and description.
     */
    public function testCreateActivityNominal(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/activites/create')
                ->assertSee('Nouvelle Activité')
                ->type('nom', 'Consultation initiale')
                ->type('description', 'Première consultation avec le client pour évaluer ses besoins.')
                ->press("Créer l'Activité")
                ->waitForLocation('/activites')
                ->assertPathIs('/activites')
                ->assertSee('Consultation initiale');
        });
    }

    /**
     * Validation: submitting an empty form shows required-field errors.
     */
    public function testCreateActivityValidation(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/activites/create')
                ->clear('nom')
                ->clear('description')
                ->script("document.querySelectorAll('input[required], textarea[required]').forEach(i => i.removeAttribute('required'))");

            $browser->press("Créer l'Activité")
                ->waitForText('nom')
                ->assertSee('nom');
        });
    }
}
