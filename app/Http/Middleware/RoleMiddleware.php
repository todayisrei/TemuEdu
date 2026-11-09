<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi akses berdasarkan kolom users.role (dicek di SERVER).
 *
 * Pemakaian di route:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,konsultan')   // boleh lebih dari satu role
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Belum login -> ke halaman login
        if (! $user) {
            return redirect()->route('login');
        }

        // Konsultan yang dinonaktifkan admin langsung dikeluarkan,
        // walaupun sesi loginnya masih aktif.
        if ($user->role === 'konsultan' && ! optional($user->consultant)->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun konsultan Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        // Role tidak sesuai -> 403
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
