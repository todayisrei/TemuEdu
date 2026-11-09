@extends('layouts.consultant')

@section('title', 'Detail Konsultasi')
@section('heading', 'Detail Konsultasi')

@section('top_action')
  <a href="{{ route('consultation.room', $consultation) }}" class="btn"><i class="fa-solid fa-comment-dots"></i> Buka Chat</a>
@endsection

@section('content')
  @php $mulai = $consultation->started_at ?? $consultation->created_at; @endphp

  <div class="grid-2">
    <div class="card">
      <div class="card-head"><h2>Informasi mahasiswa</h2></div>
      <dl class="kv">
        <dt>Nama</dt><dd>{{ $consultation->user->name }}</dd>
        <dt>Bidang konsultasi</dt><dd>{{ $consultation->prodi->nama_prodi }}</dd>
      </dl>
    </div>

    <div class="card">
      <div class="card-head"><h2>Informasi konsultasi</h2> @include('consultant.partials.status', ['c' => $consultation])</div>
      <dl class="kv">
        <dt>Topik</dt><dd>{{ $consultation->topik ?: '-' }}</dd>
        <dt>Deskripsi</dt><dd>{{ $firstQuestion ? $firstQuestion->isi : '-' }}</dd>
        <dt>Layanan</dt><dd>{{ config('temuedu.services.' . $consultation->service . '.label', $consultation->service) }} &middot; {{ $consultation->service === 'offline_meet' ? 'Koordinasi' : ($consultation->is_paid ? 'Lunas' : 'Belum dibayar') }}</dd>
        <dt>Tanggal</dt><dd>{{ $mulai->translatedFormat('l, d M Y') }}</dd>
        <dt>Waktu</dt><dd>{{ $mulai->format('H:i') }}@if ($consultation->expires_at) &ndash; {{ $consultation->expires_at->format('H:i') }}@endif</dd>
      </dl>

      @if ($consultation->status === 'active')
        <form method="POST" action="{{ route('consultant.consultations.close', $consultation->id) }}" onsubmit="return confirm('Akhiri sesi konsultasi ini?')" style="margin-top:18px">
          @csrf
          <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-flag-checkered"></i> Akhiri sesi</button>
        </form>
      @endif
    </div>
  </div>

  <div class="card">
    <div class="card-head"><h2>Janji meet</h2></div>

    @forelse ($consultation->appointments->sortByDesc('id') as $a)
      <div class="slot" style="align-items:flex-start">
        <div style="flex:1;min-width:230px">
          <b>{{ $a->tipe === 'online_meet' ? 'Online Meet' : 'Offline Meet' }}</b>
          &middot; {{ $a->schedule->tanggal->translatedFormat('l, d M Y') }}, {{ substr($a->schedule->jam_mulai, 0, 5) }}-{{ substr($a->schedule->jam_selesai, 0, 5) }}
          @if ($a->catatan)<div class="muted" style="margin-top:4px">Catatan mahasiswa: {{ $a->catatan }}</div>@endif
          @if ($a->status === 'approved' || $a->status === 'completed')
            <div style="margin-top:4px">
              @if ($a->tipe === 'online_meet')Link: <a href="{{ $a->link_meeting }}" target="_blank" rel="noopener" style="color:var(--purple);font-weight:700">{{ $a->link_meeting }}</a>
              @else Lokasi: <b>{{ $a->lokasi }}</b>@endif
            </div>
          @endif
        </div>

        @php
          $lbl = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'][$a->status] ?? $a->status;
          $cls = ['pending' => 'warn', 'approved' => 'ok', 'completed' => '', 'rejected' => 'off', 'cancelled' => 'off'][$a->status] ?? '';
        @endphp
        <span class="badge {{ $cls }}">{{ $lbl }}</span>
        @if ($a->payment_status === 'paid')<span class="badge ok">Lunas</span>@else<span class="badge warn">Belum dibayar</span>@endif

        <div style="flex:1 1 100%">
          @if ($a->status === 'pending')
            <div class="actions">
              <form method="POST" action="{{ route('consultant.appointments.approve', $a->id) }}" class="inline-form">
                @csrf
                @method('PATCH')
                @if ($a->tipe === 'online_meet')
                  <input type="url" name="link_meeting" placeholder="Link meeting (https://meet.google.com/...)" required>
                @else
                  <input type="text" name="lokasi" value="{{ old('lokasi', $a->lokasi) }}" placeholder="Lokasi bertemu (mis. Perpustakaan lt. 2)" required>
                @endif
                <button type="submit" class="btn btn-sm"><i class="fa-solid fa-check"></i> Setujui</button>
              </form>
              <form method="POST" action="{{ route('consultant.appointments.reject', $a->id) }}" onsubmit="return confirm('Tolak janji ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
              </form>
            </div>
          @elseif ($a->status === 'approved' && $a->payment_status === 'paid')
            <form method="POST" action="{{ route('consultant.appointments.complete', $a->id) }}">
              @csrf
              @method('PATCH')
              <button type="submit" class="btn btn-ghost btn-sm"><i class="fa-solid fa-flag-checkered"></i> Tandai selesai</button>
            </form>
          @elseif ($a->status === 'approved')
            <span class="muted" style="font-size:13.5px">Menunggu mahasiswa menyelesaikan pembayaran.</span>
          @endif
        </div>
      </div>
    @empty
      <div class="empty"><i class="fa-regular fa-calendar"></i>Mahasiswa belum mengajukan janji meet.</div>
    @endforelse
  </div>

  <div class="card">
    <div class="card-head"><h2>Percakapan terakhir</h2><a href="{{ route('consultation.room', $consultation) }}" class="btn btn-ghost btn-sm">Buka chat lengkap</a></div>
    @forelse ($recent as $m)
      <div class="mini">
        <div class="ava">{{ strtoupper(mb_substr((int) $m->sender_id === (int) $consultation->user_id ? $consultation->user->name : auth()->user()->name, 0, 1)) }}</div>
        <div><b>{{ (int) $m->sender_id === (int) $consultation->user_id ? $consultation->user->name : 'Anda' }}</b><small>{{ \Illuminate\Support\Str::limit($m->isi, 140) }}</small></div>
        <div class="when">{{ $m->created_at->format('H:i') }}</div>
      </div>
    @empty
      <div class="empty">Belum ada pesan.</div>
    @endforelse
  </div>

  <p><a href="{{ route('consultant.consultations.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Kembali ke daftar</a></p>
@endsection
