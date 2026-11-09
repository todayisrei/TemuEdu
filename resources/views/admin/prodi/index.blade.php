@extends('layouts.admin')

@section('title', 'Program Studi')
@section('heading', 'Program Studi')

@section('top_action')
  <a href="{{ route('admin.prodi.create') }}" class="btn"><i class="fa-solid fa-plus"></i> Tambah Prodi</a>
@endsection

@section('content')
  <div class="card">
    @if ($prodis->isEmpty())
      <div class="empty"><i class="fa-solid fa-graduation-cap"></i>Belum ada program studi.</div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th style="width:60px">#</th><th>Nama program studi</th><th>Jumlah konsultan</th><th style="width:180px">Aksi</th></tr>
          </thead>
          <tbody>
            @foreach ($prodis as $i => $p)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td><b>{{ $p->nama_prodi }}</b></td>
                <td><span class="badge">{{ $p->consultants_count }} konsultan</span></td>
                <td>
                  <div class="actions">
                    <a href="{{ route('admin.prodi.edit', $p) }}" class="btn btn-ghost btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                    <form method="POST" action="{{ route('admin.prodi.destroy', $p) }}" onsubmit="return confirm('Hapus program studi {{ $p->nama_prodi }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endsection
