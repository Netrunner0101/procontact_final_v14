<?php

namespace Database\Factories;

use App\Models\RendezVous;
use App\Models\User;
use App\Models\Contact;
use App\Models\Activite;
use Illuminate\Database\Eloquent\Factories\Factory;

class RendezVousFactory extends Factory
{
    protected $model = RendezVous::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'contact_id' => Contact::factory(),
            'activite_id' => Activite::factory(),
            'titre' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'date_debut' => $this->faker->dateTimeBetween('now', '+1 month'),
            'date_fin' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'heure_debut' => $this->faker->time('H:i'),
            'heure_fin' => $this->faker->time('H:i'),
        ];
    }
}
