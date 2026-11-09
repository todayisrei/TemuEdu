@extends('layouts.consultant')

@section('title', 'Profil')
@section('heading', 'Profil Saya')

@section('content')
  <div class="card" style="max-width:760px">
    <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;margin-bottom:22px">
      <div class="ava" style="width:84px;height:84px;flex-basis:84px;border-radius:24px"><img src="{{ $consultant->avatar_url }}" alt="{{ $user->name }}"></div>
      <div>
        <h2 style="font-size:23px">{{ $user->name }}</h2>
        <div class="muted">{{ $user->email }}</div>
        <div style="margin-top:6px">
          @if ($consultant->is_active)<span class="badge ok">Akun aktif</span>@else<span class="badge off">Nonaktif</span>@endif
        </div>
      </div>
    </div>

    <dl class="kv">
      <dt>Program studi</dt><dd>{{ $consultant->prodi->nama_prodi }}</dd>
      <dt>Keahlian</dt><dd>{{ $consultant->kompetensi ?: '-' }}</dd>
      <dt>Alumni</dt><dd>{{ $consultant->alumni ?: '-' }}</dd>
      <dt>IPK</dt><dd>{{ $consultant->ipk ?? '-' }}</dd>
      <dt>Domisili</dt><dd>{{ $consultant->domisili ?: '-' }}</dd>
      <dt>Tarif per sesi</dt><dd>Rp {{ number_format($consultant->harga, 0, ',', '.') }}</dd>
      <dt>Portofolio</dt>
      <dd>@if ($consultant->portofolio_link)<a href="{{ $consultant->portofolio_link }}" target="_blank" rel="noopener" style="color:var(--purple)">{{ $consultant->portofolio_link }}</a>@else - @endif</dd>
      <dt>Rating</dt><dd>@if ($consultant->rating_count > 0){{ $consultant->rating_avg }} ({{ $consultant->rating_count }} ulasan)@else Belum ada ulasan @endif</dd>
    </dl>

    <h2 style="margin:26px 0 0;font-size:19px">Pengaturan Notifikasi</h2>
    @include('partials.notification-settings', ['user' => $user])

    <p class="muted" style="margin:22px 0 0;font-size:13.5px"><i class="fa-solid fa-circle-info"></i> Data profil dikelola oleh administrator. Hubungi administrator jika ada yang perlu diubah.</p>
  </div>
@endsection
