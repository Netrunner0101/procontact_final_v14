<?php

namespace Tests\Feature;

use App\Models\Activite;
use App\Models\Contact;
use App\Models\RendezVous;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role_id' => 1,
            'email' => 'jane@example.com',
            'password' => Hash::make('correct-password'),
        ], $overrides));
    }

    public function test_guest_cannot_delete_an_account()
    {
        $response = $this->delete('/profile');
        $response->assertRedirect('/login');
    }

    public function test_user_can_permanently_delete_their_account_and_owned_data()
    {
        $user = $this->makeUser();
        $status = Status::factory()->create(['status_client' => 'Prospect']);
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);
        $activite = Activite::factory()->create(['user_id' => $user->id]);
        RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
        ]);

        $response = $this->actingAs($user)->delete('/profile', [
            'confirm_email' => 'jane@example.com',
            'confirm_phrase' => 'DELETE',
            'current_password' => 'correct-password',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('contacts', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('activites', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('rendez_vous', ['user_id' => $user->id]);
    }

    public function test_deletion_requires_correct_password()
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->delete('/profile', [
            'confirm_email' => 'jane@example.com',
            'confirm_phrase' => 'DELETE',
            'current_password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_deletion_requires_email_to_match()
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->delete('/profile', [
            'confirm_email' => 'someone-else@example.com',
            'confirm_phrase' => 'DELETE',
            'current_password' => 'correct-password',
        ]);

        $response->assertSessionHasErrors('confirm_email');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_deletion_requires_confirmation_phrase()
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->delete('/profile', [
            'confirm_email' => 'jane@example.com',
            'confirm_phrase' => 'nope',
            'current_password' => 'correct-password',
        ]);

        $response->assertSessionHasErrors('confirm_phrase');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_oauth_user_can_delete_without_password()
    {
        $user = $this->makeUser([
            'email' => 'oauth@example.com',
            'provider' => 'google',
            'google_id' => 'google-123',
        ]);

        $response = $this->actingAs($user)->delete('/profile', [
            'confirm_email' => 'oauth@example.com',
            'confirm_phrase' => 'DELETE',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_authenticated_user_can_export_their_data()
    {
        $user = $this->makeUser();
        $status = Status::factory()->create(['status_client' => 'Prospect']);
        Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $response = $this->actingAs($user)->get('/profile/export');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
        $response->assertHeader('content-disposition', 'attachment; filename=procontact-data-export-' . now()->format('Y-m-d') . '.json');
    }
}
