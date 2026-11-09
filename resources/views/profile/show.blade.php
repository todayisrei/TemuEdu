
@extends('layouts.student')

@section('title', 'Profil Saya | Temu Edu')

@push('styles')
<style>
    .page {
        padding: 34px 0 70px;
    }

    .wrap {
        max-width: 720px;
        margin: 0 auto;
    }

    .top {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .top img {
        width: 92px;
        height: 92px;
        border-radius: 26px;
        background: var(--purple-soft);
    }

    .top h1 {
        font-size: 28px;
    }

    .top p {
        margin: 4px 0 8px;
        color: var(--muted);
    }

    .info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin: 0;
    }

    .info div {
        padding: 14px 16px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #faf8fd;
    }

    .info dt {
        color: var(--muted);
        font-size: 13px;
    }

    .info dd {
        margin: 3px 0 0;
        font-weight: 700;
        font-size: 16px;
        word-break: break-word;
    }

    .sec {
        margin: 26px 0 14px;
        font-size: 19px;
    }

    .edit-section {
        margin-top: 24px;
        border: 1px solid var(--line);
        border-radius: 14px;
        overflow: hidden;
    }

    .edit-section summary {
        padding: 16px;
        cursor: pointer;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: space-between;
        list-style: none;
    }

    .edit-section summary::-webkit-details-marker {
        display: none;
    }

    .edit-section summary::after {
        content: '+';
        font-size: 22px;
        color: var(--muted);
    }

    .edit-section[open] summary::after {
        content: '−';
    }

    .edit-form {
        padding: 0 16px 18px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 7px;
        font-weight: 600;
        font-size: 14px;
    }

    .form-group input {
        width: 100%;
        padding: 11px 13px;
        border: 1px solid var(--line);
        border-radius: 10px;
        font: inherit;
        outline: none;
    }

    .form-group input:focus {
        border-color: var(--purple, #7661a8);
    }

    .form-error {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .alert-success {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #e8f7ed;
        color: #247a43;
    }

    .alert-error {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #fdecec;
        color: #b42318;
    }

    .btn-save {
        padding: 11px 20px;
        border: none;
        border-radius: 10px;
        background: var(--purple, #7661a8);
        color: white;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-save:hover {
        opacity: .9;
    }

    @media (max-width: 560px) {
        .info {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<main class="page">
    <div class="container wrap">
        <div class="card">

            {{-- Notifikasi berhasil --}}
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Notifikasi kesalahan --}}
            @if ($errors->any())
                <div class="alert-error">
                    <strong>Periksa kembali data yang kamu masukkan:</strong>
                    <ul style="margin: 8px 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Informasi profil --}}
            <div class="top">
                <img src="{{ $avatar }}" alt="{{ $user->name }}">

                <div>
                    <h1>{{ $user->name }}</h1>
                    <p>{{ $user->email }}</p>
                    <span class="badge">Mahasiswa</span>
                </div>
            </div>

            {{-- Detail akun --}}
            <dl class="info">
                <div>
                    <dt>Nama</dt>
                    <dd>{{ $user->name }}</dd>
                </div>

                <div>
                    <dt>Email</dt>
                    <dd>{{ $user->email }}</dd>
                </div>

                <div>
                    <dt>Bergabung sejak</dt>
                    <dd>{{ $user->created_at->translatedFormat('d F Y') }}</dd>
                </div>

                <div>
                    <dt>Total konsultasi</dt>
                    <dd>{{ $totalKonsultasi }}</dd>
                </div>

                <div>
                    <dt>Pesan belum dibaca</dt>
                    <dd>{{ $unread }}</dd>
                </div>

                <div>
                    <dt>Peran akun</dt>
                    <dd>Mahasiswa</dd>
                </div>
            </dl>

            {{-- Form edit profil --}}
            <details class="edit-section" {{ $errors->any() ? 'open' : '' }}>
                <summary>
                    Edit Profil
                </summary>

                <form
                    action="{{ route('profile.update') }}"
                    method="POST"
                    class="edit-form"
                >
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Masukkan nama lengkap"
                            required
                            maxlength="255"
                        >

                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="Masukkan email"
                            required
                            maxlength="255"
                        >

                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-save">
                        Simpan Perubahan
                    </button>
                </form>
            </details>

            {{-- Pengaturan notifikasi --}}
            <h2 class="sec">Pengaturan Notifikasi</h2>

            @include('partials.notification-settings', ['user' => $user])

        </div>
    </div>
</main>
@endsection
