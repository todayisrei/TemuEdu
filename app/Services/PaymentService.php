<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Message;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Alur pembayaran layanan TemuEdu.
 *
 * Aturan keamanan:
 *  - nominal selalu dari config('temuedu.services.*.price') (server), bukan dari request
 *  - layanan baru aktif setelah Midtrans menyatakan LUNAS (settlement / capture-accept)
 *  - status hanya diubah oleh: (1) notifikasi Midtrans yang signature-nya valid, atau
 *    (2) pengecekan status server-ke-server memakai Server Key
 */
class PaymentService
{
    public function __construct(private MidtransService $midtrans)
    {
    }

    public function priceFor(string $service): int
    {
        return (int) config("temuedu.services.{$service}.price");
    }

    public function labelFor(string $service): string
    {
        return (string) config("temuedu.services.{$service}.label", $service);
    }

    /** Mode simulasi: bayar = langsung lunas (tanpa Midtrans). Tidak pernah aktif di production. */
    public function isSimulation(): bool
    {
        return config('temuedu.payment_mode') === 'simulation' && ! app()->environment('production');
    }

    /** Tandai lunas (mode simulasi) lalu aktifkan layanan, sama seperti pembayaran Midtrans yang berhasil. */
    public function simulatePayment(Payment $payment, string $method): Payment
    {
        if (! $this->isSimulation()) {
            throw new \RuntimeException('Mode simulasi tidak aktif.');
        }

        return DB::transaction(function () use ($payment, $method) {
            $p = Payment::lockForUpdate()->findOrFail($payment->id);

            if (! $p->isPayable()) {
                return $p;   // sudah lunas / kedaluwarsa / dibatalkan
            }

            $p->fill([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_type' => 'simulasi-' . $method,
                'transaction_status' => 'settlement',
                'fraud_status' => 'accept',
                'transaction_id' => 'SIM-' . strtoupper(Str::random(10)),
            ]);

            $this->fulfil($p);
            $p->save();

            return $p;
        });
    }

    /** Baris pembayaran (belum ada token). Panggil di dalam transaksi database. */
    public function createRecord(User $user, int $consultantId, string $service, ?Consultation $consultation, ?Appointment $appointment): Payment
    {
        return Payment::create([
            'order_id' => sprintf('TEMU-%s-%s', now()->format('ymdHis'), strtoupper(Str::random(6))),
            'user_id' => $user->id,
            'consultant_id' => $consultantId,
            'consultation_id' => $consultation?->id,
            'appointment_id' => $appointment?->id,
            'service' => $service,
            'amount' => $this->priceFor($service),
            'status' => 'pending',
            'expires_at' => now()->addMinutes((int) config('temuedu.payment_hold_minutes', 30)),
        ]);
    }

    /**
     * Minta token ke Midtrans. Jika gagal: pembayaran ditandai gagal, penahan jadwal dilepas,
     * lalu exception dilempar (pesan aman untuk ditampilkan).
     */
    public function attachToken(Payment $payment, User $user): Payment
    {
        // Mode simulasi: tidak memanggil Midtrans
        if ($this->isSimulation()) {
            $payment->snap_token = 'SIMULATION';
            $payment->save();

            return $payment;
        }

        try {
            $token = $this->midtrans->createSnapToken(
                $payment,
                $user,
                $this->labelFor($payment->service) . ' TemuEdu'
            );
        } catch (Throwable $e) {
            Log::error('Midtrans createSnapToken gagal', ['order_id' => $payment->order_id, 'error' => $e->getMessage()]);

            DB::transaction(function () use ($payment) {
                $p = Payment::lockForUpdate()->find($payment->id);
                if ($p && $p->status === 'pending') {
                    $p->status = 'failed';
                    $this->release($p);
                    $p->save();
                }
            });

            throw $e;
        }

        $payment->snap_token = $token;
        $payment->save();

        return $payment;
    }

