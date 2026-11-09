<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ConsultationRoomController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Consultant\DashboardController as ConsultantDashboardController;
use App\Http\Controllers\Consultant\ConsultationController as ConsultantConsultationController;
use App\Http\Controllers\Consultant\AppointmentController as ConsultantAppointmentController;
use App\Http\Controllers\Consultant\ScheduleController as ConsultantScheduleController;
use App\Http\Controllers\Consultant\ProfileController as ConsultantProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProdiController as AdminProdiController;
use App\Http\Controllers\Admin\ConsultantController as AdminConsultantController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;

// ==========================================
// HALAMAN UTAMA = DASHBOARD MAHASISWA (landing page)
// Bisa dibuka siapa saja TANPA login.
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// Webhook Midtrans (publik, tanpa login & tanpa CSRF; keasliannya diverifikasi lewat signature)
Route::post('/midtrans/notification', [PaymentController::class, 'notification'])
    ->name('midtrans.notification');

// Halaman perantara "Cari Teman Diskusi" (publik) -> tombolnya menuju pilih prodi
Route::view('/cari-teman-diskusi', 'discussion.intro')->name('discussion.intro');

// ==========================================
// LOGIN / REGISTER (hanya untuk yang belum login)
// Login dipakai bersama oleh mahasiswa, konsultan, dan admin.
// Register publik HANYA untuk mahasiswa.
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ==========================================
// ALUR KONSULTASI -> WAJIB LOGIN
// Belum login -> diarahkan ke /login, lalu kembali ke halaman tujuan.
// ==========================================
Route::middleware('auth')->group(function () {
    // Pilih prodi & lihat konsultan (data dari database)
    Route::get('/consultation', [ConsultationController::class, 'prodi'])
        ->name('consultation.prodi');

    Route::get('/consultation/prodi/{prodiId}', [ConsultationController::class, 'consultants'])
        ->name('consultation.consultants');
});

// Memulai chat: hanya mahasiswa
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/consultation/chat/{consultantId}', [ConsultationController::class, 'chat'])
        ->name('consultation.chat');

    // Konsultasi Saya (riwayat + pesan belum dibaca) & Profil mahasiswa
    Route::get('/consultation/history', [ConsultationController::class, 'history'])
        ->name('consultation.history');

    Route::get('/profile', [ProfileController::class, 'show'])
    ->name('profile.show');

// Edit profil mahasiswa
Route::put('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');
    
    // Pilih layanan berbayar (harga & durasi dari server)
    Route::post('/consultation/service/chat/{consultantId}', [ServiceController::class, 'chat'])
        ->name('service.chat');

    Route::get('/consultation/service/online/{consultantId}', [ServiceController::class, 'onlineSchedule'])
        ->name('service.online');

    Route::post('/consultation/service/online/{consultantId}', [ServiceController::class, 'onlineCheckout'])
        ->name('service.online.checkout');

    Route::post('/consultation/service/offline/{consultantId}', [ServiceController::class, 'offline'])
        ->name('service.offline');

    // Pembayaran Midtrans
    Route::get('/payment/{orderId}', [PaymentController::class, 'show'])->name('payment.show');
    Route::get('/payment/{orderId}/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::post('/payment/{orderId}/simulate', [PaymentController::class, 'simulate'])->name('payment.simulate');
    Route::post('/payment/appointment/{appointmentId}', [PaymentController::class, 'payAppointment'])->name('payment.appointment');

    // Pengajuan Offline Meet dari dalam chat koordinasi
    Route::post('/consultation/room/{consultation}/appointments', [ConsultationRoomController::class, 'bookAppointment'])
        ->name('consultation.appointments.store');

    Route::post('/consultation/room/{consultation}/appointments/{appointmentId}/cancel', [ConsultationRoomController::class, 'cancelAppointment'])
        ->name('consultation.appointments.cancel');
});

// Notifikasi pesan: jumlah belum dibaca (polling bel navbar) & pengaturan ON/OFF
Route::middleware(['auth', 'role:user,konsultan'])->group(function () {
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])
        ->middleware('throttle:90,1')
        ->name('notifications.unread');

    Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])
        ->name('profile.notifications');
});

