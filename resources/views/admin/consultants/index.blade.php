@extends('layouts.admin')

@section('title', 'Konsultan')
@section('heading', 'Manajemen Konsultan')

@section('top_action')
  <a href="{{ route('admin.consultants.create') }}" class="btn"><i class="fa-solid fa-plus"></i> Tambah Konsultan</a>
@endsection

@section('content')
  <div class="card">
    <form method="GET" class="toolbar">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email...">
      <select name="prodi">
        <option value="">Semua prodi</option>
        @foreach ($prodis as $p)
          <option value="{{ $p->id }}" @selected(request('prodi') == $p->id)>{{ $p->nama_prodi }}</option>
        @endforeach
      </select>
      <select name="status">
        <option value="">Semua status</option>
        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
        <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
      </select>
      <button type="submit" class="btn btn-ghost"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
      @if (request()->hasAny(['q', 'prodi', 'status']))
        <a href="{{ route('admin.consultants.index') }}" class="btn btn-ghost">Reset</a>
      @endif
    </form>

    @if ($consultants->isEmpty())
      <div class="empty"><i class="fa-solid fa-chalkboard-user"></i>Belum ada konsultan yang cocok.</div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Konsultan</th><th>Prodi</th><th>Tarif</th><th>Rating</th><th>Status</th><th style="width:250px">Aksi</th></tr>
          </thead>
          <tbody>
            @foreach ($consultants as $c)
              <tr>
                <td>
                  <div class="person">
                    <div class="ava">
                      @if ($c->foto_url)<img src="{{ $c->foto_url }}" alt="{{ $c->user->name }}">@else{{ strtoupper(mb_substr($c->user->name, 0, 1)) }}@endif
                    </div>
                    <div><a href="{{ route('admin.consultants.show', $c) }}"><b>{{ $c->user->name }}</b></a><small>{{ $c->user->email }}</small></div>
                  </div>
                </td>
                <td>{{ $c->prodi->nama_prodi }}</td>
                <td>Rp {{ number_format($c->harga, 0, ',', '.') }}</td>
                <td>@if ($c->rating_count > 0)<i class="fa-solid fa-star" style="color:#f2b84a"></i> {{ $c->rating_avg }} <small class="muted">({{ $c->rating_count }})</small>@else<span class="muted">-</span>@endif</td>
                <td>@if ($c->is_active)<span class="badge ok">Aktif</span>@else<span class="badge off">Nonaktif</span>@endif</td>
                <td>
                  <div class="actions">
                    <a href="{{ route('admin.consultants.show', $c) }}" class="btn btn-ghost btn-sm">Detail</a>
                    <a href="{{ route('admin.consultants.edit', $c) }}" class="btn btn-ghost btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                    <form method="POST" action="{{ route('admin.consultants.toggle', $c) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-ghost btn-sm">{{ $c->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.consultants.destroy', $c) }}" onsubmit="return confirm('Hapus konsultan {{ $c->user->name }} beserta akunnya?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $consultants->links('admin.partials.pagination') }}
    @endif
  </div>
@endsection
