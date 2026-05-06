<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
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
        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->post('/register', [
            'nom' => 'TestUser',
            'prenom' => 'Registration',
            'email' => 'testregister@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // User should be redirected to the success page (NOT auto-logged-in into dashboard)
        $response->assertRedirect(route('register.success'));

        // User should exist in the database, with no verified email yet
        $this->assertDatabaseHas('users', [
            'nom' => 'TestUser',
            'prenom' => 'Registration',
            'email' => 'testregister@example.com',
            'email_verified_at' => null,
        ]);

        // User must NOT be authenticated until they verify their email
        $this->assertGuest();

        // Verification email was queued/sent
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\EmailVerificationMail::class, function ($mail) {
            return $mail->hasTo('testregister@example.com');
        });

        // Verify user has admin role by default and a token was generated
        $user = User::where('email', 'testregister@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('TestUser', $user->nom);
        $this->assertEquals('Registration', $user->prenom);
        $this->assertNotNull($user->email_verification_token);
        $this->assertNotNull($user->email_verification_expires);
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

    public function test_unverified_user_cannot_log_in()
    {
        $adminRole = Role::where('nom', Role::ADMIN)->first();
        $user = User::factory()->unverified()->create([
            'email' => 'pending@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role_id' => $adminRole->id,
            'provider' => null,
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertGuest();
    }

    public function test_verifying_email_marks_user_as_verified()
    {
        $adminRole = Role::where('nom', Role::ADMIN)->first();
        $token = \Illuminate\Support\Str::random(64);
        $user = User::factory()->unverified()->create([
            'email' => 'verifyme@example.com',
            'role_id' => $adminRole->id,
            'email_verification_token' => $token,
            'email_verification_expires' => now()->addHours(24),
        ]);

        $response = $this->get(route('verification.verify', ['token' => $token]));

        $response->assertRedirect(route('login'));
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verification_token);
    }

    public function test_expired_verification_token_is_rejected()
    {
        $adminRole = Role::where('nom', Role::ADMIN)->first();
        $token = \Illuminate\Support\Str::random(64);
        $user = User::factory()->unverified()->create([
            'email' => 'expired@example.com',
            'role_id' => $adminRole->id,
            'email_verification_token' => $token,
            'email_verification_expires' => now()->subHour(),
        ]);

        $response = $this->get(route('verification.verify', ['token' => $token]));

        $response->assertRedirect(route('verification.notice'));
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_invalid_verification_token_is_rejected()
    {
        $response = $this->get(route('verification.verify', ['token' => 'definitely-not-a-real-token']));
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_resend_verification_email_sends_new_email()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $adminRole = Role::where('nom', Role::ADMIN)->first();
        $user = User::factory()->unverified()->create([
            'email' => 'resend@example.com',
            'role_id' => $adminRole->id,
            'provider' => null,
        ]);

        $response = $this->post(route('verification.resend'), [
            'email' => 'resend@example.com',
        ]);

        $response->assertSessionHas('status');
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\EmailVerificationMail::class, function ($mail) {
            return $mail->hasTo('resend@example.com');
        });
    }

    public function test_register_validates_password_complexity()
    {
        $response = $this->post('/register', [
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => 'complex@example.com',
            'password' => 'onlylettersxxx',
            'password_confirmation' => 'onlylettersxxx',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
