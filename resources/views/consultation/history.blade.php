@extends('layouts.student')

@section('title', 'Konsultasi Saya | Temu Edu')

@push('styles')
<style>
  .page { padding:34px 0 70px; }
  .head { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:22px; }
  .head h1 { font-size:34px; }
  .head p { margin:6px 0 0; color:var(--muted); }
  .total { padding:10px 16px; border-radius:14px; background:#fff; border:1px solid var(--line); font-weight:700; color:var(--purple-dark); }
  .total b { color:#e5484d; }

  .item { margin-bottom:14px; border:1px solid var(--line); border-radius:20px; background:#fff; overflow:hidden; }
  .item-main { display:flex; align-items:center; gap:16px; padding:16px 18px; }
  a.item-main:hover { background:#fdfbff; }
  .item-main img { flex:0 0 54px; width:54px; height:54px; border-radius:50%; object-fit:cover; background:var(--purple-soft); }
  .mid { flex:1; min-width:0; }
  .mid b { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
  .mid small { display:block; margin-top:2px; color:var(--muted); }
  .mid p { margin:5px 0 0; color:#4a475c; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .end { flex:0 0 auto; display:flex; flex-direction:column; gap:6px; align-items:flex-end; }
  .end small { color:var(--muted); font-size:12px; }
  .badge.warn { background:#fff0e4; color:#c97432; }
  .tag { padding:3px 9px; border-radius:8px; background:#f6f0ff; color:var(--purple-dark); font-size:11.5px; font-weight:700; }

  .appts { padding:4px 18px 16px; border-top:1px dashed #eee8f2; background:#fcfaff; }
  .appt { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:11px 0; border-bottom:1px solid #f1ecf6; font-size:14px; }
  .appt:last-child { border-bottom:0; }
  .appt .act { display:flex; gap:8px; align-items:center; flex-wrap:wrap; } .appt form { margin:0; }
  .linkbtn { border:0; background:none; color:#d6455d; font:700 13px "DM Sans",sans-serif; cursor:pointer; padding:0; }

  .payrow { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:12px 18px; border-top:1px dashed #eee8f2; background:#fff9f1; font-size:14px; }
  .empty { padding:56px 20px; text-align:center; color:var(--muted); line-height:1.8; }
  .empty i { display:block; margin-bottom:14px; font-size:42px; color:#d6c9e8; }
  .pager { display:flex; gap:6px; justify-content:center; margin-top:22px; flex-wrap:wrap; }
  .pg { min-width:38px; height:38px; display:grid; place-items:center; padding:0 10px; border:1px solid #e3d9ee; border-radius:11px; background:#fff; font-weight:700; color:var(--purple-dark); }
  .pg.active { background:var(--purple); border-color:var(--purple); color:#fff; } .pg.disabled { opacity:.45; }
  @media (max-width:560px) { .item-main { flex-wrap:wrap; } .end { flex-direction:row; width:100%; justify-content:space-between; align-items:center; } }
</style>
@endpush

@section('content')
  <main class="page">
    <div class="container">
      <div class="head">
        <div>
          <h1>Konsultasi Saya</h1>
          <p>Semua layanan, pembayaran, dan percakapanmu dengan konsultan ada di sini.</p>
        </div>
        <div class="total">
    {{ $totalUnread }} pesan belum dibaca
    @if ($totalUnread > 0)
        <b>&bull;</b>
    @endif
</div>
      </div>

      @forelse ($consultations as $c)
        @php
          $svc = config('temuedu.services.' . $c->service);
          $locked = ! $c->isAccessible();
        @endphp

        <div class="item">
          @if ($locked)
            <div class="item-main">
          @else
            <a href="{{ route('consultation.room', $c) }}" class="item-main">
          @endif
              <img src="{{ $c->consultant->avatar_url }}" alt="{{ $c->consultant->user->name }}">
              <div class="mid">
                <b>
                  {{ $c->consultant->user->name }}
                  <span class="tag"><i class="fa-solid {{ $svc['icon'] ?? 'fa-comments' }}"></i> {{ $svc['label'] ?? $c->service }}</span>
                  @if ($c->unread_count > 0)<span class="badge new">{{ $c->unread_count }} baru</span>@endif
                </b>
                <small>{{ $c->prodi->nama_prodi }}@if ($c->topik) &middot; {{ \Illuminate\Support\Str::limit($c->topik, 50) }}@endif</small>
                @if ($c->lastMessage)<p>{{ \Illuminate\Support\Str::limit($c->lastMessage->isi, 90) }}</p>@endif
              </div>
              <div class="end">
                {{-- status pembayaran --}}
                @if ($c->service === 'offline_meet')
                  <span class="badge">Koordinasi</span>
                @elseif ($c->is_paid)
                  <span class="badge ok">Lunas</span>
                @else
                  <span class="badge warn">Menunggu pembayaran</span>
                @endif
                {{-- status sesi --}}
                @if ($locked)
                  <small>Belum aktif</small>
                @elseif ($c->isChatOpen())
                  <span class="badge ok">Berlangsung</span>
                @elseif ($c->status === 'closed')
                  <span class="badge">Selesai</span>
                @else
                  <span class="badge off">Berakhir</span>
                @endif
              </div>
          @if ($locked)
            </div>
          @else
            </a>
          @endif

          @if ($locked && $c->openPayment)
            <div class="payrow">
              <span>Selesaikan pembayaran Rp{{ number_format($c->openPayment->amount, 0, ',', '.') }} agar layanan aktif.</span>
              <a href="{{ route('payment.show', $c->openPayment->order_id) }}" class="btn btn-sm">Lanjutkan pembayaran</a>
            </div>
          @endif

          @if ($c->appointments->isNotEmpty())
            <div class="appts">
              @foreach ($c->appointments->sortByDesc('id') as $a)
                @php
                  $label = ['pending' => 'Menunggu konsultan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'][$a->status] ?? $a->status;
                  $canPay = $a->payment_status === 'unpaid' && (
                      ($a->tipe === 'offline_meet' && $a->status === 'approved') ||
                      ($a->tipe === 'online_meet' && $a->status === 'pending'));
                  $canCancel = $a->payment_status === 'unpaid' && in_array($a->status, ['pending', 'approved'], true);
                @endphp
                <div class="appt">
                  <div>
                    <b>{{ $a->tipe === 'online_meet' ? 'Online Meet' : 'Offline Meet' }}</b>
                    &middot; {{ $a->schedule->tanggal->translatedFormat('d M Y') }}, {{ substr($a->schedule->jam_mulai, 0, 5) }}-{{ substr($a->schedule->jam_selesai, 0, 5) }}
                    <br>
                    <span class="badge {{ in_array($a->status, ['approved', 'completed']) ? 'ok' : (in_array($a->status, ['rejected', 'cancelled']) ? 'off' : '') }}">{{ $label }}</span>
                    @if ($a->payment_status === 'paid')<span class="badge ok">Lunas</span>@elseif (! in_array($a->status, ['rejected', 'cancelled']))<span class="badge warn">Belum dibayar</span>@endif
                    @if ($a->payment_status === 'paid' && $a->status === 'approved')
                      <span style="color:var(--muted)">
                        @if ($a->tipe === 'online_meet' && $a->link_meeting) &middot; <a href="{{ $a->link_meeting }}" target="_blank" rel="noopener" style="color:var(--purple);font-weight:700">Buka link meeting</a>@endif
                        @if ($a->tipe === 'offline_meet' && $a->lokasi) &middot; Lokasi: {{ $a->lokasi }}@endif
                      </span>
                    @elseif ($a->tipe === 'offline_meet' && $a->lokasi && ! in_array($a->status, ['rejected', 'cancelled']))
                      <span style="color:var(--muted)"> &middot; Lokasi: {{ $a->lokasi }}</span>
                    @endif
                  </div>
                  <div class="act">
                    @if ($canPay)
                      <form method="POST" action="{{ route('payment.appointment', $a->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm">Bayar Rp{{ number_format($a->harga ?? config('temuedu.services.' . $a->tipe . '.price'), 0, ',', '.') }}</button>
                      </form>
                    @endif
                    @if ($canCancel)
                      <form method="POST" action="{{ route('consultation.appointments.cancel', [$c, $a->id]) }}" onsubmit="return confirm('Batalkan janji ini?')">
                        @csrf
                        <button type="submit" class="linkbtn">Batalkan</button>
                      </form>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @empty
        <div class="card empty">
          <i class="fa-regular fa-comments"></i>
          Kamu belum punya konsultasi.<br>
          <a href="{{ route('discussion.intro') }}" class="btn" style="margin-top:16px">Cari Teman Diskusi</a>
        </div>
      @endforelse

      @if ($consultations->hasPages())
        <nav class="pager">
          @if ($consultations->onFirstPage())<span class="pg disabled">&lsaquo;</span>@else<a class="pg" href="{{ $consultations->previousPageUrl() }}">&lsaquo;</a>@endif
          <span class="pg active">{{ $consultations->currentPage() }}</span>
          @if ($consultations->hasMorePages())<a class="pg" href="{{ $consultations->nextPageUrl() }}">&rsaquo;</a>@else<span class="pg disabled">&rsaquo;</span>@endif
        </nav>
      @endif
    </div>
  </main>
@endsection
