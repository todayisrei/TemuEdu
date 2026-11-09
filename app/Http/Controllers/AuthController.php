<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Login bersama untuk mahasiswa, konsultan, dan admin.
     * Role dibaca dari database, BUKAN dari input pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang kamu masukkan salah.',
            ]);
        }

        $user = Auth::user();

        // Konsultan yang dinonaktifkan tidak boleh login
        if ($user->role === 'konsultan' && ! optional($user->consultant)->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun konsultan kamu belum aktif atau sedang dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        // Admin & konsultan selalu ke dashboard masing-masing.
        // Mahasiswa kembali ke halaman yang tadi ingin dibuka (mis. konsultasi), atau ke beranda.
        if ($user->role === 'user') {
            return redirect()->intended(url('/'));
        }

        return redirect()->to($this->homeFor($user));
    }

    public function showRegister()
    {
        return view('register');
    }

    /**
     * Pendaftaran publik HANYA untuk mahasiswa.
     * Role tidak diambil dari form -> admin & konsultan tidak bisa dibuat lewat sini.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
        ]);

        $user = new User();
        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // di-hash otomatis oleh cast 'hashed'
            'role' => 'user',                      // 'user' = mahasiswa
        ])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(url('/'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /** Tujuan setelah login sesuai role. */
    private function homeFor(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'konsultan' => route('consultant.dashboard'),
            default => url('/'),
        };
    }
}
