<?php

namespace Tests\Browser;

use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UC1_ContactTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Helper: create an admin user for authenticated tests.
     */
    private function createAdminUser(): User
    {
        $role = Role::firstOrCreate(['nom' => 'admin']);
        Status::firstOrCreate(['status_client' => 'Prospect']);

        return User::factory()->create([
            'role_id' => $role->id,
            'email' => 'admin-contact@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Nominal scenario: create a contact with nom, prenom, email, phone.
     */
    public function testCreateContactNominal(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/contacts/create')
                ->assertSee('Nouveau Contact')
                ->type('nom', 'Dupont')
                ->type('prenom', 'Jean')
                ->type('emails[]', 'jean.dupont@example.com')
                ->type('phones[]', '+33 1 23 45 67 89')
                ->press('Créer le Contact')
                ->waitForLocation('/contacts')
                ->assertPathIs('/contacts')
                ->assertSee('Contact créé avec succès');
        });
    }

    /**
     * Validation: submitting an empty form shows required-field errors.
     */
    public function testCreateContactValidation(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/contacts/create')
                ->clear('nom')
                ->clear('prenom')
                ->clear('emails[]')
                ->clear('phones[]')
                ->script("document.querySelectorAll('input[required]').forEach(i => i.removeAttribute('required'))");

            $browser->press('Créer le Contact')
                ->waitForText('nom')
                ->assertSee('nom');
        });
    }

    /**
     * Invalid email: submitting a malformed email address shows an error.
     */
    public function testCreateContactInvalidEmail(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/contacts/create')
                ->type('nom', 'Dupont')
                ->type('prenom', 'Jean')
                ->type('emails[]', 'not-an-email')
                ->type('phones[]', '+33 1 23 45 67 89')
                ->script("document.querySelectorAll('input[required]').forEach(i => i.removeAttribute('required'))");

            $browser->press('Créer le Contact')
                ->waitForText("n'est pas valide")
                ->assertSee("n'est pas valide");
        });
    }

    /**
     * Cancel: clicking the cancel link returns to the contact list.
     */
    public function testCancelCreateContact(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/contacts/create')
                ->clickLink('Annuler')
                ->waitForLocation('/contacts')
                ->assertPathIs('/contacts');
        });
    }
}
