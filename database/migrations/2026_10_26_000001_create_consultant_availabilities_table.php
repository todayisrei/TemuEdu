<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('consultant_availabilities')) {
            return;
        }

        // Jam layanan mingguan konsultan (berlaku untuk Chat, Online Meet, dan penentuan Offline Meet)
        Schema::create('consultant_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');   // 1 = Senin ... 7 = Minggu (ISO)
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['consultant_id', 'day_of_week']);
        });

        // Konsultan yang SUDAH ada diberi jam layanan Senin-Minggu 00:00-23:59 agar chat lama
        // tidak langsung terkunci. Konsultan bisa mengubahnya di menu Jadwal.
        $now = now();
        foreach (DB::table('consultants')->pluck('id') as $consultantId) {
            for ($day = 1; $day <= 7; $day++) {
                DB::table('consultant_availabilities')->insert([
                    'consultant_id' => $consultantId,
                    'day_of_week' => $day,
                    'start_time' => '00:00:00',
                    'end_time' => '23:59:00',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('consultant_availabilities');
    }
};
