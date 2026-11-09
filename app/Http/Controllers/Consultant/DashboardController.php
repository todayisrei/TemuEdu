<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Services\AvailabilityService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(AvailabilityService $availability, PaymentService $payments)
    {
        $user = Auth::user();
        $consultant = $user->consultant()->with('prodi')->firstOrFail();

        $payments->releaseExpiredHolds();
        Consultation::expireOverdue($consultant->id);

        // Hanya sesi milik konsultan ini yang sudah ada pesannya.
        // Filter dilakukan di QUERY (bukan di Blade).
        $mine = fn () => Consultation::where('consultant_id', $consultant->id)->whereHas('messages');

        $stats = [
            'total' => $mine()->count(),
            // janji meet yang menunggu persetujuan konsultan (online: sudah dibayar; offline: pengajuan)
            'menunggu' => Appointment::visibleToConsultant()
                ->where('consultant_id', $consultant->id)->where('status', 'pending')->count(),
            'hari_ini' => $mine()->whereDate('started_at', today())->count(),
            'selesai' => $mine()->whereIn('status', ['closed', 'expired'])->count(),
        ];

        $latest = $mine()
            ->with(['user', 'prodi'])
            ->withCount(['messages as unread_count' => fn ($q) => $q
                ->where('tipe', 'text')->where('sender_id', '!=', $user->id)->whereNull('read_at')])
            ->withExists(['messages as replied' => fn ($q) => $q->where('sender_id', $user->id)->where('tipe', 'text')])
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // Janji meet hari ini (menunggu / disetujui)
        $todayAppointments = Appointment::with(['user', 'schedule', 'consultation'])
            ->visibleToConsultant()
            ->where('consultant_id', $consultant->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('tanggal', today()))
            ->get()
            ->sortBy(fn ($a) => $a->schedule->jam_mulai)
            ->values();

        $availableNow = $availability->isAvailableNow($consultant);
        $next = $availableNow ? null : $availability->nextWindow($consultant);
        $hasSchedule = $consultant->availabilities()->where('is_active', true)->exists();

        return view('consultant.dashboard', compact(
            'user', 'consultant', 'stats', 'latest', 'todayAppointments', 'availableNow', 'next', 'hasSchedule'
        ));
    }
}
