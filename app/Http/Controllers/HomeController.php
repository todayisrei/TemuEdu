<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /** Landing page = beranda mahasiswa (bisa dibuka tanpa login). */
    public function index()
    {
        return view('welcome');
    }

    /** Pintu masuk umum: arahkan ke halaman yang sesuai dengan role. */
    public function dashboard()
    {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'konsultan' => redirect()->route('consultant.dashboard'),
            default => redirect('/'),
        };
    }
}
