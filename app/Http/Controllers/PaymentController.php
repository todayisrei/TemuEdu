<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Services\MidtransService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $payments,
        private MidtransService $midtrans,
    ) {
    }

    /** Halaman ringkasan + popup Midtrans Snap. */
    public function show($orderId)
    {
        $payment = $this->mine($orderId);
        $this->payments->releaseExpiredHolds();
        $payment->refresh();

        if ($payment->isPaid()) {
            return redirect($this->target($payment))->with('success', 'Pembayaran sudah berhasil.');
        }

        $payment->load(['consultant.user', 'appointment.schedule']);

        $simulation = $this->payments->isSimulation();

        return view('payment.show', [
            'payment' => $payment,
            'service' => config('temuedu.services.' . $payment->service),
            'simulation' => $simulation,
            'methods' => config('temuedu.simulation_methods'),
            'banks' => config('temuedu.simulation_banks'),
            'wallets' => config('temuedu.simulation_wallets'),
            'vaNumbers' => $simulation ? $this->virtualAccounts($payment) : [],
            'clientKey' => $simulation ? '' : $this->midtrans->clientKey(),
            'snapUrl' => $this->midtrans->snapJsUrl(),
            'configured' => $simulation || $this->midtrans->isConfigured(),
        ]);
    }

    /** Mode simulasi: klik Bayar -> langsung lunas -> layanan aktif. */
    public function simulate(Request $request, $orderId)
    {
        abort_unless($this->payments->isSimulation(), 404);

        $payment = $this->mine($orderId);

        $data = $request->validate([
            'method' => ['required', 'in:' . implode(',', array_keys(config('temuedu.simulation_methods')))],
            'channel' => ['nullable', 'in:' . implode(',', array_merge(
                array_keys(config('temuedu.simulation_banks')),
                array_keys(config('temuedu.simulation_wallets'))
            ))],
        ], [
            'method.required' => 'Pilih metode pembayaran terlebih dahulu.',
            'method.in' => 'Metode pembayaran tidak valid.',
            'channel.in' => 'Pilihan bank / e-wallet tidak valid.',
        ]);

        $this->payments->releaseExpiredHolds();
        $payment = $this->payments->simulatePayment($payment->refresh(), $data['method'], $data['channel'] ?? null);

        if ($payment->isPaid()) {
            return redirect($this->target($payment))->with('success', 'Pembayaran berhasil. Layanan sudah aktif.');
        }

        return redirect()->route('consultation.history')
            ->with('error', 'Pembayaran tidak dapat diproses (' . strtolower($payment->label()) . ').');
    }

    /**
     * Dipanggil setelah popup ditutup / selesai. Status diperiksa langsung ke Midtrans (server-ke-server),
     * jadi tidak bergantung pada apa yang dikirim browser dan tetap jalan di localhost tanpa webhook.
     */
    public function finish($orderId)
    {
        $payment = $this->mine($orderId);
        $payment = $this->payments->sync($payment);

        if ($payment->isPaid()) {
            return redirect($this->target($payment))->with('success', 'Pembayaran berhasil. Layanan sudah aktif.');
        }

        if ($payment->isPending() && $payment->isPayable()) {
            return redirect()->route('payment.show', $payment->order_id)
                ->with('error', 'Pembayaran belum selesai. Selesaikan pembayaran lalu klik "Cek status pembayaran".');
        }

        return redirect()->route('consultation.history')
            ->with('error', 'Pembayaran ' . strtolower($payment->label()) . '. Silakan ulangi dari halaman Konsultasi Saya.');
    }

    /** Bayar janji yang sudah ada (Offline Meet yang disetujui / Online Meet yang ditahan). */
    public function payAppointment($appointmentId)
    {
        $user = Auth::user();

        $appointment = Appointment::with('consultation')
            ->where('user_id', $user->id)       // hanya janji milik sendiri
            ->findOrFail($appointmentId);

        $allowed = $appointment->payment_status === 'unpaid' && (
            ($appointment->tipe === 'offline_meet' && $appointment->status === 'approved') ||
            ($appointment->tipe === 'online_meet' && $appointment->status === 'pending')
        );

        if (! $allowed) {
            return back()->with('error', 'Janji ini tidak dapat dibayar saat ini.');
        }

        try {
            $payment = $this->payments->forAppointment($appointment, $user);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Pembayaran tidak dapat diproses saat ini. Silakan coba lagi.');
        }

        return redirect()->route('payment.show', $payment->order_id);
    }

    /** Webhook Midtrans (publik, tanpa login). Signature diverifikasi di PaymentService. */
    public function notification(Request $request)
    {
        if ($this->payments->isSimulation()) {
            return response('simulation mode', 200);   // webhook diabaikan saat simulasi
        }

        $data = $request->json()->all() ?: $request->all();

        $payment = $this->payments->applyNotification($data, true);

        if (! $payment) {
            return response('invalid', 403);
        }

        return response('OK', 200);
    }

    /** Nomor VA CONTOH per bank (stabil untuk satu order, bukan nomor asli). */
    private function virtualAccounts(Payment $payment): array
    {
        $out = [];

        foreach (config('temuedu.simulation_banks') as $key => $bank) {
            $length = 16 - strlen($bank['prefix']);
            $digits = str_pad((string) (crc32($payment->order_id . $key) % (10 ** $length)), $length, '0', STR_PAD_LEFT);
            $out[$key] = $bank['prefix'] . $digits;
        }

        return $out;
    }

    private function mine($orderId): Payment
    {
        return Payment::where('order_id', $orderId)->where('user_id', Auth::id())->firstOrFail();
    }

    private function target(Payment $payment): string
    {
        return $payment->consultation_id
            ? route('consultation.room', $payment->consultation_id)
            : route('consultation.history');
    }
}
