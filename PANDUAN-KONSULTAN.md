# TemuEdu - Role Konsultan (+ Chat) - Panduan

## PASANG
1. Ekstrak zip ini dan TIMPA ke folder project (struktur folder sama). Tidak ada file yang perlu dihapus.
2. Tidak ada migration baru (tabel consultations, messages, appointments, consultant_schedules sudah ada).
   Pastikan `php artisan migrate` pernah sukses.
3. Jalankan:
       php artisan optimize:clear
       php artisan serve
4. (Opsional, untuk presentasi) isi data contoh - hanya jalan di APP_ENV=local:
       php artisan db:seed --class=DemoDataSeeder
   Perintah ini membuat 2 konsultan, 1 mahasiswa, jadwal, 1 konsultasi + 1 janji meet pending.
   Password dibuat ACAK dan ditampilkan sekali di terminal (atau isi DEMO_PASSWORD di .env).

## ROUTE KONSULTAN (semua: auth + role:konsultan)
GET    /consultant/dashboard
GET    /consultant/consultations            (?status=menunggu|berlangsung|selesai &q=nama)
GET    /consultant/consultations/{id}
POST   /consultant/consultations/{id}/close
PATCH  /consultant/appointments/{id}/approve | reject | complete
GET    /consultant/schedules
POST   /consultant/schedules
DELETE /consultant/schedules/{id}
GET    /consultant/profile

Ruang chat (mahasiswa & konsultan PEMILIK sesi, dicek di server):
GET  /consultation/room/{id}   GET|POST /consultation/room/{id}/messages   POST /consultation/room/{id}/close
Mahasiswa: GET /consultation/chat/{consultantId}  POST /consultation/room/{id}/appointments
           POST /consultation/room/{id}/appointments/{appointmentId}/cancel
Dihapus: /consultants, /appointments, /chats (lama; tanpa tampilan & tanpa cek kepemilikan).
Dihapus: consultant.profile.edit/update (profil dikelola admin, konsultan hanya melihat).

## HAK AKSES
| Halaman              | Mahasiswa | Konsultan        | Admin |
|----------------------|-----------|------------------|-------|
| /admin/*             | 403       | 403              | ya    |
| /consultant/*        | 403       | ya (data miliknya)| 403  |
| /consultation (prodi, daftar konsultan) | ya | ya     | ya    |
| mulai chat           | ya        | 403              | 403   |
| ruang chat           | sesinya   | sesinya          | 403   |
Konsultan tidak bisa membuka konsultasi/janji/jadwal konsultan lain: query selalu dibatasi
consultant_id = konsultan yang login, jadi ID milik orang lain -> 404.
Konsultan yang dinonaktifkan admin: tidak bisa login, dan sesi yang sedang berjalan langsung dikeluarkan.

## STATUS (tidak ada status baru di database)
consultations.status : active | closed | expired  -> label konsultan:
   Menunggu    = aktif & konsultan belum membalas
   Berlangsung = aktif & konsultan sudah membalas
   Selesai     = closed / expired
appointments.status  : pending | approved | rejected | completed | cancelled
Kartu dashboard: Total Konsultasi | Menunggu Konfirmasi (= janji meet pending) |
                 Konsultasi Hari Ini (sesi dimulai hari ini) | Konsultasi Selesai.
Topik konsultasi diisi otomatis dari pertanyaan pertama mahasiswa.

## ALUR (sesuai catatan TemuEdu)
Mahasiswa -> prodi -> konsultan (dari database) -> chat (timer 1 jam mulai dari pesan pertama)
 -> tombol "Janji Meet": pilih jadwal konsultan + online/offline -> status pending
Konsultan -> Detail konsultasi -> Setujui (isi link / lokasi) atau Tolak (slot dibuka lagi) -> Tandai selesai
Jadwal ketersediaan diatur konsultan di menu Jadwal.

## CARA TES (2 browser: biasa + incognito)
 1. Admin login -> Konsultan -> Tambah Konsultan (aktif). Catat email & password. Logout.
 2. Konsultan login -> otomatis ke /consultant/dashboard. Menu Jadwal -> tambah 1-2 slot (hari ini/besok).
 3. Mahasiswa (daftar di /register) -> Mulai Konsultasi -> pilih prodi -> pilih konsultan -> kirim pesan.
 4. Konsultan: dashboard / Konsultasi menampilkan percakapan (status Menunggu, label "1 baru").
    Klik Detail -> Buka Chat -> balas. Di sisi mahasiswa pesan muncul +-3 detik tanpa refresh.
 5. Mahasiswa: tombol "Janji Meet" -> pilih jadwal + metode -> Ajukan.
 6. Konsultan: Detail konsultasi -> isi link/lokasi -> Setujui. Mahasiswa melihat status Disetujui + link.
 7. Konsultan: Tandai selesai; menu Jadwal menampilkan jadwal hari ini dan slot (Dipesan/Tersedia).
 8. Authorization: mahasiswa buka /consultant/dashboard -> 403; konsultan buka /admin -> 403;
    konsultan A buka /consultant/consultations/{id konsultan B} -> 404; mahasiswa B buka room mahasiswa A -> 403;
    admin buka /admin -> tetap bisa.
 9. Logout dari ketiga role (tombol Keluar) -> kembali ke /login atau landing.

## FILE
Baru:
  app/Http/Controllers/Consultant/{Dashboard,Consultation,Appointment,Schedule,Profile}Controller.php
  app/Http/Controllers/ConsultationRoomController.php      (chat + janji meet mahasiswa)
  app/Support/Avatar.php
  resources/views/layouts/consultant.blade.php, layouts/partials/panel-styles.blade.php
  resources/views/consultant/{dashboard,schedules,profile}.blade.php, consultant/consultations/*, consultant/partials/status.blade.php
  database/seeders/DemoDataSeeder.php
Diubah:
  routes/web.php (grup /consultant, route chat/janji; route lama dihapus)
  layouts/admin.blade.php (CSS dipindah ke partial bersama; tampilan admin sama)
  ConsultationController (data dari database), HomeController
  Models: Consultant (avatar_url), Consultation (expireOverdue, isChatOpen), ConsultantSchedule (relasi)
  Admin/ConsultantController (aturan hapus), consultation/chat.blade.php (chat nyata + janji meet),
  AppServiceProvider (tanggal bahasa Indonesia), config/app.php (timezone Asia/Jakarta)
Tidak disentuh: landing, FAQ, ulasan, login, register, modul admin lainnya.

## BATASAN
- Chat memakai polling 3 detik (bukan WebSocket); cukup untuk demo.
- Mahasiswa belum punya halaman "riwayat konsultasi" sendiri (mereka masuk lewat daftar konsultan);
  jadwal/janji mahasiswa dilihat di tombol "Janji Meet" pada chat.
- Konsultan belum bisa mengedit profil (sengaja: dikelola admin).
- Ulasan setelah sesi selesai belum dibuat.
- Controller lama AppointmentController, ChatController, ConsultantController masih ada di folder tetapi
  tidak terhubung ke route; boleh dihapus nanti.
