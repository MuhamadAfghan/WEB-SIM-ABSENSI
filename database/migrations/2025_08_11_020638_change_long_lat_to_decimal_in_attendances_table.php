<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Ubah ke double precision
            $table->double('longitude')->change();
            $table->double('latitude')->change();
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
