<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'notification_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_enabled' => 'boolean',
        ];
    }

    // Profil konsultan (hanya ada kalau role = konsultan)
    public function consultant(): HasOne { return $this->hasOne(Consultant::class); }

    // Konsultasi yang dimulai user ini (sebagai mahasiswa)
    public function consultations(): HasMany { return $this->hasMany(Consultation::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isKonsultan(): bool { return $this->role === 'konsultan'; }
    public function isUser(): bool { return $this->role === 'user'; }
}
