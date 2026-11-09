<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultant;
use App\Models\Consultation;
use App\Models\Payment;
use App\Services\AvailabilityService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Pemilihan layanan oleh mahasiswa: Chat, Online Meet, Offline Meet.
 * Harga & durasi selalu dari config('temuedu.services') (server).
 */
class ServiceController extends Controller
{
    public function __construct(
        private AvailabilityService $availability,
        private PaymentService $payments,
    ) {
    }

    // ------------------------------------------------------------------ CHAT

    public function chat($consultantId)
    {
        $consultant = $this->consultant($consultantId);
        $user = Auth::user();

        $this->payments->releaseExpiredHolds();
        Consultation::expireOverdue();

        // 1) masih punya sesi chat berbayar yang berjalan -> masuk saja (tidak bayar dua kali)
        // "Sesi berbayar yang berjalan" = sudah ada pembayaran lunas lewat Midtrans, ATAU sesi lama
        // (sebelum fitur pembayaran) yang timernya benar-benar sudah berjalan. Sesi lama yang belum
        // pernah dipakai TIDAK dihitung, supaya mahasiswa tetap bisa membeli sesi baru.
        $running = Consultation::where('user_id', $user->id)
            ->where('consultant_id', $consultant->id)
            ->where('service', 'chat')->where('is_paid', true)->where('status', 'active')
            ->where(function ($q) {
                $q->whereNotNull('started_at')
                    ->orWhereHas('payments', fn ($p) => $p->where('status', 'paid'));
            })
            ->latest('id')->first();

        if ($running && $running->isChatOpen()) {
            return redirect()->route('consultation.room', $running)
                ->with('success', 'Kamu masih punya sesi chat yang aktif dengan konsultan ini.');
        }

        // 2) ada pembayaran yang belum selesai -> lanjutkan
        $unpaid = Consultation::where('user_id', $user->id)
            ->where('consultant_id', $consultant->id)
            ->where('service', 'chat')->where('is_paid', false)->where('status', 'active')
            ->latest('id')->first();

        if ($unpaid) {
            $open = Payment::where('consultation_id', $unpaid->id)->where('status', 'pending')->latest('id')->first();

            if ($open && $open->snap_token && $open->isPayable()) {
                return redirect()->route('payment.show', $open->order_id);
            }
        }

        // 3) konsultan harus sedang berada di jam layanan
        if (! $this->availability->isAvailableNow($consultant)) {
            return back()->with('error', $this->unavailableMessage($consultant));
        }

        $consultation = $unpaid ?: Consultation::create([
            'user_id' => $user->id,
            'consultant_id' => $consultant->id,
            'prodi_id' => $consultant->prodi_id,
            'service' => 'chat',
            'is_paid' => false,                                   // terkunci sampai lunas
            'harga' => $this->payments->priceFor('chat'),
            'status' => 'active',
        ]);

        return $this->toPayment(fn () => $this->payments->forConsultation($consultation, $user));
    }

    // ----------------------------------------------------------- ONLINE MEET

    /** Langkah 1: pilih tanggal & jam dari jam layanan konsultan. */
    public function onlineSchedule(Request $request, $consultantId)
    {
        $consultant = $this->consultant($consultantId);
        $this->payments->releaseExpiredHolds();

        $minutes = (int) config('temuedu.services.online_meet.minutes');
        $ahead = (int) config('temuedu.booking_days_ahead', 14);

        $days = [];
        for ($i = 0; $i < $ahead; $i++) {
            $date = today()->addDays($i);
            $count = count($this->availability->startOptions($consultant, $date, $minutes));

            if ($count > 0) {
                $days[] = ['date' => $date, 'count' => $count];
            }
        }

        $selected = null;
        $wanted = (string) $request->query('date');
        foreach ($days as $d) {
            if ($d['date']->format('Y-m-d') === $wanted) {
                $selected = $d['date'];
            }
        }
        $selected ??= $days[0]['date'] ?? null;

        $options = $selected ? $this->availability->startOptions($consultant, $selected, $minutes) : [];
        $summary = $this->availability->summaryFor($consultant);

        return view('service.online-schedule', compact('consultant', 'days', 'selected', 'options', 'minutes', 'summary'));
    }

