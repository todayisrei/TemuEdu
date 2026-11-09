<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 64)->unique();          // order_id yang dikirim ke Midtrans

            // catatan keuangan: tidak ikut terhapus jika user/konsultan dihapus (restrict)
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('consultant_id')->constrained('consultants')->restrictOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();

            $table->string('service', 20);                      // chat | online_meet | offline_meet
            $table->unsignedInteger('amount');                  // Rupiah, dari config (server)
            $table->string('status', 20)->default('pending');  // pending | paid | failed | expired | cancelled | refund_pending

            $table->text('snap_token')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->string('transaction_status', 30)->nullable();
            $table->string('fraud_status', 30)->nullable();
            $table->json('raw_notification')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
