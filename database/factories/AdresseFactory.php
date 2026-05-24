<?php

namespace Database\Factories;

use App\Models\Adresse;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdresseFactory extends Factory
{
    protected $model = Adresse::class;

    public function definition(): array
    {
        return [
            'rue' => $this->faker->streetName(),
            'numero_rue' => $this->faker->buildingNumber(),
            'code_postal' => $this->faker->postcode(),
            'ville' => $this->faker->city(),
            'pays_code' => 'FR',
            'is_principale' => true,
        ];
    }
}
