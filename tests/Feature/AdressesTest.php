<?php

namespace Tests\Feature;

use App\Models\Adresse;
use App\Models\Contact;
use App\Models\Pays;
use App\Models\User;
use App\Services\AdresseSyncer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdressesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Pays::firstOrCreate(['code' => 'FR'], ['nom' => 'France']);
        Pays::firstOrCreate(['code' => 'BE'], ['nom' => 'Belgique']);
    }

    public function test_user_can_have_multiple_addresses(): void
    {
        $user = User::factory()->create();

        $user->adresses()->create([
            'rue' => 'Rue de Paris', 'ville' => 'Paris', 'pays_code' => 'FR', 'is_principale' => true,
        ]);
        $user->adresses()->create([
            'rue' => 'Avenue Louise', 'ville' => 'Bruxelles', 'pays_code' => 'BE', 'is_principale' => false,
        ]);

        $this->assertCount(2, $user->adresses()->get());
        $this->assertSame('Paris', $user->adressePrincipale->ville);
    }

    public function test_contact_can_have_multiple_addresses_polymorphically(): void
    {
        $contact = Contact::factory()->create();

        $contact->adresses()->create([
            'rue' => 'Test', 'ville' => 'Lyon', 'pays_code' => 'FR', 'is_principale' => true,
        ]);

        $this->assertCount(1, $contact->adresses);
        $this->assertSame('Lyon', $contact->adressePrincipale->ville);
        $this->assertSame('France', $contact->adressePrincipale->pays->nom);
    }

    public function test_only_one_address_can_be_principal_per_owner(): void
    {
        $contact = Contact::factory()->create();

        $first = $contact->adresses()->create([
            'ville' => 'A', 'is_principale' => true,
        ]);
        $second = $contact->adresses()->create([
            'ville' => 'B', 'is_principale' => true,
        ]);

        $first->refresh();
        $second->refresh();

        $this->assertFalse($first->is_principale);
        $this->assertTrue($second->is_principale);
    }

    public function test_adresse_syncer_replaces_addresses(): void
    {
        $contact = Contact::factory()->create();
        $contact->adresses()->create(['ville' => 'Old', 'is_principale' => true]);

        app(AdresseSyncer::class)->sync($contact, [
            ['rue' => 'A', 'ville' => 'New 1', 'pays_code' => 'FR', 'is_principale' => true],
            ['rue' => 'B', 'ville' => 'New 2', 'pays_code' => 'FR'],
        ]);

        $contact->refresh();
        $this->assertCount(2, $contact->adresses);
        $this->assertSame('New 1', $contact->adressePrincipale->ville);
    }

    public function test_adresse_syncer_drops_empty_rows(): void
    {
        $contact = Contact::factory()->create();

        app(AdresseSyncer::class)->sync($contact, [
            ['rue' => '', 'ville' => '', 'pays_code' => ''],
            ['rue' => 'Filled', 'ville' => 'City', 'pays_code' => 'FR'],
        ]);

        $this->assertCount(1, $contact->adresses);
        $this->assertSame('Filled', $contact->adresses->first()->rue);
    }

    public function test_adresse_syncer_auto_marks_first_as_principal_when_none_flagged(): void
    {
        $contact = Contact::factory()->create();

        app(AdresseSyncer::class)->sync($contact, [
            ['rue' => 'A', 'pays_code' => 'FR'],
            ['rue' => 'B', 'pays_code' => 'BE'],
        ]);

        $principals = $contact->adresses()->where('is_principale', true)->count();
        $this->assertSame(1, $principals);
    }

    public function test_deleting_contact_cascades_to_adresses(): void
    {
        $contact = Contact::factory()->create();
        $contact->adresses()->create(['ville' => 'X', 'is_principale' => true]);
        $contact->adresses()->create(['ville' => 'Y']);

        $contactId = $contact->id;
        $contact->delete();

        $orphans = Adresse::where('addressable_type', Contact::class)
            ->where('addressable_id', $contactId)
            ->count();

        $this->assertSame(0, $orphans, 'Contact deletion must cascade to its addresses.');
    }

    public function test_deleting_user_cascades_to_user_and_contact_addresses(): void
    {
        $user = User::factory()->create();
        $user->adresses()->create(['ville' => 'UserCity', 'is_principale' => true]);

        $contact = Contact::factory()->create(['user_id' => $user->id]);
        $contact->adresses()->create(['ville' => 'ContactCity', 'is_principale' => true]);

        $userId = $user->id;
        $contactId = $contact->id;
        $user->delete();

        $userAddressesLeft = Adresse::where('addressable_type', User::class)
            ->where('addressable_id', $userId)->count();
        $contactAddressesLeft = Adresse::where('addressable_type', Contact::class)
            ->where('addressable_id', $contactId)->count();

        $this->assertSame(0, $userAddressesLeft, "User's own addresses must be deleted.");
        $this->assertSame(0, $contactAddressesLeft, "Cascaded contacts' addresses must also be deleted.");
    }
}
