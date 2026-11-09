<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: lewati jika sudah ada di database
        if (Schema::hasTable('messages')) {
            return;
        }

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipe', ['text', 'system'])->default('text'); // 'system' = notif otomatis, mis. "Janji meet dibuat"
            $table->text('isi');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['consultation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
