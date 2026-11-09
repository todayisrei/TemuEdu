<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ProdiSeeder::class);

        // Akun admin hanya dibuat jika ADMIN_EMAIL & ADMIN_PASSWORD diisi di file .env.
        // Tidak ada password bawaan di kode. Alternatif: php artisan temuedu:buat-admin
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD belum diisi di .env, akun admin tidak dibuat.');
            $this->command?->warn('Buat admin dengan: php artisan temuedu:buat-admin email@kamu.com');
            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->command?->info("Admin {$email} sudah ada, dilewati.");
            return;
        }

        $user = new User();
        $user->forceFill([
            'name' => env('ADMIN_NAME', 'Administrator'),
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'email_verified_at' => now(),
        ])->save();

        $this->command?->info("Admin {$email} berhasil dibuat.");
    }
}
