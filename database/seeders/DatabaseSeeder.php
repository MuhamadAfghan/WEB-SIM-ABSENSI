<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin;
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

        // Admin Seeder
        Admin::firstOrCreate(
            ['username' => 'admin'], // cari dulu
            ['password' => bcrypt('admin123')]
        );
    }
}
