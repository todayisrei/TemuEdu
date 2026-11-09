<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Pembungkus tipis untuk Midtrans (Snap + Status API) memakai HTTP client Laravel.
 * Server Key hanya dibaca dari config/.env dan TIDAK PERNAH dikirim ke browser.
 */
class MidtransService
{
    public function isConfigured(): bool
    {
        return filled(config('midtrans.server_key')) && filled(config('midtrans.client_key'));
    }

    public function clientKey(): string
    {
        return (string) config('midtrans.client_key');
    }

    /** URL snap.js (untuk popup pembayaran di browser). */
    public function snapJsUrl(): string
    {
        return $this->appBase() . '/snap/snap.js';
    }

    /** Buat transaksi Snap dan kembalikan snap token. */
    public function createSnapToken(Payment $payment, User $user, string $itemName): string
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $payment->order_id,
                'gross_amount' => (int) $payment->amount,   // nominal dari server, bukan dari browser
            ],
            'item_details' => [[
                'id' => $payment->service,
                'price' => (int) $payment->amount,
                'quantity' => 1,
                'name' => Str::limit($itemName, 50, ''),
            ]],
            'customer_details' => [
                'first_name' => Str::limit($user->name, 50, ''),
                'email' => $user->email,
            ],
            'expiry' => [
                'unit' => 'minutes',
                'duration' => (int) config('temuedu.payment_hold_minutes', 30),
            ],
        ];

        $response = $this->send(fn (PendingRequest $http) => $http->post($this->appBase() . '/snap/v1/transactions', $payload));

        $token = $response->json('token');

        if (! $response->successful() || ! $token) {
            $reason = $response->json('error_messages.0') ?? ('HTTP ' . $response->status());
            throw new RuntimeException('Midtrans menolak pembuatan transaksi: ' . $reason);
        }

        return $token;
    }

    /** Status transaksi langsung dari Midtrans (server-ke-server). null jika transaksi belum ada. */
    public function fetchStatus(string $orderId): ?array
    {
        $response = $this->send(fn (PendingRequest $http) => $http->get($this->apiBase() . '/v2/' . urlencode($orderId) . '/status'));

        $data = $response->json();

        if (! is_array($data) || ! isset($data['transaction_status'])) {
            return null;
        }

        return $data;
    }

    /** Verifikasi signature notifikasi: sha512(order_id + status_code + gross_amount + server_key). */
    public function validSignature(array $n): bool
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key'] as $key) {
            if (! isset($n[$key])) {
                return false;
            }
        }

        $expected = hash('sha512', $n['order_id'] . $n['status_code'] . $n['gross_amount'] . $this->serverKey());

        return hash_equals($expected, (string) $n['signature_key']);
    }

    private function send(callable $call)
    {
        try {
            return $call($this->http());
        } catch (ConnectionException $e) {
            $detail = config('app.debug') ? ' (' . Str::limit($e->getMessage(), 160) . ')' : '';
            throw new RuntimeException('Tidak dapat terhubung ke Midtrans. Periksa koneksi internet atau pengaturan SSL.' . $detail);
        }
    }

    private function http(): PendingRequest
    {
        return Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->asJson()
            ->timeout(20)
            ->withOptions(['verify' => (bool) config('midtrans.verify_ssl', true)]);
    }

    private function serverKey(): string
    {
        $key = config('midtrans.server_key');

        if (blank($key)) {
            throw new RuntimeException('Pembayaran belum dikonfigurasi (MIDTRANS_SERVER_KEY belum diisi di file .env).');
        }

        return (string) $key;
    }

    private function appBase(): string
    {
        return config('midtrans.is_production') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
    }

    private function apiBase(): string
    {
        return config('midtrans.is_production') ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
    }
}
