<?php

namespace App\Services;

use App\Models\Consultant;
use App\Models\ConsultantSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Jadwal KETERSEDIAAN konsultan (jam layanan mingguan) untuk semua layanan.
 *  - Chat         : konsultan hanya dapat menerima/membalas saat berada di jam layanan
 *  - Online Meet  : mahasiswa memilih tanggal & jam di dalam jam layanan
 *  - Offline Meet : waktu yang diusulkan harus berada di dalam jam layanan
 * consultant_schedules dipakai sebagai "blok waktu yang sudah dipesan" (is_booked = true).
 */
class AvailabilityService
{
    public const DAYS = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

    public function activeWindows(Consultant $consultant, int $isoDay): Collection
    {
        return $consultant->availabilities()
            ->where('is_active', true)
            ->where('day_of_week', $isoDay)
            ->orderBy('start_time')
            ->get();
    }

    public function isAvailableAt(Consultant $consultant, Carbon $at): bool
    {
        $time = $at->format('H:i:s');

        return $consultant->availabilities()
            ->where('is_active', true)
            ->where('day_of_week', $at->dayOfWeekIso)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();
    }

    public function isAvailableNow(Consultant $consultant): bool
    {
        return $this->isAvailableAt($consultant, now());
    }

    /** Jam layanan berikutnya (untuk info "kembali pada ..."). */
    public function nextWindow(Consultant $consultant): ?array
    {
        for ($i = 0; $i <= 7; $i++) {
            $date = today()->addDays($i);

            foreach ($this->activeWindows($consultant, $date->dayOfWeekIso) as $w) {
                $start = Carbon::parse($date->format('Y-m-d') . ' ' . $w->start_time);

                if ($start->isFuture()) {
                    $hari = $i === 0 ? 'Hari ini' : ($i === 1 ? 'Besok' : self::DAYS[$date->dayOfWeekIso]);

                    return [
                        'start' => $start,
                        'label' => $hari . ', ' . substr($w->start_time, 0, 5) . '-' . substr($w->end_time, 0, 5),
                    ];
                }
            }
        }

        return null;
    }

    /** Ringkasan mingguan: [['hari' => 'Senin', 'jam' => '09:00-12:00, 13:00-16:00'], ...] */
    public function weeklySummary(Consultant $consultant): array
    {
        $rows = $consultant->availabilities()->where('is_active', true)
            ->orderBy('day_of_week')->orderBy('start_time')->get()->groupBy('day_of_week');

        $out = [];
        foreach ($rows as $day => $windows) {
            $out[] = [
                'hari' => self::DAYS[(int) $day] ?? (string) $day,
                'jam' => $windows->map(fn ($w) => substr($w->start_time, 0, 5) . '-' . substr($w->end_time, 0, 5))->implode(', '),
            ];
        }

        return $out;
    }

    /** Info lengkap untuk ditampilkan ke mahasiswa. */
    public function summaryFor(Consultant $consultant): array
    {
        $now = $this->isAvailableNow($consultant);
        $next = $now ? null : $this->nextWindow($consultant);

        return [
            'now' => $now,
            'next' => $next ? $next['label'] : null,
            'weekly' => $this->weeklySummary($consultant),
        ];
    }

    /** Pilihan jam mulai pada suatu tanggal untuk layanan berdurasi $minutes. @return string[] 'HH:MM' */
    public function startOptions(Consultant $consultant, Carbon $date, int $minutes): array
    {
        $date = $date->copy()->startOfDay();
        $step = max(5, (int) config('temuedu.slot_step_minutes', 30));
        $busy = $this->busyBlocks($consultant, $date);

        $options = [];

        foreach ($this->activeWindows($consultant, $date->dayOfWeekIso) as $w) {
            $windowEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $w->end_time);
            $cursor = Carbon::parse($date->format('Y-m-d') . ' ' . $w->start_time);

            while ($cursor->copy()->addMinutes($minutes)->lte($windowEnd)) {
                $end = $cursor->copy()->addMinutes($minutes);

                if ($cursor->isFuture() && ! $this->overlaps($cursor, $end, $busy, $date)) {
                    $options[] = $cursor->format('H:i');
                }

                $cursor->addMinutes($step);
            }
        }

        return array_values(array_unique($options));
    }

    /** null = valid. Selain itu pesan error untuk ditampilkan. */
    public function validateBooking(Consultant $consultant, Carbon $start, int $minutes): ?string
    {
        if ($start->isPast()) {
            return 'Waktu yang dipilih sudah lewat.';
        }

        $maxDays = (int) config('temuedu.booking_days_ahead', 14);
        if ($start->gt(today()->addDays($maxDays)->endOfDay())) {
            return "Pemesanan hanya bisa untuk {$maxDays} hari ke depan.";
        }

        $end = $start->copy()->addMinutes($minutes);

        if ($end->format('Y-m-d') !== $start->format('Y-m-d')) {
            return 'Durasi layanan melewati tengah malam. Pilih jam yang lebih awal.';
        }

        $startTime = $start->format('H:i:s');
        $endTime = $end->format('H:i:s');

        $inside = $this->activeWindows($consultant, $start->dayOfWeekIso)
            ->contains(fn ($w) => $w->start_time <= $startTime && $w->end_time >= $endTime);

        if (! $inside) {
            $hari = self::DAYS[$start->dayOfWeekIso];
            $jam = $this->activeWindows($consultant, $start->dayOfWeekIso)
                ->map(fn ($w) => substr($w->start_time, 0, 5) . '-' . substr($w->end_time, 0, 5))->implode(', ');

            return $jam
                ? "Waktu tersebut di luar jam layanan konsultan. Jam layanan hari {$hari}: {$jam} (durasi {$minutes} menit harus muat di dalamnya)."
                : "Konsultan tidak memiliki jam layanan pada hari {$hari}.";
        }

        if ($this->overlaps($start, $end, $this->busyBlocks($consultant, $start), $start)) {
            return 'Jadwal tersebut bertabrakan dengan pemesanan lain. Silakan pilih waktu lain.';
        }

        return null;
    }

    /** Tandai rentang waktu sebagai terpesan (dipakai ulang bila baris yang sama sudah ada). */
    public function reserve(Consultant $consultant, Carbon $start, int $minutes): ConsultantSchedule
    {
        return ConsultantSchedule::updateOrCreate(
            [
                'consultant_id' => $consultant->id,
                'tanggal' => $start->format('Y-m-d'),
                'jam_mulai' => $start->format('H:i:s'),
            ],
            [
                'jam_selesai' => $start->copy()->addMinutes($minutes)->format('H:i:s'),
                'is_booked' => true,
            ]
        );
    }

    private function busyBlocks(Consultant $consultant, Carbon $date): Collection
    {
        return ConsultantSchedule::where('consultant_id', $consultant->id)
            ->whereDate('tanggal', $date->format('Y-m-d'))
            ->where('is_booked', true)
            ->get();
    }

    private function overlaps(Carbon $start, Carbon $end, Collection $busy, Carbon $date): bool
    {
        $day = $date->format('Y-m-d');

        foreach ($busy as $b) {
            $bs = Carbon::parse($day . ' ' . $b->jam_mulai);
            $be = Carbon::parse($day . ' ' . $b->jam_selesai);

            if ($start->lt($be) && $end->gt($bs)) {
                return true;
            }
        }

        return false;
    }
}
