<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'consultation_id', 'user_id', 'consultant_id', 'schedule_id',
        'tipe', 'link_meeting', 'lokasi', 'catatan', 'status',
        'payment_status', 'duration_minutes', 'harga',
    ];

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(Payment::class); }

    public function isPaid(): bool { return $this->payment_status === 'paid'; }

    /** Konsultan melihat: janji yang sudah dibayar, atau pengajuan Offline Meet (dibayar setelah disetujui). */
    public function scopeVisibleToConsultant($query)
    {
        return $query->where(fn ($q) => $q->where('payment_status', 'paid')->orWhere('tipe', 'offline_meet'));
    }

    public function consultation(): BelongsTo { return $this->belongsTo(Consultation::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function consultant(): BelongsTo { return $this->belongsTo(Consultant::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(ConsultantSchedule::class, 'schedule_id'); }
}
