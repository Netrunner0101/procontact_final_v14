<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'state_client' => $this->faker->randomElement(['Actif', 'Inactif', 'Prospect']),
            'status_id' => Status::factory(),
        ];
    }
}
