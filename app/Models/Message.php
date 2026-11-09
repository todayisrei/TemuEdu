<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['consultation_id', 'sender_id', 'tipe', 'isi', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    /**
     * Pesan BELUM DIBACA milik seorang pengguna:
     *  - hanya pesan teks (bukan pesan sistem)
     *  - dikirim oleh LAWAN chat (pesan sendiri tidak dihitung)
     *  - read_at masih kosong
     * Mahasiswa -> sesi miliknya (consultations.user_id)
     * Konsultan -> sesi konsultasinya (consultations.consultant_id)
     */
    public function scopeUnreadFor($query, User $user)
    {
        if ($user->role === 'konsultan') {
            $column = 'consultations.consultant_id';
            $value = optional($user->consultant)->id ?? 0;
        } else {
            $column = 'consultations.user_id';
            $value = $user->id;
        }

        return $query
            ->join('consultations', 'consultations.id', '=', 'messages.consultation_id')
            ->where($column, $value)
            ->where('messages.tipe', 'text')
            ->where('messages.sender_id', '!=', $user->id)
            ->whereNull('messages.read_at');
    }

    public function consultation(): BelongsTo { return $this->belongsTo(Consultation::class); }
    public function sender(): BelongsTo { return $this->belongsTo(User::class, 'sender_id'); }
}
