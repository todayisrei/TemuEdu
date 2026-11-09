<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'consultant_id', 'consultation_id', 'appointment_id',
        'service', 'amount', 'status', 'snap_token', 'transaction_id', 'payment_type',
        'transaction_status', 'fraud_status', 'raw_notification', 'expires_at', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_notification' => 'array',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public const LABELS = [
        'pending' => 'Menunggu pembayaran',
        'paid' => 'Lunas',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        'cancelled' => 'Dibatalkan',
        'refund_pending' => 'Perlu refund',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function consultant(): BelongsTo { return $this->belongsTo(Consultant::class); }
    public function consultation(): BelongsTo { return $this->belongsTo(Consultation::class); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }

    public function isPaid(): bool { return $this->status === 'paid'; }
    public function isPending(): bool { return $this->status === 'pending'; }

    public function label(): string { return self::LABELS[$this->status] ?? $this->status; }

    /** Masih bisa dibayar: pending dan belum lewat batas waktu. */
    public function isPayable(): bool
    {
        return $this->isPending() && (! $this->expires_at || $this->expires_at->isFuture());
    }
}
