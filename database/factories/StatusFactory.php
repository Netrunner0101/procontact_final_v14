<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        return [
            'status_client' => $this->faker->randomElement(['Prospect', 'Client actif', 'Client inactif', 'Lead qualifié']),
        ];
    }
}
