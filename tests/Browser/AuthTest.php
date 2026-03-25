<?php

namespace Tests\Browser;

use App\Models\Role;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Helper: ensure admin role exists and create an admin user.
     */
    private function createAdminUser(string $email = 'admin@test.com', string $password = 'password'): User
    {
        $role = Role::firstOrCreate(['nom' => 'admin']);

        return User::factory()->create([
            'role_id' => $role->id,
            'email' => $email,
            'password' => bcrypt($password),
        ]);
    }

    /**
     * Nominal login: valid credentials redirect to /dashboard.
     */
    public function testLoginNominal(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->assertSee('Pro Contact')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Se connecter')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard');
        });
    }

    /**
     * Invalid credentials: wrong password shows an error message.
     */
    public function testLoginInvalidCredentials(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'wrong-password')
                ->press('Se connecter')
                ->waitForText('ne correspondent pas')
                ->assertSee('ne correspondent pas');
        });
    }

    /**
     * Register: fill the registration form and end up on /dashboard.
     */
    public function testRegister(): void
    {
        // Ensure the admin role exists so registration can assign it.
        Role::firstOrCreate(['nom' => 'admin']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Pro Contact')
                ->type('nom', 'Durand')
                ->type('prenom', 'Marie')
                ->type('email', 'marie.durand@example.com')
                ->type('password', 'Secret123!')
                ->type('password_confirmation', 'Secret123!')
                ->press('mon compte')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard');
        });
    }

    /**
     * Logout: authenticated user logs out and is redirected to /login.
     */
    public function testLogout(): void
    {
        $user = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');

            // Open the user dropdown and click the logout button.
            // The logout is inside a form; we submit it via script to avoid
            // needing to locate the exact dropdown toggle.
            $browser->script("document.querySelector('form[action*=\"logout\"] button').click()");

            $browser->waitForLocation('/login')
                ->assertPathIs('/login');
        });
    }
}
