@extends('layouts.consultant')

@section('title', 'Dashboard')
@section('heading', 'Dashboard Konsultan')

@section('content')
  <div class="card" style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;margin-bottom:22px">
    <div class="ava" style="width:64px;height:64px;flex-basis:64px;border-radius:20px">
      <img src="{{ $consultant->avatar_url }}" alt="{{ $user->name }}">
    </div>
    <div style="flex:1;min-width:220px">
      <h2 style="font-size:24px">Halo, {{ $user->name }}</h2>
      <div class="muted" style="margin-top:4px">Kelola konsultasi dan jadwal mahasiswa dari sini.</div>
    </div>
    <span class="badge">{{ $consultant->prodi->nama_prodi }}</span>
  </div>

  <div class="card" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:22px">
    <div style="flex:1;min-width:230px">
      <b>Status jam layanan</b>
      <div class="muted" style="font-size:14px;margin-top:4px">
        @if (! $hasSchedule)
          Kamu belum punya jam layanan aktif. Mahasiswa belum bisa memesan Chat atau Online Meet sampai kamu mengatur jadwal.
        @elseif ($availableNow)
          Kamu sedang dalam jam layanan: bisa menerima dan membalas chat.
        @else
          Kamu sedang di luar jam layanan{{ $next ? ' (berikutnya: ' . $next['label'] . ')' : '' }}. Balasan chat dinonaktifkan.
        @endif
      </div>
    </div>
    @if ($hasSchedule && $availableNow)<span class="badge ok">Tersedia</span>@elseif ($hasSchedule)<span class="badge warn">Di luar jam layanan</span>@else<span class="badge off">Belum diatur</span>@endif
    <a href="{{ route('consultant.schedules.index') }}" class="btn btn-ghost btn-sm">Atur jadwal</a>
  </div>

  <div class="stats four">
    <div class="stat"><div class="ico"><i class="fa-solid fa-comments"></i></div><div><b>{{ $stats['total'] }}</b><span>Total konsultasi</span></div></div>
    <div class="stat"><div class="ico o"><i class="fa-solid fa-hourglass-half"></i></div><div><b>{{ $stats['menunggu'] }}</b><span>Menunggu konfirmasi</span></div></div>
    <div class="stat"><div class="ico p"><i class="fa-solid fa-calendar-day"></i></div><div><b>{{ $stats['hari_ini'] }}</b><span>Konsultasi hari ini</span></div></div>
    <div class="stat"><div class="ico g"><i class="fa-solid fa-circle-check"></i></div><div><b>{{ $stats['selesai'] }}</b><span>Konsultasi selesai</span></div></div>
  </div>

  <div class="card">
    <div class="card-head">
      <h2>Jadwal hari ini</h2>
      <a href="{{ route('consultant.schedules.index') }}" class="btn btn-ghost btn-sm">Kelola jadwal</a>
    </div>

    @forelse ($todayAppointments as $a)
      <div class="slot">
        <b style="min-width:110px">{{ substr($a->schedule->jam_mulai, 0, 5) }} - {{ substr($a->schedule->jam_selesai, 0, 5) }}</b>
        <div style="flex:1;min-width:180px">
          Mahasiswa: <b>{{ $a->user->name }}</b><br>
          <span class="muted">Topik: {{ $a->consultation->topik ?: '-' }} &middot; {{ $a->tipe === 'online_meet' ? 'Online Meet' : 'Offline Meet' }}</span>
        </div>
        @if ($a->status === 'approved')<span class="badge ok">Disetujui</span>@else<span class="badge warn">Menunggu</span>@endif
        <a href="{{ route('consultant.consultations.show', $a->consultation_id) }}" class="btn btn-ghost btn-sm">Detail</a>
      </div>
    @empty
      <div class="empty"><i class="fa-regular fa-calendar"></i>Tidak ada janji meet hari ini.</div>
    @endforelse
  </div>

  <div class="card">
    <div class="card-head">
      <h2>Konsultasi terbaru</h2>
      <a href="{{ route('consultant.consultations.index') }}" class="btn btn-ghost btn-sm">Lihat semua</a>
    </div>

    @if ($latest->isEmpty())
      <div class="empty"><i class="fa-regular fa-comments"></i>Belum ada mahasiswa yang menghubungimu.<br>Konsultasi muncul di sini setelah mahasiswa mengirim pesan pertama.</div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr><th>Mahasiswa</th><th>Topik</th><th>Tanggal</th><th>Status</th><th style="width:100px">Aksi</th></tr></thead>
          <tbody>
            @foreach ($latest as $c)
              <tr>
                <td><b>{{ $c->user->name }}</b> @if ($c->unread_count > 0)<span class="badge new">{{ $c->unread_count }} baru</span>@endif</td>
                <td>{{ \Illuminate\Support\Str::limit($c->topik ?: '-', 50) }}</td>
                <td>{{ ($c->started_at ?? $c->created_at)->translatedFormat('d M Y, H:i') }}</td>
                <td>@include('consultant.partials.status', ['c' => $c])</td>
                <td><a href="{{ route('consultant.consultations.show', $c->id) }}" class="btn btn-ghost btn-sm">Detail</a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endsection
