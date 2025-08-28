<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            // Monday work schedule
            'monday_start_time' => '08:00',
            'monday_end_time' => '15:30',
            'monday_is_active' => true,

            // Tuesday work schedule
            'tuesday_start_time' => '08:00',
            'tuesday_end_time' => '15:30',
            'tuesday_is_active' => true,

            // Wednesday work schedule
            'wednesday_start_time' => '08:00',
            'wednesday_end_time' => '15:30',
            'wednesday_is_active' => true,

            // Thursday work schedule
            'thursday_start_time' => '08:00',
            'thursday_end_time' => '15:30',
            'thursday_is_active' => true,

            // Friday work schedule
            'friday_start_time' => '08:00',
            'friday_end_time' => '15:30',
            'friday_is_active' => true,

            // Saturday work schedule
            'saturday_start_time' => '08:00',
            'saturday_end_time' => '15:30',
            'saturday_is_active' => true,

            // Location settings - SMK Wikrama Bogor
            'location_name' => 'SMK Wikrama Bogor',
            'latitude' => -6.6453,
            'longitude' => 106.8440,
            'radius' => 100
        ]);
    }
}
