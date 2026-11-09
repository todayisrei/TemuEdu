<?php

/*
| Kredensial Midtrans diambil dari file .env (jangan ditulis di kode / Blade / JavaScript).
|   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx    (RAHASIA, hanya dipakai di server)
|   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx    (boleh tampil di halaman, dipakai snap.js)
|   MIDTRANS_IS_PRODUCTION=false
|   MIDTRANS_VERIFY_SSL=true                  (set false HANYA di lokal bila muncul error SSL cURL 60)
*/
return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'verify_ssl' => (bool) env('MIDTRANS_VERIFY_SSL', true),
];
