<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: lewati jika sudah ada di database
        if (Schema::hasTable('prodis')) {
            return;
        }

        // Program Studi di FEB
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_prodi')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
