# TemuEdu - Jadwal Ketersediaan, Pilihan Layanan & Pembayaran Midtrans

## 1. PASANG
1. Ekstrak zip, TIMPA ke folder project (struktur folder sama).
2. HAPUS 3 file lama yang sudah tidak terhubung ke route (dan bisa membuat janji tanpa bayar kalau dipakai lagi):
       app/Http/Controllers/AppointmentController.php
       app/Http/Controllers/ChatController.php
       app/Http/Controllers/ConsultantController.php
3. Isi kredensial Midtrans di file .env (JANGAN dibagikan / diunggah):
       MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
       MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
       MIDTRANS_IS_PRODUCTION=false
       MIDTRANS_VERIFY_SSL=true
   Kunci sandbox: https://dashboard.sandbox.midtrans.com -> Settings -> Access Keys.
   Jika muncul "SSL certificate problem" (cURL error 60) di XAMPP: set MIDTRANS_VERIFY_SSL=false HANYA di lokal,
   atau perbaiki curl.cainfo di php.ini.
4. Jalankan:
       php artisan migrate
       php artisan config:clear
       php artisan optimize:clear
       php artisan serve
   Migration aman (data lama dipertahankan): tabel baru consultant_availabilities & payments, kolom baru di
   consultations (service, is_paid) dan appointments (payment_status, duration_minutes, harga).
   Data lama dianggap "Chat, sudah dibayar". Konsultan yang sudah ada otomatis diberi jam layanan
   Senin-Minggu 00:00-23:59 agar chat lama tidak terkunci -> minta konsultan menyesuaikan di menu Jadwal.
5. (Opsional) data contoh:  php artisan db:seed --class=DemoDataSeeder   (hanya APP_ENV=local)

## 2. KONSEP
JADWAL KONSULTAN = JAM LAYANAN MINGGUAN (bukan jadwal Online Meet saja). Contoh: Senin 09:00-12:00,
Selasa 13:00-16:00. Berlaku untuk semua layanan:
| Layanan      | Durasi | Harga     | Aturan                                                                 |
|--------------|--------|-----------|------------------------------------------------------------------------|
| Chat         | 60 mnt | Rp35.000  | Bayar dulu -> ruang chat terbuka. Timer 60 mnt mulai dari pesan pertama mahasiswa (tetap seperti sebelumnya). Beli chat & kirim pesan pertama hanya saat konsultan di jam layanan; konsultan hanya bisa membalas di jam layanan. |
| Online Meet  | 90 mnt | Rp55.000  | Pilih tanggal & jam (dari jam layanan, kelipatan 30 mnt, tidak bertabrakan) -> bayar -> konsultan melihat booking, mengisi link, menyetujui -> selesai. |
| Offline Meet | 120 mnt| Rp155.000 | Koordinasi lewat chat (30 mnt, gratis) -> mahasiswa ajukan pertemuan (tanggal, jam, lokasi di dalam jam layanan) -> konsultan setujui -> mahasiswa bayar -> terkonfirmasi. |
Harga & durasi ada di config/temuedu.php (server). Browser tidak pernah mengirim nominal.

## 3. KEAMANAN PEMBAYARAN
- Nominal dari config server; order_id dibuat server; mahasiswa hanya bisa membuka pembayaran miliknya (404 untuk milik orang lain).
- Layanan aktif HANYA setelah Midtrans menyatakan settlement / capture-accept:
    a) webhook POST /midtrans/notification, signature SHA512 diverifikasi (CSRF dikecualikan khusus rute ini), atau
    b) halaman "selesai bayar" menanyakan status ke Midtrans server-ke-server memakai Server Key
       (jadi tetap jalan di localhost tanpa webhook; klik "Cek status pembayaran" bila perlu).
- Nominal notifikasi dicocokkan dengan catatan; hanya pembayaran berstatus pending yang bisa berubah.
- Ruang chat berbayar dikunci di server (halaman, polling, kirim pesan, akhiri sesi) sampai lunas.
- Server Key tidak pernah ada di Blade/JS (hanya Client Key yang tampil, memang publik).
- Webhook di produksi: Midtrans Dashboard -> Settings -> Configuration -> Payment Notification URL =
  https://domainmu/midtrans/notification . Untuk uji lokal bisa memakai ngrok.

## 4. HALAMAN & ROUTE BARU
Mahasiswa : POST /consultation/service/chat/{id} | GET,POST /consultation/service/online/{id} | POST /consultation/service/offline/{id}
            GET /payment/{order} (popup Snap) | GET /payment/{order}/finish | POST /payment/appointment/{id}
            Konsultasi Saya (/consultation/history) kini menampilkan layanan, status pembayaran & janji + tombol Bayar/Batalkan
