<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /** Pemantauan konsultasi (lihat saja, admin tidak mengubah/membaca isi chat). */
    public function index(Request $request)
    {
        $consultations = Consultation::with(['user', 'consultant.user', 'prodi'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.consultations.index', compact('consultations'));
    }
}
