<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultant;
use App\Services\AvailabilityService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Jadwal KETERSEDIAAN konsultan = jam layanan mingguan.
 * Berlaku untuk Chat, Online Meet, dan penentuan Offline Meet (bukan hanya Online Meet).
 */
class ScheduleController extends Controller
{
    public function index(AvailabilityService $availability, PaymentService $payments)
    {
        $me = $this->me();
        $payments->releaseExpiredHolds();

        $windows = $me->availabilities()->orderBy('day_of_week')->orderBy('start_time')->get()->groupBy('day_of_week');

        // Janji meet yang akan datang (online sudah dibayar / offline sedang diproses)
        $appointments = Appointment::with(['user', 'schedule', 'consultation'])
            ->visibleToConsultant()
            ->where('consultant_id', $me->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('tanggal', '>=', today()))
            ->get()
            ->sortBy(fn ($a) => $a->schedule->tanggal->format('Y-m-d') . ' ' . $a->schedule->jam_mulai)
            ->values();

        $todayAppointments = $appointments->filter(fn ($a) => $a->schedule->tanggal->isToday())->values();

        $availableNow = $availability->isAvailableNow($me);
        $next = $availableNow ? null : $availability->nextWindow($me);
        $days = AvailabilityService::DAYS;

        return view('consultant.schedules', compact('windows', 'appointments', 'todayAppointments', 'availableNow', 'next', 'days'));
    }

    public function store(Request $request)
    {
        $me = $this->me();
        $data = $this->validated($request);

        if ($this->overlaps($me, $data)) {
            return back()->withInput()->withErrors(['start_time' => 'Jam tersebut bertumpuk dengan jadwal lain di hari yang sama.']);
        }

        $me->availabilities()->create($data);

        return back()->with('success', 'Jadwal layanan ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $me = $this->me();
        $window = $me->availabilities()->findOrFail($id);
        $data = $this->validated($request);

        if ($this->overlaps($me, $data, $window->id)) {
            return back()->withInput()->withErrors(['start_time' => 'Jam tersebut bertumpuk dengan jadwal lain di hari yang sama.']);
        }

        $window->update($data);

        return back()->with('success', 'Jadwal layanan diperbarui.');
    }

    public function toggle($id)
    {
        $window = $this->me()->availabilities()->findOrFail($id);
        $window->update(['is_active' => ! $window->is_active]);

        return back()->with('success', 'Jadwal ' . ($window->is_active ? 'diaktifkan' : 'dinonaktifkan') . '.');
    }

    public function destroy($id)
    {
        $this->me()->availabilities()->findOrFail($id)->delete();

        return back()->with('success', 'Jadwal layanan dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'day_of_week.required' => 'Pilih hari.',
            'day_of_week.between' => 'Hari tidak valid.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Format jam mulai tidak valid.',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'end_time.date_format' => 'Format jam selesai tidak valid.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        return [
            'day_of_week' => (int) $data['day_of_week'],
            'start_time' => $data['start_time'] . ':00',
            'end_time' => $data['end_time'] . ':00',
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function overlaps(Consultant $me, array $data, ?int $ignoreId = null): bool
    {
        return $me->availabilities()
            ->where('day_of_week', $data['day_of_week'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();
    }

    private function me(): Consultant
    {
        return Auth::user()->consultant ?? abort(403, 'Profil konsultan tidak ditemukan.');
    }
}
