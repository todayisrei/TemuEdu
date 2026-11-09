<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    protected $fillable = ['nama_prodi'];

    public function consultants(): HasMany
    {
        return $this->hasMany(Consultant::class);
    }
}
