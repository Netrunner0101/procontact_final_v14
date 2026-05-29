<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePhoneTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create(['role_id' => 1]);
    }

    public function test_user_can_save_multiple_phone_numbers_from_profile(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->put('/profile', [
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'phones' => ['+33 6 11 11 11 11', '+32 2 22 22 22 22'],
            ])
            ->assertRedirect();

        $this->assertEquals(2, $user->numeroTelephones()->count());
        $this->assertDatabaseHas('numero_telephones', [
            'user_id' => $user->id,
            'contact_id' => null,
            'numero_telephone' => '+33 6 11 11 11 11',
        ]);
    }

    public function test_profile_update_replaces_existing_phone_numbers(): void
    {
        $user = $this->makeUser();
        $user->numeroTelephones()->create(['numero_telephone' => '+33 6 00 00 00 00']);

        $this->actingAs($user)->put('/profile', [
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'phones' => ['+32 2 22 22 22 22'],
        ])->assertRedirect();

        $this->assertEquals(['+32 2 22 22 22 22'], $user->numeroTelephones()->pluck('numero_telephone')->all());
    }

    public function test_user_may_have_zero_phone_numbers(): void
    {
        $user = $this->makeUser();
        $user->numeroTelephones()->create(['numero_telephone' => '+33 6 00 00 00 00']);

        $this->actingAs($user)->put('/profile', [
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'phones' => ['', '   '],
        ])->assertRedirect();

        $this->assertEquals(0, $user->numeroTelephones()->count());
    }

    public function test_user_phones_exclude_contact_phone_numbers(): void
    {
        $user = $this->makeUser();
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        // Contact phones also carry the admin's user_id for tenant scoping;
        // they must not be returned as the user's own numbers.
        $contact->numeroTelephones()->create([
            'numero_telephone' => '+1 555 0000',
            'user_id' => $user->id,
        ]);
        $user->numeroTelephones()->create(['numero_telephone' => '+33 6 11 11 11 11']);

        $this->assertEquals(1, $user->numeroTelephones()->count());
        $this->assertEquals(1, $contact->numeroTelephones()->count());
    }
}
