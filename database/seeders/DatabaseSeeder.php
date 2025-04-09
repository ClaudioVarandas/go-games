<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@ggg.com',
            'role' => 'admin',
            'password' => '1234',
        ]);

        User::factory()->create([
            'name' => 'gamer',
            'email' => 'gamer@ggg.com',
            'role' => 'gamer',
            'password' => '1234',
        ]);

    }
}
