<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /** Daftar mahasiswa (role 'user'), hanya untuk dilihat. */
    public function index(Request $request)
    {
        $students = User::where('role', 'user')
            ->withCount('consultations')
            ->when($request->filled('q'), function ($q) use ($request) {
                $s = $request->q;
                $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }
}
