<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: lewati jika sudah ada di database
        if (Schema::hasTable('consultant_schedules')) {
            return;
        }

        // Jadwal ketersediaan konsultan (dipilih user saat menentukan meet di chat)
        Schema::create('consultant_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('is_booked')->default(false);
            $table->timestamps();

            // satu konsultan tidak boleh punya dua slot yang sama
            $table->unique(['consultant_id', 'tanggal', 'jam_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultant_schedules');
    }
};