    /** Pembayaran untuk janji (Online Meet yang ditahan / Offline Meet yang sudah disetujui). */
    public function forAppointment(Appointment $appointment, User $user): Payment
    {
        $this->releaseExpiredHolds();

        $existing = Payment::where('appointment_id', $appointment->id)
            ->where('status', 'pending')->latest('id')->first();

        if ($existing && $existing->snap_token && $existing->isPayable()) {
            return $existing;
        }

        $payment = DB::transaction(fn () => $this->createRecord(
            $user, $appointment->consultant_id, $appointment->tipe, $appointment->consultation, $appointment
        ));

        return $this->attachToken($payment, $user);
    }

    /** Pembayaran untuk sesi Chat. */
    public function forConsultation(Consultation $consultation, User $user): Payment
    {
        $this->releaseExpiredHolds();

        $existing = Payment::where('consultation_id', $consultation->id)
            ->whereNull('appointment_id')->where('status', 'pending')->latest('id')->first();

        if ($existing && $existing->snap_token && $existing->isPayable()) {
            return $existing;
        }

        $payment = DB::transaction(fn () => $this->createRecord(
            $user, $consultation->consultant_id, 'chat', $consultation, null
        ));

        return $this->attachToken($payment, $user);
    }

    /** Terapkan status dari Midtrans. $checkSignature=true untuk notifikasi (webhook) dari luar. */
    public function applyNotification(array $data, bool $checkSignature = true): ?Payment
    {
        $orderId = $data['order_id'] ?? null;

        if (! $orderId) {
            return null;
        }

        if ($checkSignature && ! $this->midtrans->validSignature($data)) {
            Log::warning('Notifikasi Midtrans: signature tidak valid', ['order_id' => $orderId]);
            return null;
        }

        return DB::transaction(function () use ($orderId, $data) {
            $payment = Payment::where('order_id', $orderId)->lockForUpdate()->first();

            if (! $payment) {
                return null;
            }

            // nominal harus sama dengan yang kita catat
            $gross = (int) round((float) ($data['gross_amount'] ?? 0));
            if ($gross !== (int) $payment->amount) {
                Log::warning('Notifikasi Midtrans: nominal tidak cocok', ['order_id' => $orderId, 'gross' => $gross, 'amount' => $payment->amount]);
                return $payment;
            }

            $payment->fill([
                'transaction_id' => $data['transaction_id'] ?? $payment->transaction_id,
                'payment_type' => $data['payment_type'] ?? $payment->payment_type,
                'transaction_status' => $data['transaction_status'] ?? $payment->transaction_status,
                'fraud_status' => $data['fraud_status'] ?? $payment->fraud_status,
                'raw_notification' => $data,
            ]);

            // hanya pembayaran yang masih "pending" yang boleh berubah status
            if ($payment->status !== 'pending') {
                $payment->save();
                return $payment;
            }

            $new = $this->mapStatus($data);

            if ($new === 'paid') {
                $payment->status = 'paid';
                $payment->paid_at = now();
                $this->fulfil($payment);
            } elseif (in_array($new, ['expired', 'failed', 'cancelled'], true)) {
                $payment->status = $new;
                $this->release($payment);
            }

            $payment->save();

            return $payment;
        });
    }

    /** Tanyakan status ke Midtrans (dipakai halaman "selesai bayar" karena webhook tidak sampai ke localhost). */
    public function sync(Payment $payment): Payment
    {
        if (! $payment->isPending() || $this->isSimulation()) {
            return $payment;
        }

        try {
            $data = $this->midtrans->fetchStatus($payment->order_id);
        } catch (Throwable $e) {
            Log::warning('Midtrans fetchStatus gagal', ['order_id' => $payment->order_id, 'error' => $e->getMessage()]);
            return $payment;
        }

        if ($data) {
            $this->applyNotification($data + ['order_id' => $payment->order_id], false);
        }

        return $payment->fresh();
    }

    /** Lepas penahan jadwal / sesi dari pembayaran yang melewati batas waktu. */
    public function releaseExpiredHolds(): void
    {
        $due = Payment::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now()->subMinutes(2)) // jeda 2 menit agar notifikasi lunas sempat masuk
            ->pluck('id');

        $this->releaseStaleOfflineRequests();

