<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['consultation_id', 'user_id', 'consultant_id', 'rating', 'ulasan'];

    public function consultation(): BelongsTo { return $this->belongsTo(Consultation::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function consultant(): BelongsTo { return $this->belongsTo(Consultant::class); }
}
