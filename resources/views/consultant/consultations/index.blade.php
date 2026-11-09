@extends('layouts.consultant')

@section('title', 'Konsultasi')
@section('heading', 'Konsultasi')

@section('content')
  <div class="tabs">
    <a href="{{ route('consultant.consultations.index') }}" class="{{ ! $status ? 'active' : '' }}">Semua</a>
    <a href="{{ route('consultant.consultations.index', ['status' => 'menunggu']) }}" class="{{ $status === 'menunggu' ? 'active' : '' }}">Menunggu</a>
    <a href="{{ route('consultant.consultations.index', ['status' => 'berlangsung']) }}" class="{{ $status === 'berlangsung' ? 'active' : '' }}">Berlangsung</a>
    <a href="{{ route('consultant.consultations.index', ['status' => 'selesai']) }}" class="{{ $status === 'selesai' ? 'active' : '' }}">Selesai</a>
  </div>

  <div class="card">
    <form method="GET" class="toolbar">
      @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama mahasiswa...">
      <button type="submit" class="btn btn-ghost"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
    </form>

    @if ($consultations->isEmpty())
      <div class="empty"><i class="fa-regular fa-comments"></i>Belum ada konsultasi pada kategori ini.</div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Mahasiswa</th><th>Bidang</th><th>Topik</th><th>Metode</th><th>Tanggal</th><th>Waktu</th><th>Status</th><th style="width:170px">Aksi</th></tr>
          </thead>
          <tbody>
            @foreach ($consultations as $c)
              @php
                $metode = ['Chat'];
                foreach ($c->appointments->pluck('tipe')->unique() as $t) {
                    $metode[] = $t === 'online_meet' ? 'Online Meet' : 'Offline Meet';
                }
                $mulai = $c->started_at ?? $c->created_at;
              @endphp
              <tr>
                <td><b>{{ $c->user->name }}</b> @if ($c->unread_count > 0)<span class="badge new">{{ $c->unread_count }} baru</span>@endif</td>
                <td>{{ $c->prodi->nama_prodi }}</td>
                <td>{{ \Illuminate\Support\Str::limit($c->topik ?: '-', 45) }}</td>
                <td>{{ implode(', ', $metode) }}</td>
                <td>{{ $mulai->translatedFormat('d M Y') }}</td>
                <td>{{ $mulai->format('H:i') }}</td>
                <td>@include('consultant.partials.status', ['c' => $c])</td>
                <td>
                  <div class="actions">
                    <a href="{{ route('consultant.consultations.show', $c->id) }}" class="btn btn-ghost btn-sm">Detail</a>
                    <a href="{{ route('consultation.room', $c) }}" class="btn btn-sm"><i class="fa-solid fa-comment-dots"></i> Chat</a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $consultations->links('admin.partials.pagination') }}
    @endif
  </div>
@endsection