        foreach ($due as $id) {
            DB::transaction(function () use ($id) {
                $p = Payment::lockForUpdate()->find($id);

                if ($p && $p->status === 'pending') {
                    $p->status = 'expired';
                    $this->release($p);
                    $p->save();
                }
            });
        }
    }

    /** Pengajuan Offline Meet yang tidak ditindaklanjuti >72 jam dibatalkan agar jadwal tidak tertahan selamanya. */
    public function releaseStaleOfflineRequests(): void
    {
        Appointment::with('schedule')
            ->where('tipe', 'offline_meet')
            ->where('payment_status', 'unpaid')
            ->whereIn('status', ['pending', 'approved'])
            ->where('updated_at', '<', now()->subHours(72))
            ->get()
            ->each(function ($a) {
                $a->update(['status' => 'cancelled']);
                $a->schedule?->update(['is_booked' => false]);
            });
    }

    private function mapStatus(array $data): string
    {
        $ts = $data['transaction_status'] ?? '';
        $fraud = $data['fraud_status'] ?? null;

        return match (true) {
            $ts === 'settlement' => 'paid',
            $ts === 'capture' => ($fraud === null || $fraud === 'accept') ? 'paid' : 'pending',
            $ts === 'expire' => 'expired',
            $ts === 'cancel' => 'cancelled',
            in_array($ts, ['deny', 'failure'], true) => 'failed',
            default => 'pending',
        };
    }

    /** Pembayaran lunas -> aktifkan layanan. */
    private function fulfil(Payment $payment): void
    {
        $appointment = $payment->appointment_id
            ? Appointment::with('schedule')->lockForUpdate()->find($payment->appointment_id)
            : null;

        $consultation = $payment->consultation_id
            ? Consultation::lockForUpdate()->find($payment->consultation_id)
            : null;

        if ($payment->service === 'chat' && $consultation) {
            $consultation->update(['is_paid' => true]);
            $this->note($consultation->id, $payment->user_id,
                'Pembayaran Chat berhasil. Silakan mulai bertanya; timer ' . config('temuedu.services.chat.minutes') . ' menit berjalan saat pesan pertama dikirim.');
        }

        if ($payment->service === 'online_meet' && $appointment) {
            $appointment->update(['payment_status' => 'paid']);
            $consultation?->update(['is_paid' => true]);

            if ($consultation) {
                $this->note($consultation->id, $payment->user_id,
                    'Pembayaran Online Meet berhasil. Jadwal: ' . $this->when($appointment) . '. Menunggu konsultan mengirim link meeting.');
            }
        }

        if ($payment->service === 'offline_meet' && $appointment) {
            $appointment->update(['payment_status' => 'paid']);

            if ($consultation) {
                $this->note($consultation->id, $payment->user_id,
                    'Pembayaran Offline Meet berhasil. Pertemuan terkonfirmasi: ' . $this->when($appointment)
                    . ($appointment->lokasi ? ', lokasi: ' . $appointment->lokasi : '') . '.');
            }
        }
    }

    /** Pembayaran gagal/kedaluwarsa -> lepas penahan. */
    private function release(Payment $payment): void
    {
        if ($payment->service === 'chat' && $payment->consultation_id) {
            Consultation::where('id', $payment->consultation_id)->where('is_paid', false)->update(['status' => 'expired']);
        }

        if ($payment->service === 'online_meet' && $payment->appointment_id) {
            $a = Appointment::with('schedule')->find($payment->appointment_id);

            if ($a && $a->payment_status !== 'paid') {
                $a->update(['status' => 'cancelled']);
                $a->schedule?->update(['is_booked' => false]);
                Consultation::where('id', $a->consultation_id)->where('is_paid', false)->update(['status' => 'expired']);
            }
        }

        // Offline Meet: janji tetap "disetujui, belum dibayar" sehingga mahasiswa dapat membayar ulang.
    }

    private function when(Appointment $a): string
    {
        $s = $a->schedule;

        return $s->tanggal->translatedFormat('l, d M Y') . ' pukul ' . substr($s->jam_mulai, 0, 5) . '-' . substr($s->jam_selesai, 0, 5);
    }

    private function note(int $consultationId, int $senderId, string $text): void
    {
        Message::create([
            'consultation_id' => $consultationId,
            'sender_id' => $senderId,
            'tipe' => 'system',
            'isi' => $text,
        ]);
    }
}
