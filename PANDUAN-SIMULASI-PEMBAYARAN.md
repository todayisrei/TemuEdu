# Mode Simulasi Pembayaran (langsung lunas)

Alur: pilih layanan -> halaman Pembayaran -> pilih metode -> tampil QRIS / nomor VA / QR e-wallet
      -> klik "Saya sudah bayar (simulasi)" -> LUNAS -> langsung masuk chat.
- QRIS & E-Wallet: tampil QR contoh (butuh internet untuk memuat pustaka QR dari cdnjs; jika gagal tampil teks pengganti).
- Transfer Bank: pilih BCA/BNI/BRI/Mandiri -> tampil nomor VA contoh + tombol Salin.
- QR dan nomor VA hanya CONTOH (tidak bisa dipakai membayar sungguhan).
Tanpa Midtrans, tanpa kunci, tanpa uang asli. Cocok untuk demo.

## Pasang
1. Timpa isi zip ke folder project.
2. php artisan config:clear
3. php artisan optimize:clear
(Tidak ada migration baru.)

## Mode
- Bawaan: simulation saat APP_ENV=local  |  midtrans saat APP_ENV=production.
- Untuk memaksa, tambahkan di .env:
      PAYMENT_MODE=simulation     (klik Bayar = langsung lunas)
      PAYMENT_MODE=midtrans       (pembayaran sungguhan lewat Midtrans, butuh kunci)
  lalu jalankan php artisan config:clear
- Mode simulasi TIDAK PERNAH aktif jika APP_ENV=production, apa pun isi PAYMENT_MODE.

## Aturan layanan tetap berlaku
Layanan baru aktif setelah pembayaran lunas (di server, bukan di browser). Harga dari config.
Chat: bayar -> ruang chat terbuka (timer 60 mnt mulai di pesan pertama). Online Meet: pilih jadwal -> bayar -> konsultan konfirmasi.
Offline Meet: koordinasi chat -> ajukan -> konsultan setujui -> bayar.
Riwayat simulasi tampil di Admin > Pembayaran dengan metode "simulasi-qris" dst.
