<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultant;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ConsultantController extends Controller
{
    public function index(Request $request)
    {
        $consultants = Consultant::with(['user', 'prodi'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $s = $request->q;
                $query->whereHas('user', function ($u) use ($s) {
                    $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
                });
            })
            ->when($request->filled('prodi'), fn ($q) => $q->where('prodi_id', $request->prodi))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'aktif'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $prodis = Prodi::orderBy('id')->get();

        return view('admin.consultants.index', compact('consultants', 'prodis'));
    }

    public function create()
    {
        $prodis = Prodi::orderBy('id')->get();

        return view('admin.consultants.create', compact('prodis'));
    }

    /** Membuat AKUN LOGIN (users) + PROFIL (consultants) sekaligus. */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());

        DB::transaction(function () use ($request, $data) {
            $user = new User();
            $user->forceFill([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => $data['password'], // di-hash otomatis (cast 'hashed')
                'role'              => 'konsultan',
                'email_verified_at' => now(),
            ])->save();

            Consultant::create([
                'user_id'         => $user->id,
                'prodi_id'        => $data['prodi_id'],
                'foto'            => $request->hasFile('foto') ? $request->file('foto')->store('consultants', 'public') : null,
                'domisili'        => $data['domisili'] ?? null,
                'alumni'          => $data['alumni'] ?? null,
                'ipk'             => $data['ipk'] ?? null,
                'harga'           => $data['harga'],
                'kompetensi'      => $data['kompetensi'] ?? null,
                'portofolio_link' => $data['portofolio_link'] ?? null,
                'is_active'       => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Akun konsultan berhasil dibuat. Berikan email & password-nya kepada konsultan.');
    }

    public function show(Consultant $consultant)
    {
        $consultant->load(['user', 'prodi'])->loadCount(['consultations', 'appointments', 'reviews']);

        $latestConsultations = $consultant->consultations()->with(['user', 'prodi'])->latest()->take(5)->get();

        return view('admin.consultants.show', compact('consultant', 'latestConsultations'));
    }

    public function edit(Consultant $consultant)
    {
        $consultant->load('user');
        $prodis = Prodi::orderBy('id')->get();

        return view('admin.consultants.edit', compact('consultant', 'prodis'));
    }

    public function update(Request $request, Consultant $consultant)
    {
        $data = $request->validate($this->rules($consultant), $this->messages());

        DB::transaction(function () use ($request, $data, $consultant) {
            $user = $consultant->user;
            $user->name  = $data['name'];
            $user->email = $data['email'];

            // Password hanya diubah kalau diisi (reset password)
            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }
            $user->save();

            $foto = $consultant->foto;
            if ($request->hasFile('foto')) {
                if ($foto) {
                    Storage::disk('public')->delete($foto);
                }
                $foto = $request->file('foto')->store('consultants', 'public');
            }

            $consultant->update([
                'prodi_id'        => $data['prodi_id'],
                'foto'            => $foto,
                'domisili'        => $data['domisili'] ?? null,
                'alumni'          => $data['alumni'] ?? null,
                'ipk'             => $data['ipk'] ?? null,
                'harga'           => $data['harga'],
                'kompetensi'      => $data['kompetensi'] ?? null,
                'portofolio_link' => $data['portofolio_link'] ?? null,
                'is_active'       => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.consultants.index')->with('success', 'Data konsultan berhasil diperbarui.');
    }

    /** Aktif / nonaktifkan konsultan (nonaktif = tidak tampil di daftar mahasiswa). */
    public function toggle(Consultant $consultant)
    {
        $consultant->update(['is_active' => ! $consultant->is_active]);

        return back()->with('success', 'Status konsultan diubah menjadi ' . ($consultant->is_active ? 'aktif' : 'nonaktif') . '.');
    }

    public function destroy(Consultant $consultant)
    {
        // Jangan hapus yang sudah punya riwayat chat/janji. Sesi kosong (belum ada pesan) boleh ikut terhapus.
        if ($consultant->consultations()->whereHas('messages')->exists() || $consultant->appointments()->exists() || $consultant->payments()->exists()) {
            return back()->with('error', 'Konsultan sudah punya riwayat konsultasi, jadi tidak bisa dihapus. Nonaktifkan saja.');
        }

        if ($consultant->foto) {
            Storage::disk('public')->delete($consultant->foto);
        }

        // Menghapus user otomatis menghapus baris consultants (cascade)
        $consultant->user->delete();

        return redirect()->route('admin.consultants.index')->with('success', 'Konsultan berhasil dihapus.');
    }

    private function rules(?Consultant $consultant = null): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($consultant?->user_id)],
            'password'        => [$consultant ? 'nullable' : 'required', 'string', 'min:8'],
            'prodi_id'        => ['required', 'exists:prodis,id'],
            'foto'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'domisili'        => ['nullable', 'string', 'max:255'],
            'alumni'          => ['nullable', 'string', 'max:255'],
            'ipk'             => ['nullable', 'numeric', 'between:0,4'],
            'harga'           => ['required', 'integer', 'min:0'],
            'kompetensi'      => ['nullable', 'string', 'max:2000'],
            'portofolio_link' => ['nullable', 'url', 'max:255'],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah dipakai akun lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'prodi_id.required'  => 'Program studi wajib dipilih.',
            'prodi_id.exists'    => 'Program studi tidak valid.',
            'foto.image'         => 'File harus berupa gambar.',
            'foto.mimes'         => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'foto.max'           => 'Ukuran foto maksimal 2 MB.',
            'ipk.numeric'        => 'IPK harus berupa angka.',
            'ipk.between'        => 'IPK harus antara 0 sampai 4.',
            'harga.required'     => 'Tarif wajib diisi.',
            'harga.integer'      => 'Tarif harus berupa angka bulat (Rupiah).',
            'portofolio_link.url'=> 'Link harus diawali http:// atau https://',
        ];
    }
}
