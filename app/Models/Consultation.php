<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consultation extends Model
{
    protected $fillable = [
        'user_id', 'consultant_id', 'prodi_id', 'service', 'is_paid', 'topik', 'harga',
        'status', 'started_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'expires_at' => 'datetime', 'is_paid' => 'boolean'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function consultant(): BelongsTo { return $this->belongsTo(Consultant::class); }
    public function prodi(): BelongsTo { return $this->belongsTo(Prodi::class); }
    public function messages(): HasMany { return $this->hasMany(Message::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }
    public function review(): HasOne { return $this->hasOne(Review::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    /** Tandai semua sesi yang lewat 1 jam sebagai 'expired' (opsional per konsultan). */
    public static function expireOverdue(?int $consultantId = null): void
    {
        static::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->when($consultantId, fn ($q) => $q->where('consultant_id', $consultantId))
            ->update(['status' => 'expired']);
    }

    /** Lama timer chat (menit) untuk layanan ini. null = tanpa timer (Online Meet). */
    public function timerMinutes(): ?int
    {
        return match ($this->service) {
            'chat' => (int) config('temuedu.services.chat.minutes', 60),
            'offline_meet' => (int) config('temuedu.offline_coordination_minutes', 30),
            default => null,
        };
    }

    public function isTimed(): bool
    {
        return $this->timerMinutes() !== null;
    }

    /**
     * Ruang chat boleh dibuka? Layanan berbayar HARUS sudah lunas.
     * Offline Meet: chat koordinasi boleh dibuka sebelum bayar (bayar setelah jadwal & lokasi disepakati).
     */
    public function isAccessible(): bool
    {
        return $this->service === 'offline_meet' || (bool) $this->is_paid;
    }

    public function scopeAccessible($query)
    {
        return $query->where(fn ($q) => $q->where('service', 'offline_meet')->orWhere('is_paid', true));
    }

    /** Mulai hitung timer (dipanggil saat pesan pertama mahasiswa dikirim). */
    public function start(?int $minutes = null): void
    {
        if ($this->started_at) {
            return;
        }

        $minutes ??= $this->timerMinutes();

        if ($minutes === null) {
            return; // layanan tanpa timer
        }

        $this->update([
            'started_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
        ]);
    }

    /** Tandai kedaluwarsa kalau batas 1 jam sudah lewat. */
    public function expireIfNeeded(): void
    {
        if ($this->status === 'active' && $this->expires_at && $this->expires_at->isPast()) {
            $this->update(['status' => 'expired']);
        }
    }

    /** Chat boleh dipakai: sesi aktif dan (belum dimulai atau belum lewat 1 jam). */
    public function isChatOpen(): bool
    {
        return $this->status === 'active' && (! $this->expires_at || $this->expires_at->isFuture());
    }

    public function remainingSeconds(): ?int
    {
        if (! $this->expires_at || $this->status !== 'active') {
            return null;
        }

        return max(0, $this->expires_at->getTimestamp() - now()->getTimestamp());
    }
}
