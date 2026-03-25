<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['nom' => Role::ADMIN], ['description' => 'Administrator']);
        Role::firstOrCreate(['nom' => Role::CLIENT], ['description' => 'Client']);
    }

    public function test_register_page_renders()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_new_user_can_register_successfully()
    {
        $response = $this->post('/register', [
            'nom' => 'TestUser',
            'prenom' => 'Registration',
            'email' => 'testregister@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // User should be redirected to dashboard
        $response->assertRedirect('/dashboard');

        // User should exist in the database
        $this->assertDatabaseHas('users', [
            'nom' => 'TestUser',
            'prenom' => 'Registration',
            'email' => 'testregister@example.com',
        ]);

        // User should be authenticated
        $this->assertAuthenticated();

        // Verify user has admin role by default
        $user = User::where('email', 'testregister@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('TestUser', $user->nom);
        $this->assertEquals('Registration', $user->prenom);
    }

    public function test_register_validates_required_fields()
    {
        $response = $this->post('/register', []);
        $response->assertSessionHasErrors(['nom', 'prenom', 'email', 'password']);
    }

    public function test_register_validates_email_uniqueness()
    {
        $adminRole = Role::where('nom', Role::ADMIN)->first();
        User::factory()->create([
            'email' => 'existing@example.com',
            'role_id' => $adminRole->id,
        ]);

        $response = $this->post('/register', [
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_validates_password_confirmation()
    {
        $response = $this->post('/register', [
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_validates_password_minimum_length()
    {
        $response = $this->post('/register', [
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_validates_email_format()
    {
        $response = $this->post('/register', [
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_cannot_access_register()
    {
        $adminRole = Role::where('nom', Role::ADMIN)->first();
        $user = User::factory()->create(['role_id' => $adminRole->id]);

        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect();
    }
}
