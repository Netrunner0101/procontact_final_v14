<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Pays;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NumeroTelephonePaysTest extends TestCase
{
    use RefreshDatabase;

    private function seedPays(): void
    {
        Pays::updateOrCreate(['code' => 'BE'], ['nom' => 'Belgique', 'indicatif' => '+32']);
        Pays::updateOrCreate(['code' => 'FR'], ['nom' => 'France', 'indicatif' => '+33']);
    }

    public function test_pays_table_has_indicatif_column(): void
    {
        $this->seedPays();

        $this->assertSame('+32', Pays::find('BE')->indicatif);
    }

    public function test_phone_number_belongs_to_a_country(): void
    {
        $this->seedPays();
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $phone = $contact->numeroTelephones()->create([
            'numero_telephone' => '470123456',
            'pays_code' => 'BE',
            'user_id' => $user->id,
        ]);

        $this->assertSame('BE', $phone->pays->code);
        $this->assertSame('+32 470123456', $phone->full_number);
    }

    public function test_same_number_can_have_different_prefixes(): void
    {
        $this->seedPays();
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $be = $contact->numeroTelephones()->create([
            'numero_telephone' => '470123456', 'pays_code' => 'BE', 'user_id' => $user->id,
        ]);
        $fr = $contact->numeroTelephones()->create([
            'numero_telephone' => '470123456', 'pays_code' => 'FR', 'user_id' => $user->id,
        ]);

        $this->assertSame($be->numero_telephone, $fr->numero_telephone);
        $this->assertNotSame($be->full_number, $fr->full_number);
        $this->assertSame('+33 470123456', $fr->full_number);
    }

    public function test_country_has_many_phone_numbers(): void
    {
        $this->seedPays();
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);
        $contact->numeroTelephones()->create([
            'numero_telephone' => '470123456', 'pays_code' => 'BE', 'user_id' => $user->id,
        ]);

        $this->assertCount(1, Pays::find('BE')->numerosTelephone);
    }

    public function test_full_number_falls_back_to_raw_value_without_country(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);
        $phone = $contact->numeroTelephones()->create([
            'numero_telephone' => '+33 6 12 34 56 78', 'user_id' => $user->id,
        ]);

        $this->assertNull($phone->pays_code);
        $this->assertSame('+33 6 12 34 56 78', $phone->full_number);
    }
}
