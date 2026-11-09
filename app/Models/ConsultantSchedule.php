<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultantSchedule extends Model
{
    protected $fillable = ['consultant_id', 'tanggal', 'jam_mulai', 'jam_selesai', 'is_booked'];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'is_booked' => 'boolean'];
    }

    public function consultant(): BelongsTo { return $this->belongsTo(Consultant::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class, 'schedule_id'); }

    /** Waktu mulai slot sebagai Carbon (tanggal + jam_mulai). */
    public function startsAt(): \Illuminate\Support\Carbon
    {
        return \Illuminate\Support\Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_mulai);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_booked', false)->whereDate('tanggal', '>=', now()->toDateString());
    }
}
