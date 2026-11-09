<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // consultations: jenis layanan + status bayar.
        // Data LAMA (sebelum ada pembayaran) otomatis dianggap layanan chat yang sudah dibayar.
        if (! Schema::hasColumn('consultations', 'service')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->string('service', 20)->default('chat')->after('prodi_id'); // chat | online_meet | offline_meet
                $table->boolean('is_paid')->default(true)->after('service');
            });
        }

        // appointments: status pembayaran + durasi + harga (snapshot). Data lama = sudah dibayar.
        if (! Schema::hasColumn('appointments', 'payment_status')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->string('payment_status', 20)->default('paid')->after('status'); // unpaid | paid
                $table->unsignedSmallInteger('duration_minutes')->nullable()->after('payment_status');
                $table->unsignedInteger('harga')->nullable()->after('duration_minutes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('appointments', 'payment_status')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn(['payment_status', 'duration_minutes', 'harga']);
            });
        }

        if (Schema::hasColumn('consultations', 'service')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->dropColumn(['service', 'is_paid']);
            });
        }
    }
};
