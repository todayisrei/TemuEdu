<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Akuntansi', 'Manajemen', 'Bisnis Digital', 'Ilmu Ekonomi', 'Ekonomi Islam / Syariah'] as $nama) {
            Prodi::firstOrCreate(['nama_prodi' => $nama]);
        }
    }
}