    /** Langkah 2: tahan jadwal + buat tagihan, lalu ke pembayaran. */
    public function onlineCheckout(Request $request, $consultantId)
    {
        $consultant = $this->consultant($consultantId);
        $user = Auth::user();

        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'catatan' => ['nullable', 'string', 'max:300'],
        ], [
            'date.required' => 'Pilih tanggal terlebih dahulu.',
            'time.required' => 'Pilih jam terlebih dahulu.',
        ]);

        $this->payments->releaseExpiredHolds();

        // sudah menahan jadwal yang belum dibayar? lanjutkan pembayaran itu saja
        $hold = Appointment::where('user_id', $user->id)->where('consultant_id', $consultant->id)
            ->where('tipe', 'online_meet')->where('payment_status', 'unpaid')->where('status', 'pending')
            ->latest('id')->first();

        if ($hold) {
            $open = Payment::where('appointment_id', $hold->id)->where('status', 'pending')->latest('id')->first();

            if ($open && $open->snap_token && $open->isPayable()) {
                return redirect()->route('payment.show', $open->order_id)
                    ->with('success', 'Kamu masih punya jadwal yang menunggu pembayaran.');
            }
        }

        $minutes = (int) config('temuedu.services.online_meet.minutes');
        $start = Carbon::createFromFormat('Y-m-d H:i', $data['date'] . ' ' . $data['time']);

        $result = DB::transaction(function () use ($consultant, $user, $start, $minutes, $data) {
            // kunci baris konsultan agar dua mahasiswa tidak memesan jam yang sama bersamaan
            Consultant::whereKey($consultant->id)->lockForUpdate()->first();

            if ($error = $this->availability->validateBooking($consultant, $start, $minutes)) {
                return ['error' => $error];
            }

            $block = $this->availability->reserve($consultant, $start, $minutes);

            $consultation = Consultation::create([
                'user_id' => $user->id,
                'consultant_id' => $consultant->id,
                'prodi_id' => $consultant->prodi_id,
                'service' => 'online_meet',
                'is_paid' => false,
                'topik' => 'Online Meet',
                'harga' => $this->payments->priceFor('online_meet'),
                'status' => 'active',
            ]);

            $appointment = Appointment::create([
                'consultation_id' => $consultation->id,
                'user_id' => $user->id,
                'consultant_id' => $consultant->id,
                'schedule_id' => $block->id,
                'tipe' => 'online_meet',
                'catatan' => $data['catatan'] ?? null,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'duration_minutes' => $minutes,
                'harga' => $this->payments->priceFor('online_meet'),
            ]);

            $payment = $this->payments->createRecord($user, $consultant->id, 'online_meet', $consultation, $appointment);

            return compact('payment');
        });

        if (isset($result['error'])) {
            return back()->withInput()->with('error', $result['error']);
        }

        return $this->toPayment(fn () => $this->payments->attachToken($result['payment'], $user));
    }

    // ---------------------------------------------------------- OFFLINE MEET

    /** Mulai koordinasi lewat chat. Pembayaran dilakukan setelah waktu & lokasi disepakati. */
    public function offline($consultantId)
    {
        $consultant = $this->consultant($consultantId);
        $user = Auth::user();

        Consultation::expireOverdue();

        $existing = Consultation::where('user_id', $user->id)
            ->where('consultant_id', $consultant->id)
            ->where('service', 'offline_meet')->where('status', 'active')
            ->latest('id')->first();

        if ($existing && $existing->isChatOpen()) {
            return redirect()->route('consultation.room', $existing);
        }

        // batasi agar chat koordinasi tidak dipakai berulang sebagai chat gratis
        $recent = Consultation::where('user_id', $user->id)->where('consultant_id', $consultant->id)
            ->where('service', 'offline_meet')->where('created_at', '>=', now()->subDay())->count();

        if ($recent >= 2) {
            return back()->with('error', 'Pengajuan Offline Meet dengan konsultan ini sudah dibuat dua kali hari ini. Silakan coba lagi besok.');
        }

        $consultation = Consultation::create([
            'user_id' => $user->id,
            'consultant_id' => $consultant->id,
            'prodi_id' => $consultant->prodi_id,
            'service' => 'offline_meet',
            'is_paid' => false,
            'topik' => 'Offline Meet',
            'harga' => $this->payments->priceFor('offline_meet'),
            'status' => 'active',
        ]);

        return redirect()->route('consultation.room', $consultation)
            ->with('success', 'Diskusikan waktu & lokasi pertemuan di chat, lalu ajukan pertemuan lewat tombol Janji Meet. Pembayaran dilakukan setelah konsultan menyetujui.');
    }

    // --------------------------------------------------------------- helper

    private function consultant($id): Consultant
    {
        return Consultant::with('user')->where('is_active', true)->findOrFail($id);
    }

    private function unavailableMessage(Consultant $consultant): string
    {
        $next = $this->availability->nextWindow($consultant);

        return 'Konsultan sedang di luar jam layanan.'
            . ($next ? ' Kamu bisa kembali pada ' . $next['label'] . '.' : ' Konsultan belum mengatur jadwal layanan.');
    }

    /** Jalankan pembuatan tagihan; tangani kegagalan Midtrans dengan pesan yang jelas. */
    private function toPayment(callable $make)
    {
        try {
            $payment = $make();
        } catch (RuntimeException $e) {
            return redirect()->route('consultation.history')->with('error', $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('consultation.history')->with('error', 'Pembayaran tidak dapat diproses saat ini. Silakan coba lagi.');
        }

        return redirect()->route('payment.show', $payment->order_id);
    }
}
