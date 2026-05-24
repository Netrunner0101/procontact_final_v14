<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Pays;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdressesRenderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Pays::firstOrCreate(['code' => 'FR'], ['nom' => 'France']);
        Pays::firstOrCreate(['code' => 'BE'], ['nom' => 'Belgique']);

        $adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Admin']);
        $this->user = User::factory()->create(['role_id' => $adminRole->id]);
    }

    public function test_profile_page_renders_with_address_repeater(): void
    {
        $this->user->adresses()->create([
            'rue' => 'Rue Test', 'numero_rue' => '12', 'code_postal' => '75001',
            'ville' => 'Paris', 'pays_code' => 'FR', 'is_principale' => true,
        ]);

        $response = $this->actingAs($this->user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Rue Test', false);
        $response->assertSee('adresses[', false);
        $response->assertSee('Add an address', false);
    }

    public function test_contact_create_page_renders_address_repeater(): void
    {
        $response = $this->actingAs($this->user)->get('/contacts/create');

        $response->assertStatus(200);
        $response->assertSee('Add an address', false);
        $response->assertSee('adresses[', false);
    }

    public function test_contact_edit_page_renders_existing_addresses(): void
    {
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $status->id,
        ]);
        $contact->adresses()->create([
            'rue' => 'Rue Existante', 'ville' => 'Lyon', 'pays_code' => 'FR', 'is_principale' => true,
        ]);

        $response = $this->actingAs($this->user)->get("/contacts/{$contact->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Rue Existante', false);
    }

    public function test_contact_show_page_renders_addresses(): void
    {
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $status->id,
        ]);
        $contact->adresses()->create([
            'rue' => 'Rue Affichée', 'ville' => 'Marseille',
            'pays_code' => 'FR', 'is_principale' => true,
        ]);

        $response = $this->actingAs($this->user)->get("/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertSee('Rue Affichée', false);
        $response->assertSee('Marseille', false);
    }

    public function test_can_create_contact_with_multiple_addresses_via_http(): void
    {
        $response = $this->actingAs($this->user)->post('/contacts', [
            'nom' => 'Doe',
            'prenom' => 'John',
            'emails' => ['john@example.com'],
            'phones' => ['+33 6 12 34 56 78'],
            'adresses' => [
                ['rue' => 'A', 'ville' => 'Paris', 'pays_code' => 'FR', 'is_principale' => 1],
                ['rue' => 'B', 'ville' => 'Bruxelles', 'pays_code' => 'BE'],
            ],
        ]);

        $response->assertRedirect();
        $contact = Contact::where('nom', 'Doe')->first();
        $this->assertNotNull($contact);
        $this->assertCount(2, $contact->adresses);
        $this->assertSame('Paris', $contact->adressePrincipale->ville);
    }

    public function test_can_update_contact_addresses_via_http(): void
    {
        $status = Status::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $this->user->id,
            'status_id' => $status->id,
        ]);
        $contact->adresses()->create([
            'rue' => 'Old', 'ville' => 'OldCity', 'pays_code' => 'FR', 'is_principale' => true,
        ]);

        // Need to seed an email + phone (model requires at least one on update too).
        $contact->emails()->create(['email' => 'x@example.com', 'user_id' => $this->user->id]);
        $contact->numeroTelephones()->create(['numero_telephone' => '+33 1 23 45 67 89', 'user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->put("/contacts/{$contact->id}", [
            'nom' => $contact->nom,
            'prenom' => $contact->prenom,
            'emails' => ['x@example.com'],
            'phones' => ['+33 1 23 45 67 89'],
            'adresses' => [
                ['rue' => 'New', 'ville' => 'NewCity', 'pays_code' => 'FR', 'is_principale' => 1],
            ],
        ]);

        $response->assertRedirect();
        $contact->refresh();
        $this->assertCount(1, $contact->adresses);
        $this->assertSame('NewCity', $contact->adressePrincipale->ville);
    }
}
