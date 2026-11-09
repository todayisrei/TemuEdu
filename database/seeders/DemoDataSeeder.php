<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Consultant;
use App\Models\ConsultantSchedule;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Message;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * DATA CONTOH untuk testing/presentasi. TIDAK dipanggil otomatis oleh DatabaseSeeder.
 *
 *   php artisan db:seed --class=DemoDataSeeder
 *
 * - Hanya berjalan di APP_ENV=local.
 * - Aman diulang (tidak menimpa data yang sudah ada).
 * - Password dibuat acak lalu DITAMPILKAN SEKALI di terminal (tidak ada password tetap di kode).
 *   Atau isi DEMO_PASSWORD di .env untuk memakai password sendiri.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            $this->command?->error('DemoDataSeeder hanya boleh dijalankan di APP_ENV=local.');
            return;
        }

        $this->call(ProdiSeeder::class);

        $password = env('DEMO_PASSWORD') ?: Str::random(10);
        $created = [];

        // ---- konsultan ----
        $consultants = [];
        foreach ([
            ['Raka Saputra', 'raka.konsultan@temuedu.test', 'Akuntansi', 'Akuntansi Keuangan, Perpajakan', 'Universitas Negeri Jakarta', 3.90, 30000],
            ['Adit Pratama', 'adit.konsultan@temuedu.test', 'Manajemen', 'Manajemen Bisnis, Kewirausahaan', 'Universitas Indonesia', 3.85, 25000],
        ] as [$nama, $email, $prodi, $kompetensi, $alumni, $ipk, $harga]) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $user = new User();
                $user->forceFill([
                    'name' => $nama, 'email' => $email, 'password' => $password,
                    'role' => 'konsultan', 'email_verified_at' => now(),
                ])->save();
                $created[] = $email;
            }

            $consultants[$prodi] = Consultant::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'prodi_id' => Prodi::where('nama_prodi', $prodi)->firstOrFail()->id,
                    'kompetensi' => $kompetensi, 'alumni' => $alumni, 'ipk' => $ipk,
                    'harga' => $harga, 'domisili' => 'Jakarta', 'is_active' => true,
                ]
            );
        }

        // ---- mahasiswa ----
        $student = User::where('email', 'mahasiswa.demo@temuedu.test')->first();
        if (! $student) {
            $student = new User();
            $student->forceFill([
                'name' => 'Mahasiswa Demo', 'email' => 'mahasiswa.demo@temuedu.test',
                'password' => $password, 'role' => 'user', 'email_verified_at' => now(),
            ])->save();
            $created[] = 'mahasiswa.demo@temuedu.test';
        }

        // ---- jam layanan (ketersediaan) Senin-Minggu 00:00-23:59 agar mudah dites kapan saja ----
        foreach ($consultants as $c) {
            if (! $c->availabilities()->exists()) {
                for ($day = 1; $day <= 7; $day++) {
                    $c->availabilities()->create([
                        'day_of_week' => $day, 'start_time' => '00:00:00', 'end_time' => '23:59:00', 'is_active' => true,
                    ]);
                }
            }
        }

        // ---- sesi Chat contoh (sudah dibayar) ----
        $raka = $consultants['Akuntansi'];

        if (! Consultation::where('user_id', $student->id)->where('consultant_id', $raka->id)->exists()) {
            $consultation = Consultation::create([
                'user_id' => $student->id, 'consultant_id' => $raka->id, 'prodi_id' => $raka->prodi_id,
                'service' => 'chat', 'is_paid' => true,
                'harga' => config('temuedu.services.chat.price'), 'status' => 'active',
                'topik' => 'Bingung menyusun laporan arus kas',
                'started_at' => now()->subMinutes(5), 'expires_at' => now()->addMinutes(55),
            ]);

            Payment::create([
                'order_id' => 'DEMO-' . strtoupper(Str::random(8)), 'user_id' => $student->id,
                'consultant_id' => $raka->id, 'consultation_id' => $consultation->id,
                'service' => 'chat', 'amount' => config('temuedu.services.chat.price'),
                'status' => 'paid', 'payment_type' => 'demo', 'paid_at' => now()->subMinutes(6),
            ]);

            foreach ([
                [$student->id, 'text', 'Halo Kak, saya bingung menyusun laporan arus kas untuk tugas akuntansi.'],
                [$raka->user_id, 'text', 'Halo! Boleh ceritakan bagian mana yang paling membingungkan?'],
                [$student->id, 'text', 'Saya belum paham bedanya arus kas operasi dan investasi.'],
            ] as [$sender, $tipe, $isi]) {
                Message::create(['consultation_id' => $consultation->id, 'sender_id' => $sender, 'tipe' => $tipe, 'isi' => $isi]);
            }
        }

        // ---- Online Meet contoh (sudah dibayar, menunggu konsultan mengirim link) ----
        $adit = $consultants['Manajemen'];

        if (! Consultation::where('user_id', $student->id)->where('consultant_id', $adit->id)->exists()) {
            $minutes = (int) config('temuedu.services.online_meet.minutes');
            $start = today()->addDay()->setTime(10, 0);

            $block = ConsultantSchedule::updateOrCreate(
                ['consultant_id' => $adit->id, 'tanggal' => $start->format('Y-m-d'), 'jam_mulai' => '10:00:00'],
                ['jam_selesai' => $start->copy()->addMinutes($minutes)->format('H:i:s'), 'is_booked' => true]
            );

            $consultation = Consultation::create([
                'user_id' => $student->id, 'consultant_id' => $adit->id, 'prodi_id' => $adit->prodi_id,
                'service' => 'online_meet', 'is_paid' => true, 'topik' => 'Online Meet',
                'harga' => config('temuedu.services.online_meet.price'), 'status' => 'active',
            ]);

            $appointment = Appointment::create([
                'consultation_id' => $consultation->id, 'user_id' => $student->id, 'consultant_id' => $adit->id,
                'schedule_id' => $block->id, 'tipe' => 'online_meet', 'catatan' => 'Ingin membahas strategi pemasaran.',
                'status' => 'pending', 'payment_status' => 'paid', 'duration_minutes' => $minutes,
                'harga' => config('temuedu.services.online_meet.price'),
            ]);

            Payment::create([
                'order_id' => 'DEMO-' . strtoupper(Str::random(8)), 'user_id' => $student->id,
                'consultant_id' => $adit->id, 'consultation_id' => $consultation->id, 'appointment_id' => $appointment->id,
                'service' => 'online_meet', 'amount' => config('temuedu.services.online_meet.price'),
                'status' => 'paid', 'payment_type' => 'demo', 'paid_at' => now(),
            ]);

            Message::create([
                'consultation_id' => $consultation->id, 'sender_id' => $student->id, 'tipe' => 'system',
                'isi' => 'Pembayaran Online Meet berhasil. Jadwal: ' . $start->translatedFormat('l, d M Y') . ' pukul 10:00-11:30. Menunggu konsultan mengirim link meeting.',
            ]);
        }

        if ($created) {
            $this->command?->info('Akun demo dibuat (password sama untuk semua, catat sekarang): ' . $password);
            foreach ($created as $email) {
                $this->command?->line(' - ' . $email);
            }
        } else {
            $this->command?->info('Data demo sudah ada, tidak ada akun baru dibuat.');
        }
    }
}
