<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            SettingSeeder::class,
            // Add other seeders here as needed
        ]);

        // Create one user
        User::create([
            'name' => 'Sample User',
            'password' => bcrypt('password123'),
            'nip' => '123456789',
            'email' => 'user@example.com',
            'telepon' => '08123456789',
            'divisi' => 'STAFF',
            'mapel' => 'PJOK',
        ]);

        // Create 100 users using factory
        User::factory(100)->create();

        // Admin Seeder
        Admin::firstOrCreate(
            ['username' => 'admin'], // cari dulu
            ['password' => bcrypt('admin123')]
        );
    }
}
