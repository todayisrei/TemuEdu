<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\Consultant;
use App\Models\Consultation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Konsultasi milik konsultan yang sedang login.
 * Semua query dibatasi consultant_id = konsultan login (tidak bisa melihat milik konsultan lain).
 *
 * Status yang dipakai (TIDAK ada status baru di database):
 *   consultations.status : active | closed | expired
 *   Label tampilan:
 *     Menunggu    = active & konsultan belum membalas
 *     Berlangsung = active & konsultan sudah membalas
 *     Selesai     = closed / expired
 */
class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $me = $this->me();
        Consultation::expireOverdue($me->id);

        $status = $request->query('status');

        $consultations = Consultation::query()
            ->where('consultant_id', $me->id)
            ->whereHas('messages')
            ->with(['user', 'prodi', 'appointments'])
            ->withCount(['messages as unread_count' => fn ($q) => $q
                ->where('tipe', 'text')->where('sender_id', '!=', Auth::id())->whereNull('read_at')])
            ->withExists(['messages as replied' => fn ($q) => $q->where('sender_id', Auth::id())->where('tipe', 'text')])
            ->when($request->filled('q'), fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->q . '%')))
            ->when($status === 'menunggu', fn ($q) => $q->where('status', 'active')
                ->whereDoesntHave('messages', fn ($m) => $m->where('sender_id', Auth::id())->where('tipe', 'text')))
            ->when($status === 'berlangsung', fn ($q) => $q->where('status', 'active')
                ->whereHas('messages', fn ($m) => $m->where('sender_id', Auth::id())->where('tipe', 'text')))
            ->when($status === 'selesai', fn ($q) => $q->whereIn('status', ['closed', 'expired']))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('consultant.consultations.index', compact('consultations', 'status'));
    }

    public function show($id)
    {
        $me = $this->me();
        Consultation::expireOverdue($me->id);

        // findOrFail pada query yang sudah dibatasi -> konsultasi konsultan lain = 404
        $consultation = Consultation::where('consultant_id', $me->id)
            ->accessible()
            ->with(['user', 'prodi', 'appointments.schedule'])
            ->findOrFail($id);

        // Online Meet yang belum dibayar tidak ditampilkan ke konsultan
        $consultation->setRelation('appointments', $consultation->appointments
            ->filter(fn ($a) => $a->tipe === 'offline_meet' || $a->payment_status === 'paid')->values());

        $firstQuestion = $consultation->messages()
            ->where('tipe', 'text')->where('sender_id', $consultation->user_id)
            ->orderBy('id')->first();

        $recent = $consultation->messages()->where('tipe', 'text')->orderByDesc('id')->take(5)->get()->reverse()->values();

        $consultation->replied = $consultation->messages()->where('tipe', 'text')->where('sender_id', Auth::id())->exists();

        return view('consultant.consultations.show', compact('consultation', 'firstQuestion', 'recent'));
    }

    /** Akhiri sesi chat dari halaman detail. */
    public function close($id)
    {
        $me = $this->me();

        $consultation = Consultation::where('consultant_id', $me->id)->accessible()->findOrFail($id);
        $consultation->expireIfNeeded();

        if ($consultation->status === 'active') {
            $consultation->update(['status' => 'closed']);

            Message::create([
                'consultation_id' => $consultation->id,
                'sender_id' => Auth::id(),
                'tipe' => 'system',
                'isi' => 'Sesi konsultasi diakhiri oleh konsultan.',
            ]);
        }

        return redirect()->route('consultant.consultations.show', $consultation->id)
            ->with('success', 'Sesi konsultasi telah diakhiri.');
    }

    private function me(): Consultant
    {
        return Auth::user()->consultant ?? abort(403, 'Profil konsultan tidak ditemukan.');
    }
}
