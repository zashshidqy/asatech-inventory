<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user for development
        User::factory()->create([
            'name' => 'Admin AsaTech',
            'email' => 'admin@asatech.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create staff user for development
        User::factory()->create([
            'name' => 'Staff AsaTech',
            'email' => 'staff@asatech.test',
            'password' => bcrypt('password123'),
            'role' => 'staff',
            'is_active' => true,
        ]);
    }
}