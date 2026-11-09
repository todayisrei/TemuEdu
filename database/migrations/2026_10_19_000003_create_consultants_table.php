<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: lewati jika sudah ada di database
        if (Schema::hasTable('consultants')) {
            return;
        }

        // Profil lengkap konsultan (akunnya tetap di tabel users dengan role 'konsultan')
        Schema::create('consultants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('prodis')->restrictOnDelete();

            $table->string('foto')->nullable();
            $table->string('domisili')->nullable();
            $table->text('kompetensi')->nullable();
            $table->string('alumni')->nullable();            // asal kampus
            $table->decimal('ipk', 3, 2)->nullable();
            $table->unsignedInteger('harga')->default(0);    // tarif per sesi (Rupiah)
            $table->string('portofolio_link')->nullable();   // LinkedIn / portofolio

            // Ringkasan ulasan (dihitung dari tabel reviews)
            $table->decimal('rating_avg', 2, 1)->default(0);
            $table->unsignedInteger('rating_count')->default(0);

            $table->boolean('is_active')->default(true);     // admin bisa menonaktifkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultants');
    }
};
