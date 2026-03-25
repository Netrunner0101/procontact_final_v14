<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'Prospect',
            'Client actif',
            'Client inactif',
            'Lead qualifié',
            'Lead non qualifié',
            'En négociation',
            'Fermé gagné',
            'Fermé perdu',
        ];

        foreach ($statuses as $status) {
            Status::create(['status_client' => $status]);
        }
    }
}
