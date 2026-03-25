<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@procontact.test'],
            [
                'nom' => 'Admin',
                'prenom' => 'ProContact',
                'email' => 'admin@procontact.test',
                'password' => Hash::make('password'),
                'role_id' => 1, // admin
                'provider' => 'email',
            ]
        );

        // Client user
        User::firstOrCreate(
            ['email' => 'client@procontact.test'],
            [
                'nom' => 'Client',
                'prenom' => 'Test',
                'email' => 'client@procontact.test',
                'password' => Hash::make('password'),
                'role_id' => 2, // client
                'provider' => 'email',
            ]
        );
    }
}
