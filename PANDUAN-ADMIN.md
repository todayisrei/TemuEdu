# TemuEdu - Panduan Modul Administrator

Project ini sudah lengkap (termasuk vendor/ dan .env milikmu). Tinggal ekstrak.

## MULAI CEPAT (urut, 6 perintah)

Di terminal, dari dalam folder project:

    php artisan migrate                 # TIDAK menghapus data. Aman. (jangan pakai migrate:fresh)
    php artisan db:seed                 # mengisi 5 program studi (aman diulang)
    php artisan temuedu:buat-admin emailkamu@gmail.com --name="Nama Kamu"
                                        # lalu ketik password (min. 8 karakter) 2x
    php artisan storage:link            # supaya foto konsultan tampil
    php artisan optimize:clear
    php artisan serve

Buka http://127.0.0.1:8000/login lalu masuk memakai akun admin tadi -> otomatis ke /admin.

Catatan: pastikan MySQL menyala dan database `temuedu` sudah dibuat (sesuai .env).
Cek tabel: `php artisan migrate:status` -> semua harus "Ran".

Opsi lain membuat admin lewat seeder: isi ADMIN_EMAIL dan ADMIN_PASSWORD di .env lalu
`php artisan db:seed`. Tidak ada password bawaan di kode. Hapus kedua baris itu dari .env setelah selesai.

## ALUR AKSES (sesuai keputusan)

- Landing page (/) = dashboard mahasiswa -> boleh dibuka TANPA login.
- /consultation (pilih prodi, konsultan, chat) -> WAJIB login. Belum login diarahkan ke /login,
  setelah login kembali ke halaman tujuan.
- Register publik (/register) -> hanya mahasiswa (role dipaksa 'user' di server).
- Login satu halaman untuk semua role. Setelah login:
    mahasiswa -> landing page (/)         konsultan -> /consultant/dashboard (halaman sementara)
    admin     -> /admin
- Konsultan yang dinonaktifkan: tidak bisa login (pesan jelas), dan jika sedang login langsung
  dikeluarkan saat membuka halaman konsultan.
- /admin/* hanya admin (middleware role:admin, dicek di server). Role lain -> 403.

## FITUR ADMIN

Dashboard (angka dari database) | Konsultan: daftar, cari/filter, tambah (akun login + profil +
foto), detail, edit, reset password, aktif/nonaktif, hapus aman | Program Studi: tambah/edit/hapus |
Mahasiswa: daftar (lihat saja) | Konsultasi: pemantauan (lihat saja).

Aturan data: konsultan yang sudah punya riwayat konsultasi tidak bisa dihapus (nonaktifkan saja);
prodi yang masih dipakai konsultan tidak bisa dihapus.

## FILE YANG DIBUAT / DIUBAH

Baru:
  app/Http/Middleware/RoleMiddleware.php
  app/Http/Controllers/Admin/{Dashboard,Prodi,Consultant,Student,Consultation}Controller.php
  app/Console/Commands/BuatAdmin.php
  resources/views/layouts/admin.blade.php, resources/views/admin/**, errors/403.blade.php
  resources/views/consultant/dashboard.blade.php (sementara)
Diubah:
  bootstrap/app.php (alias middleware 'role')      routes/web.php
  AuthController.php, HomeController.php           app/Models/Consultant.php (accessor foto_url)
  database/seeders/DatabaseSeeder.php (tanpa password bawaan)
  resources/views/login.blade.php (tersambung ke backend; mode demo dihapus)
  resources/views/register.blade.php (pilihan role dihapus)
  resources/views/welcome.blade.php (navbar: tombol Masuk <-> Keluar/Dashboard sesuai status login)
Database: TIDAK ada migration baru (struktur yang ada sudah cukup).
Tidak disentuh: isi landing, FAQ, ulasan, halaman /consultation (masih data dummy).

## CHECKLIST PENGUJIAN

A. Login per role
 1. Admin login (email+password benar) -> masuk /admin
 2. Mahasiswa (daftar lewat /register, lalu login) -> masuk landing, tombol berubah jadi "Keluar"
 3. Konsultan (dibuat admin) login -> halaman Dashboard Konsultan sementara
 4. Konsultan dinonaktifkan admin -> login ditolak dengan pesan jelas
 5. Password salah / email tidak terdaftar -> pesan "Email atau kata sandi ... salah"
 6. Email kosong / format salah -> pesan validasi muncul
 7. Sudah login lalu buka /login -> diarahkan ke halaman sesuai role (bukan form login)
B. Akses URL
 1. Belum login buka /admin -> ke /login
 2. Belum login buka /consultation -> ke /login, setelah login kembali ke /consultation
 3. Mahasiswa buka /admin atau /admin/consultants -> 403
 4. Konsultan buka /admin -> 403
 5. Landing (/) bisa dibuka tanpa login
C. Register
 1. Daftar tanpa pilihan role -> akun terbuat dengan role 'user'
 2. (Uji keamanan) kirim role=admin lewat inspect element -> tetap 'user'
 3. Email sama dua kali -> ditolak
D. Admin
 1. Tambah prodi, edit, hapus (prodi yang dipakai konsultan -> ditolak)
 2. Tambah konsultan lengkap + foto -> muncul di daftar; cek di phpMyAdmin: users.role='konsultan',
    password berbentuk hash ($2y$...), consultants.user_id terhubung
 3. Email konsultan dobel -> ditolak
 4. Edit konsultan, kosongkan password -> password lama tetap; isi -> password berganti
 5. Nonaktifkan / aktifkan konsultan
 6. Halaman Mahasiswa dan Konsultasi tampil (kosong = pesan "belum ada")
E. Logout
 1. Klik Keluar (sidebar admin / navbar landing) -> kembali ke /login atau landing, /admin tidak bisa dibuka lagi
F. Regresi
 1. Landing, FAQ accordion, carousel ulasan, link prodi -> normal
 2. /consultation/... setelah login -> tampil seperti sebelumnya

Batas pengujian: dashboard konsultan hanya halaman sementara; /consultation masih data dummy,
jadi konsultan buatan admin belum muncul di sana.

## MASALAH UMUM
 - "Route [...] not defined" / "Target class [role]..." -> php artisan optimize:clear
 - Foto tidak tampil -> php artisan storage:link, cek APP_URL di .env
 - SQLSTATE / tabel tidak ada -> php artisan migrate
 - 419 Page Expired -> refresh halaman login (token CSRF kedaluwarsa)
 - Upload foto gagal -> naikkan upload_max_filesize & post_max_size di php.ini

## PEKERJAAN LANJUTAN
 1. Sambungkan /consultation ke database (hanya konsultan is_active=true).
 2. Dashboard Konsultan: jadwal, janji, profil.
 3. Perbaiki ChatController & AppointmentController: cek kepemilikan data (saat ini user login bisa
    mengakses chat/janji lewat ID) dan ganti perbandingan consultant_id dengan consultants.id.
 4. Buat view yang belum ada: appointments.*, chats.*, consultants.*.
 5. Keamanan: .env berisi APP_KEY, jangan diunggah ke GitHub publik.
