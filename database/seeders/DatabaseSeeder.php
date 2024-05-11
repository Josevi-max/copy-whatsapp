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
        User::factory()->create([
            'name' => 'Josevi',
            'email' => 'Josevi@test.com',
            'password' => bcrypt('123456789')
        ]);
        User::factory(10)->create();

        
    }
}
