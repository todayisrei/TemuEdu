<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProdiController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount('consultants')->orderBy('id')->get();

        return view('admin.prodi.index', compact('prodis'));
    }

    public function create()
    {
        return view('admin.prodi.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_prodi' => ['required', 'string', 'max:100', 'unique:prodis,nama_prodi'],
        ], [
            'nama_prodi.required' => 'Nama program studi wajib diisi.',
            'nama_prodi.unique'   => 'Program studi tersebut sudah ada.',
        ]);

        Prodi::create($data);

        return redirect()->route('admin.prodi.index')->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function edit(Prodi $prodi)
    {
        return view('admin.prodi.edit', compact('prodi'));
    }

    public function update(Request $request, Prodi $prodi)
    {
        $data = $request->validate([
            'nama_prodi' => ['required', 'string', 'max:100', Rule::unique('prodis', 'nama_prodi')->ignore($prodi->id)],
        ], [
            'nama_prodi.required' => 'Nama program studi wajib diisi.',
            'nama_prodi.unique'   => 'Program studi tersebut sudah ada.',
        ]);

        $prodi->update($data);

        return redirect()->route('admin.prodi.index')->with('success', 'Program studi berhasil diperbarui.');
    }

    public function destroy(Prodi $prodi)
    {
        // Tabel consultants & consultations memakai restrictOnDelete, jadi cek dulu.
        if ($prodi->consultants()->exists()) {
            return back()->with('error', 'Prodi tidak bisa dihapus karena masih dipakai oleh konsultan.');
        }

        $prodi->delete();

        return redirect()->route('admin.prodi.index')->with('success', 'Program studi berhasil dihapus.');
    }
}
