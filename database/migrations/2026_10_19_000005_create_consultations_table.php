<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: lewati jika sudah ada di database
        if (Schema::hasTable('consultations')) {
            return;
        }

        // Sesi konsultasi = ruang chat antara user dan konsultan
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('prodis')->restrictOnDelete();

            $table->string('topik')->nullable();                 // misal: "Konsultasi skripsi Akuntansi"
            $table->unsignedInteger('harga')->default(0);        // snapshot harga saat konsultasi dibuat
            $table->enum('status', ['active', 'closed', 'expired'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();         // chat hanya 1 jam: started_at + 1 jam
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['consultant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
