@extends('layouts.admin')

@section('title', 'Detail Konsultan')
@section('heading', 'Detail Konsultan')

@section('top_action')
  <a href="{{ route('admin.consultants.edit', $consultant) }}" class="btn"><i class="fa-solid fa-pen"></i> Edit</a>
@endsection

@section('content')
  <div class="grid-2">
    <div class="card">
      <div class="person" style="margin-bottom:20px">
        <div class="ava" style="width:72px;height:72px;flex-basis:72px;border-radius:22px;font-size:26px">
          @if ($consultant->foto_url)<img src="{{ $consultant->foto_url }}" alt="{{ $consultant->user->name }}">@else{{ strtoupper(mb_substr($consultant->user->name, 0, 1)) }}@endif
        </div>
        <div>
          <b style="font-size:19px">{{ $consultant->user->name }}</b>
          <small>{{ $consultant->user->email }}</small><br>
          @if ($consultant->is_active)<span class="badge ok">Aktif</span>@else<span class="badge off">Nonaktif</span>@endif
        </div>
      </div>

      <table>
        <tbody>
          <tr><td class="muted" style="width:150px">Program studi</td><td>{{ $consultant->prodi->nama_prodi }}</td></tr>
          <tr><td class="muted">Alumni</td><td>{{ $consultant->alumni ?: '-' }}</td></tr>
          <tr><td class="muted">IPK</td><td>{{ $consultant->ipk ?? '-' }}</td></tr>
          <tr><td class="muted">Domisili</td><td>{{ $consultant->domisili ?: '-' }}</td></tr>
          <tr><td class="muted">Tarif</td><td>Rp {{ number_format($consultant->harga, 0, ',', '.') }}</td></tr>
          <tr><td class="muted">Rating</td><td>@if ($consultant->rating_count > 0){{ $consultant->rating_avg }} ({{ $consultant->rating_count }} ulasan)@else Belum ada ulasan @endif</td></tr>
          <tr><td class="muted">Portofolio</td><td>@if ($consultant->portofolio_link)<a href="{{ $consultant->portofolio_link }}" target="_blank" rel="noopener" style="color:var(--purple);font-weight:700">{{ $consultant->portofolio_link }}</a>@else - @endif</td></tr>
          <tr><td class="muted">Kompetensi</td><td>{{ $consultant->kompetensi ?: '-' }}</td></tr>
          <tr><td class="muted">Dibuat</td><td>{{ $consultant->created_at->format('d M Y H:i') }}</td></tr>
        </tbody>
      </table>
    </div>

    <div>
      <div class="stats" style="grid-template-columns:1fr 1fr 1fr">
        <div class="stat" style="flex-direction:column;align-items:flex-start"><b>{{ $consultant->consultations_count }}</b><span>Konsultasi</span></div>
        <div class="stat" style="flex-direction:column;align-items:flex-start"><b>{{ $consultant->appointments_count }}</b><span>Janji meet</span></div>
        <div class="stat" style="flex-direction:column;align-items:flex-start"><b>{{ $consultant->reviews_count }}</b><span>Ulasan</span></div>
      </div>

      <div class="card">
        <div class="card-head"><h2>Konsultasi terbaru</h2></div>
        @forelse ($latestConsultations as $c)
          <div class="mini">
            <div class="ava">{{ strtoupper(mb_substr($c->user->name, 0, 1)) }}</div>
            <div><b>{{ $c->user->name }}</b><small>{{ $c->topik ?: $c->prodi->nama_prodi }}</small></div>
            <div class="when">{{ $c->created_at->diffForHumans() }}</div>
          </div>
        @empty
          <div class="empty"><i class="fa-solid fa-comments"></i>Belum ada konsultasi.</div>
        @endforelse
      </div>
    </div>
  </div>

  <p style="margin-top:20px"><a href="{{ route('admin.consultants.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Kembali ke daftar</a></p>
@endsection
