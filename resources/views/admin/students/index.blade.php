@extends('layouts.admin')

@section('title', 'Mahasiswa')
@section('heading', 'Data Mahasiswa')

@section('content')
  <div class="card">
    <form method="GET" class="toolbar">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email...">
      <button type="submit" class="btn btn-ghost"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
      @if (request()->filled('q'))
        <a href="{{ route('admin.students.index') }}" class="btn btn-ghost">Reset</a>
      @endif
    </form>

    @if ($students->isEmpty())
      <div class="empty"><i class="fa-solid fa-users"></i>Belum ada mahasiswa yang cocok.</div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr><th>Mahasiswa</th><th>Konsultasi</th><th>Terdaftar</th></tr></thead>
          <tbody>
            @foreach ($students as $s)
              <tr>
                <td>
                  <div class="person">
                    <div class="ava">{{ strtoupper(mb_substr($s->name, 0, 1)) }}</div>
                    <div><b>{{ $s->name }}</b><small>{{ $s->email }}</small></div>
                  </div>
                </td>
                <td><span class="badge">{{ $s->consultations_count }}x</span></td>
                <td>{{ $s->created_at->translatedFormat('d M Y') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $students->links('admin.partials.pagination') }}
    @endif
  </div>
@endsection
