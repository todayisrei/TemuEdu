<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: lewati jika sudah ada di database
        if (Schema::hasTable('reviews')) {
            return;
        }

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->unique()->constrained('consultations')->cascadeOnDelete(); // 1 konsultasi = 1 ulasan
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');   // 1-5
            $table->text('ulasan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
