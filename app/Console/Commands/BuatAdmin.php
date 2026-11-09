<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BuatAdmin extends Command
{
    protected $signature = 'temuedu:buat-admin {email : Email admin} {--name=Administrator : Nama admin}';

    protected $description = 'Membuat akun administrator (admin tidak mendaftar lewat halaman publik)';

    public function handle(): int
    {
        $email = $this->argument('email');

        if (User::where('email', $email)->exists()) {
            $this->error("Email {$email} sudah terdaftar.");
            return self::FAILURE;
        }

        $password = (string) $this->secret('Password (minimal 8 karakter)');
        $ulang    = (string) $this->secret('Ulangi password');

        if (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');
            return self::FAILURE;
        }

        if ($password !== $ulang) {
            $this->error('Konfirmasi password tidak sama.');
            return self::FAILURE;
        }

        $user = new User();
        $user->forceFill([
            'name'              => $this->option('name'),
            'email'             => $email,
            'password'          => $password,   // otomatis di-hash oleh cast 'hashed' di model User
            'role'              => 'admin',
            'email_verified_at' => now(),
        ])->save();

        $this->info("Admin {$email} berhasil dibuat. Silakan login di /login.");

        return self::SUCCESS;
    }
}
