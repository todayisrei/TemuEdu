<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\Consultation;
use App\Models\Message;
use App\Models\Prodi;
use App\Services\AvailabilityService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    /** Ikon & deskripsi tampilan per prodi (data tampilan saja; nama prodi dari database). */
    private const META = [
        'Akuntansi' => ['fa-calculator', 'Konsultasi seputar akuntansi dan keuangan.'],
        'Manajemen' => ['fa-chart-line', 'Diskusi tentang manajemen dan bisnis.'],
        'Bisnis Digital' => ['fa-laptop', 'Konsultasi seputar bisnis digital dan teknologi.'],
        'Ilmu Ekonomi' => ['fa-coins', 'Diskusi mengenai teori dan ilmu ekonomi.'],
        'Ekonomi Islam / Syariah' => ['fa-mosque', 'Konsultasi seputar ekonomi Islam dan syariah.'],
    ];

    /** Halaman pilih program studi (dari tabel prodis). */
    public function prodi()
    {
        $prodis = Prodi::orderBy('id')->get()->map(function ($p) {
            $meta = self::META[$p->nama_prodi] ?? ['fa-book-open', 'Konsultasi seputar ' . $p->nama_prodi . '.'];

            return [
                'id' => $p->id,
                'nama' => $p->nama_prodi,
                'icon' => $meta[0],
                'deskripsi' => $meta[1],
            ];
        })->all();

        return view('consultation.prodi', compact('prodis'));
    }

    /** Daftar konsultan AKTIF pada satu prodi + status ketersediaan + pilihan layanan. */
    public function consultants($prodiId, AvailabilityService $availability)
    {
        $prodi = Prodi::findOrFail($prodiId);
        $namaProdi = $prodi->nama_prodi;

        $filteredConsultants = Consultant::with(['user', 'prodi'])
            ->where('prodi_id', $prodi->id)
            ->where('is_active', true)
            ->orderByDesc('rating_avg')
            ->orderBy('id')
            ->get()
            ->map(function ($c) use ($availability) {
                $info = $availability->summaryFor($c);

                return [
                    'id' => $c->id,
                    'nama' => $c->user->name,
                    'foto' => $c->avatar_url,
                    'prodi' => $c->prodi->nama_prodi,
                    'kompetensi' => $c->kompetensi ?: '-',
                    'alumni' => $c->alumni ?: '-',
                    'ipk' => $c->ipk ?? '-',
                    'rating' => $c->rating_count > 0 ? $c->rating_avg : 'Baru',
                    'tersedia' => $info['now'],            // sedang di dalam jam layanan?
                    'berikutnya' => $info['next'],         // jam layanan berikutnya (jika sedang tidak tersedia)
                    'jadwal' => $info['weekly'],           // ringkasan jam layanan mingguan
                    'portofolio' => $c->portofolio_link ?: '#',
                ];
            })
            ->all();

        $services = config('temuedu.services');

        return view('consultation.consultants', compact('filteredConsultants', 'namaProdi', 'prodiId', 'services'));
    }

    /** Konsultasi Saya: semua sesi mahasiswa + status pembayaran + pesan belum dibaca per sesi. */
    public function history(PaymentService $payments)
    {
        $user = Auth::user();

        $payments->releaseExpiredHolds();
        Consultation::expireOverdue();

        $consultations = Consultation::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereHas('messages')
                    ->orWhereHas('appointments')
                    ->orWhereHas('payments', fn ($p) => $p->where('status', 'pending'));
            })
            ->with(['consultant.user', 'prodi', 'appointments.schedule', 'payments'])
            ->withCount(['messages as unread_count' => fn ($q) => $q
                ->where('tipe', 'text')->where('sender_id', '!=', $user->id)->whereNull('read_at')])
            ->orderByDesc('id')
            ->paginate(10);

        foreach ($consultations as $c) {
            $c->lastMessage = $c->messages()->where('tipe', 'text')->latest('id')->first();
            $c->openPayment = $c->payments->where('status', 'pending')->filter(fn ($p) => $p->isPayable() && $p->snap_token)->sortByDesc('id')->first();
        }

        $totalUnread = Message::unreadFor($user)->count();

        return view('consultation.history', compact('consultations', 'totalUnread'));
    }

    /**
     * Rute lama "mulai chat" (GET). TIDAK lagi membuat sesi gratis:
     * hanya melanjutkan sesi yang sudah dibayar; selain itu kembali memilih layanan.
     */
    public function chat($consultantId)
    {
        $consultant = Consultant::where('is_active', true)->findOrFail($consultantId);
        $user = Auth::user();

        Consultation::expireOverdue();

        $consultation = Consultation::where('user_id', $user->id)
            ->where('consultant_id', $consultant->id)
            ->where('status', 'active')
            ->accessible()
            ->latest('id')
            ->first();

        if ($consultation && $consultation->isChatOpen()) {
            return redirect()->route('consultation.room', $consultation);
        }

        return redirect()->route('consultation.consultants', $consultant->prodi_id)
            ->with('error', 'Pilih layanan konsultasi terlebih dahulu (Chat, Online Meet, atau Offline Meet).');
    }
}