// Ruang chat: mahasiswa & konsultan PEMILIK sesi (dicek lagi di controller)
Route::middleware(['auth', 'role:user,konsultan'])->group(function () {
    Route::get('/consultation/room/{consultation}', [ConsultationRoomController::class, 'show'])
        ->name('consultation.room');

    Route::get('/consultation/room/{consultation}/messages', [ConsultationRoomController::class, 'messages'])
        ->name('consultation.messages');

    Route::post('/consultation/room/{consultation}/messages', [ConsultationRoomController::class, 'store'])
        ->middleware('throttle:40,1')
        ->name('consultation.messages.store');

    Route::post('/consultation/room/{consultation}/close', [ConsultationRoomController::class, 'close'])
        ->name('consultation.close');
});

// ==========================================
// ROUTE YANG MEMBUTUHKAN LOGIN (SEMUA ROLE)
// ==========================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Mengarahkan ke halaman sesuai role
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // CATATAN: route lama /consultants, /appointments, /chats DIHAPUS karena belum punya tampilan
    // dan tidak memeriksa kepemilikan data. Penggantinya:
    //   - daftar konsultan  -> /consultation/prodi/{id}
    //   - chat              -> consultation.room
    //   - janji meet        -> consultation.appointments.* & consultant.appointments.*
});

// ==========================================
// KHUSUS KONSULTAN  (/consultant/...)
// auth + role:konsultan; data selalu dibatasi ke konsultan yang login (di controller)
// ==========================================
Route::middleware(['auth', 'role:konsultan'])
    ->prefix('consultant')
    ->name('consultant.')
    ->group(function () {

        Route::get('/dashboard', [ConsultantDashboardController::class, 'index'])->name('dashboard');

        // Konsultasi
        Route::get('/consultations', [ConsultantConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/consultations/{id}', [ConsultantConsultationController::class, 'show'])->name('consultations.show');
        Route::post('/consultations/{id}/close', [ConsultantConsultationController::class, 'close'])->name('consultations.close');

        // Janji meet
        Route::patch('/appointments/{id}/approve', [ConsultantAppointmentController::class, 'approve'])->name('appointments.approve');
        Route::patch('/appointments/{id}/reject', [ConsultantAppointmentController::class, 'reject'])->name('appointments.reject');
        Route::patch('/appointments/{id}/complete', [ConsultantAppointmentController::class, 'complete'])->name('appointments.complete');

        // Jadwal ketersediaan
        Route::get('/schedules', [ConsultantScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/schedules', [ConsultantScheduleController::class, 'store'])->name('schedules.store');
        Route::put('/schedules/{id}', [ConsultantScheduleController::class, 'update'])->name('schedules.update');
        Route::patch('/schedules/{id}/toggle', [ConsultantScheduleController::class, 'toggle'])->name('schedules.toggle');
        Route::delete('/schedules/{id}', [ConsultantScheduleController::class, 'destroy'])->name('schedules.destroy');

        // Profil (hanya lihat)
        Route::get('/profile', [ConsultantProfileController::class, 'show'])->name('profile');
    });

// ==========================================
// KHUSUS ADMINISTRATOR  (/admin/...)
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Program studi
        Route::resource('prodi', AdminProdiController::class)
            ->except(['show'])
            ->parameters(['prodi' => 'prodi']);

        // Konsultan (+ akun login)
        Route::patch('consultants/{consultant}/toggle', [AdminConsultantController::class, 'toggle'])
            ->name('consultants.toggle');
        Route::resource('consultants', AdminConsultantController::class);

        // Mahasiswa (lihat saja)
        Route::get('students', [AdminStudentController::class, 'index'])->name('students.index');

        // Pembayaran (lihat saja)
        Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');

        // Konsultasi (pemantauan, lihat saja)
        Route::get('consultations', [AdminConsultationController::class, 'index'])->name('consultations.index');
    });
