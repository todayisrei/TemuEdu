@extends('layouts.admin')

@section('title', 'Konsultasi')
@section('heading', 'Pemantauan Konsultasi')

@section('content')
  <div class="card">
    <form method="GET" class="toolbar">
      <select name="status">
        <option value="">Semua status</option>
        <option value="active" @selected(request('status') === 'active')>Aktif</option>
        <option value="closed" @selected(request('status') === 'closed')>Selesai</option>
        <option value="expired" @selected(request('status') === 'expired')>Kedaluwarsa</option>
      </select>
      <button type="submit" class="btn btn-ghost"><i class="fa-solid fa-filter"></i> Terapkan</button>
      @if (request()->filled('status'))
        <a href="{{ route('admin.consultations.index') }}" class="btn btn-ghost">Reset</a>
      @endif
    </form>

    @if ($consultations->isEmpty())
      <div class="empty"><i class="fa-solid fa-comments"></i>Belum ada data konsultasi.</div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr><th>Mahasiswa</th><th>Konsultan</th><th>Prodi</th><th>Topik</th><th>Status</th><th>Mulai</th></tr></thead>
          <tbody>
            @foreach ($consultations as $c)
              <tr>
                <td><b>{{ $c->user->name }}</b></td>
                <td>{{ $c->consultant->user->name }}</td>
                <td>{{ $c->prodi->nama_prodi }}</td>
                <td>{{ $c->topik ?: '-' }}</td>
                <td>
                  @if ($c->status === 'active')<span class="badge ok">Aktif</span>
                  @elseif ($c->status === 'closed')<span class="badge">Selesai</span>
                  @else<span class="badge off">Kedaluwarsa</span>@endif
                </td>
                <td>{{ $c->started_at ? $c->started_at->format('d M Y H:i') : '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $consultations->links('admin.partials.pagination') }}
    @endif
  </div>
@endsection
