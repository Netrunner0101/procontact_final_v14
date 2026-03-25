<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\User;
use App\Models\RendezVous;
use App\Models\Activite;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'rendez_vous_id' => RendezVous::factory(),
            'activite_id' => Activite::factory(),
            'titre' => $this->faker->sentence(4),
            'commentaire' => $this->faker->paragraph(),
            'date_create' => $this->faker->dateTimeThisMonth(),
            'date_update' => $this->faker->dateTimeThisMonth(),
        ];
    }
}
