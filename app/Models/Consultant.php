<?php

namespace App\Models;

use App\Support\Avatar;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultant extends Model
{
    protected $fillable = [
        'user_id', 'prodi_id', 'foto', 'domisili', 'kompetensi', 'alumni',
        'ipk', 'harga', 'portofolio_link', 'rating_avg', 'rating_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'ipk' => 'decimal:2',
            'rating_avg' => 'decimal:1',
            'is_active' => 'boolean',
        ];
    }

    /** URL foto profil (null kalau belum ada foto). Dipakai: $consultant->foto_url */
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->foto ? asset('storage/' . $this->foto) : null);
    }

    /** Foto profil, atau avatar inisial jika belum ada foto. Dipakai: $consultant->avatar_url */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->foto_url ?: Avatar::initials($this->user->name ?? '?'));
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function prodi(): BelongsTo { return $this->belongsTo(Prodi::class); }
    public function availabilities(): HasMany { return $this->hasMany(ConsultantAvailability::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function schedules(): HasMany { return $this->hasMany(ConsultantSchedule::class); }
    public function consultations(): HasMany { return $this->hasMany(Consultation::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }

    /** Hitung ulang rata-rata rating dari tabel reviews. */
    public function refreshRating(): void
    {
        $this->update([
            'rating_avg' => round($this->reviews()->avg('rating') ?? 0, 1),
            'rating_count' => $this->reviews()->count(),
        ]);
    }
}
