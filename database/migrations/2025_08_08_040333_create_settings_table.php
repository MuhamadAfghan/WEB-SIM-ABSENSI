<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Monday work schedule
            $table->time('monday_start_time')->default('08:00');
            $table->time('monday_end_time')->default('15:30');
            $table->boolean('monday_is_active')->default(true);

            // Tuesday work schedule
            $table->time('tuesday_start_time')->default('08:00');
            $table->time('tuesday_end_time')->default('15:30');
            $table->boolean('tuesday_is_active')->default(true);

            // Wednesday work schedule
            $table->time('wednesday_start_time')->default('08:00');
            $table->time('wednesday_end_time')->default('15:30');
            $table->boolean('wednesday_is_active')->default(true);

            // Thursday work schedule
            $table->time('thursday_start_time')->default('08:00');
            $table->time('thursday_end_time')->default('15:30');
            $table->boolean('thursday_is_active')->default(true);

            // Friday work schedule
            $table->time('friday_start_time')->default('08:00');
            $table->time('friday_end_time')->default('15:30');
            $table->boolean('friday_is_active')->default(true);

            // Saturday work schedule
            $table->time('saturday_start_time')->default('08:00');
            $table->time('saturday_end_time')->default('15:30');
            $table->boolean('saturday_is_active')->default(true);

            // Location settings
            $table->string('location_name')->default('SMK Wikrama Bogor');
            $table->double('latitude', 10, 7)->default(-6.6476344);
            $table->double('longitude', 10, 7)->default(106.8169444);
            $table->integer('radius')->default(100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
