<?php

/*
|--------------------------------------------------------------------------
| Pengaturan layanan TemuEdu
|--------------------------------------------------------------------------
| Harga & durasi ditentukan di SERVER (di sini), tidak pernah dari input pengguna.
*/
return [

    'services' => [
        'chat' => [
            'label' => 'Chat',
            'minutes' => 60,
            'price' => 35000,
            'icon' => 'fa-comments',
            'desc' => 'Konsultasi lewat chat',
        ],
        'online_meet' => [
            'label' => 'Online Meet',
            'minutes' => 90,
            'price' => 55000,
            'icon' => 'fa-video',
            'desc' => 'Bertemu lewat video call',
        ],
        'offline_meet' => [
            'label' => 'Offline Meet',
            'minutes' => 120,
            'price' => 155000,
            'icon' => 'fa-location-dot',
            'desc' => 'Bertemu langsung',
        ],
    ],

    // Mode pembayaran:
    //   'simulation' = klik "Bayar" langsung dianggap LUNAS (tanpa uang asli) -> untuk demo / uji lokal
    //   'midtrans'   = pembayaran sungguhan lewat Midtrans (sandbox / produksi)
    // Bawaan: simulation saat APP_ENV bukan production. Mode simulasi TIDAK PERNAH aktif di production.
    'payment_mode' => env('PAYMENT_MODE', env('APP_ENV', 'production') === 'production' ? 'midtrans' : 'simulation'),

    // Pilihan metode pada halaman pembayaran simulasi (hanya tampilan)
    'simulation_methods' => [
        'qris' => 'QRIS',
        'bank_transfer' => 'Transfer Bank (Virtual Account)',
        'ewallet' => 'E-Wallet (GoPay / OVO / DANA)',
        'credit_card' => 'Kartu Kredit / Debit',
    ],

    // Pilihan bank (nomor VA contoh) & e-wallet pada halaman pembayaran simulasi.
    // 'prefix' = awalan nomor VA contoh (BUKAN nomor asli, hanya untuk tampilan).
    'simulation_banks' => [
        'bca' => ['label' => 'BCA', 'prefix' => '70012'],
        'bni' => ['label' => 'BNI', 'prefix' => '8808'],
        'bri' => ['label' => 'BRI', 'prefix' => '26215'],
        'mandiri' => ['label' => 'Mandiri', 'prefix' => '88608'],
    ],
    'simulation_wallets' => [
        'gopay' => 'GoPay',
        'ovo' => 'OVO',
        'dana' => 'DANA',
        'shopeepay' => 'ShopeePay',
    ],

    // Lama chat koordinasi untuk layanan Offline Meet (sebelum pengajuan pertemuan disepakati)
    'offline_coordination_minutes' => 30,

    // Slot yang sedang menunggu pembayaran ditahan selama ... menit, lalu dilepas otomatis
    'payment_hold_minutes' => 30,

    // Kelipatan jam mulai yang ditawarkan pada pilihan jadwal Online Meet
    'slot_step_minutes' => 30,

    // Mahasiswa dapat memesan sampai ... hari ke depan
    'booking_days_ahead' => 14,
];