Konsultan : /consultant/schedules = jam layanan (tambah, ubah, aktif/nonaktif, hapus) + janji hari ini/mendatang
Admin     : /admin/payments (daftar pembayaran, lihat saja; status "Perlu refund" diproses manual di dashboard Midtrans)
Publik    : POST /midtrans/notification

## 5. CHECKLIST TES (2 browser: mahasiswa & konsultan)
 1. Konsultan: Jadwal -> tambah Senin 09:00-12:00; ubah; nonaktifkan; hapus. Status "Sedang dalam jam layanan" mengikuti jam sekarang.
 2. Mahasiswa: pilih konsultan -> Konsultasi -> popup tiga layanan dengan durasi & harga -> status ketersediaan + jam layanan tampil.
 3. CHAT: Mulai Chat -> halaman Pembayaran -> popup Midtrans -> bayar (simulator sandbox) -> otomatis masuk ruang chat.
    Sebelum bayar, buka langsung /consultation/room/{id} -> diarahkan ke pembayaran (tidak bisa masuk).
 4. Chat: kirim pesan pertama -> timer 60 menit jalan; balasan konsultan; Akhiri sesi; notifikasi tetap jalan.
 5. Konsultan di luar jam layanan: tombol Mulai Chat nonaktif + info "kembali pada ..."; balasan konsultan ditolak server.
 6. ONLINE MEET: Pilih Jadwal -> tanggal & jam -> bayar -> konsultan melihat booking (Menunggu konfirmasi) -> isi link -> Setujui
    -> mahasiswa melihat link di Konsultasi Saya / chat -> Tandai selesai.
 7. Dua mahasiswa memilih jam yang sama -> yang kedua ditolak / jam hilang dari pilihan.
 8. Tutup popup tanpa bayar -> jadwal tertahan 30 menit; "Batalkan" di Konsultasi Saya melepasnya; lewat batas waktu terlepas otomatis.
 9. OFFLINE MEET: Ajukan Pertemuan -> chat -> Janji Meet (tanggal, jam, lokasi) -> konsultan Setujui -> mahasiswa Bayar Rp155.000 -> Lunas.
10. Konsultan Tolak janji yang sudah dibayar -> pembayaran tampil "Perlu refund" di /admin/payments.
11. Keamanan: coba buka /payment/{order milik orang lain} -> 404; tidak ada input nominal di form manapun.
12. Regresi: login/logout 3 role, notifikasi bel, profil, Cari Teman Diskusi, dashboard konsultan & admin.

Pembayaran uji (sandbox) - kartu: 4811 1111 1111 1114, exp bebas (masa depan), CVV 123, OTP 112233.
Metode lain lewat simulator: https://simulator.sandbox.midtrans.com (cek dokumentasi Midtrans untuk data uji terbaru).

## 6. KEPUTUSAN DESAIN YANG PERLU KAMU TAHU
- Chat koordinasi Offline Meet dibatasi 30 menit (config temuedu.offline_coordination_minutes) dan maks. 2x/hari per konsultan,
  agar tidak menjadi chat gratis.
- Pembayaran janji yang ditolak konsultan TIDAK otomatis dikembalikan (refund dilakukan manual di dashboard Midtrans).
- Pelepasan jadwal kedaluwarsa dijalankan saat halaman terkait dibuka (tanpa cron/scheduler).
- Kolom "Tarif" di form admin konsultan tidak lagi dipakai (harga sekarang tetap per layanan).
- Modal Janji Meet yang terbuka otomatis kini hanya muncul di ruang koordinasi Offline Meet yang belum punya pengajuan
  (Online Meet & Chat tidak lagi memakai modal itu untuk memesan).
- Pembayaran bersifat sandbox sampai MIDTRANS_IS_PRODUCTION=true dan kunci produksi diisi.

## 7. FILE
Baru : config/{temuedu,midtrans}.php; 3 migration 2026_10_26_*; app/Services/{Availability,Midtrans,Payment}Service.php;
       Models ConsultantAvailability & Payment; Controllers ServiceController, PaymentController, Admin/PaymentController;
       views service/online-schedule, payment/show, consultation/history (ditulis ulang), consultant/schedules (ditulis ulang), admin/payments/index
Ubah : routes/web.php, bootstrap/app.php (CSRF webhook), .env.example, Models (Consultant, Consultation, Appointment),
       ConsultationController, ConsultationRoomController, Consultant/{Dashboard,Schedule,Appointment,Consultation}Controller,
       Admin/ConsultantController, views consultants/prodi/chat/consultant dashboard & detail/admin layout, DemoDataSeeder
Hapus: 3 controller lama (lihat langkah 1.2)
