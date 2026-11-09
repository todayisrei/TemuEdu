<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultant;
use App\Models\Consultation;
use App\Models\Prodi;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'mahasiswa'       => User::where('role', 'user')->count(),
            'konsultan'       => Consultant::count(),
            'konsultan_aktif' => Consultant::where('is_active', true)->count(),
            'prodi'           => Prodi::count(),
            'konsultasi'      => Consultation::count(),
            'janji_pending'   => Appointment::where('status', 'pending')->count(),
        ];

        $prodis = Prodi::withCount('consultants')->orderBy('id')->get();
        $maxKonsultan = max(1, (int) $prodis->max('consultants_count'));

        $latestConsultants = Consultant::with(['user', 'prodi'])->latest()->take(5)->get();
        $latestStudents    = User::where('role', 'user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'prodis', 'maxKonsultan', 'latestConsultants', 'latestStudents'
        ));
    }
}
