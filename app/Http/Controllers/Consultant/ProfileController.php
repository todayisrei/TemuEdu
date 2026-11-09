<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /** Profil konsultan (hanya lihat; data dikelola administrator). */
    public function show()
    {
        $user = Auth::user();
        $consultant = $user->consultant()->with('prodi')->firstOrFail();

        return view('consultant.profile', compact('user', 'consultant'));
    }
}
