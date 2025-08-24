<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->date('date-start');
            $table->date('date-end');
            $table->enum('type', ['sakit', 'izin', 'tanpa_keterangan']);
            $table->boolean('is_approved')->default(false);
            $table->string('description')->nullable();
            $table->string('upload_attachment')->nullable();
        });
    }

    /** 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
