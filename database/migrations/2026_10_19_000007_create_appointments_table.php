<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: lewati jika sudah ada di database
        if (Schema::hasTable('appointments')) {
            return;
        }

        // Janji meet (online/offline) yang ditentukan di dalam chat konsultasi
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('consultant_schedules')->restrictOnDelete();

            $table->enum('tipe', ['online_meet', 'offline_meet']);
            $table->string('link_meeting')->nullable();   // diisi untuk online meet
            $table->string('lokasi')->nullable();         // diisi untuk offline meet
            $table->text('catatan')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])
                  ->default('pending');
            $table->timestamps();

            $table->index(['consultant_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
