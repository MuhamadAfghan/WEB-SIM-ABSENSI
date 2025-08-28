<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->dropColumn(['longitude', 'latitude']);
    });

    Schema::table('attendances', function (Blueprint $table) {
        $table->double('longitude')->nullable(false);
        $table->double('latitude')->nullable(false);
    });
}


    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Balikin ke integer kalau rollback
            $table->integer('longitude')->change();
            $table->integer('latitude')->change();
        });
    }
};
