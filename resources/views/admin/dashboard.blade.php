@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard Administrator')

@section('top_action')
  <a href="{{ route('admin.consultants.create') }}" class="btn"><i class="fa-solid fa-plus"></i> Tambah Konsultan</a>
@endsection

@section('content')
  <div class="stats">
    <div class="stat"><div class="ico"><i class="fa-solid fa-users"></i></div><div><b>{{ $stats['mahasiswa'] }}</b><span>Mahasiswa terdaftar</span></div></div>
    <div class="stat"><div class="ico g"><i class="fa-solid fa-chalkboard-user"></i></div><div><b>{{ $stats['konsultan'] }}</b><span>Konsultan ({{ $stats['konsultan_aktif'] }} aktif)</span></div></div>
    <div class="stat"><div class="ico o"><i class="fa-solid fa-graduation-cap"></i></div><div><b>{{ $stats['prodi'] }}</b><span>Program studi</span></div></div>
    <div class="stat"><div class="ico p"><i class="fa-solid fa-comments"></i></div><div><b>{{ $stats['konsultasi'] }}</b><span>Total konsultasi</span></div></div>
    <div class="stat"><div class="ico o"><i class="fa-solid fa-hourglass-half"></i></div><div><b>{{ $stats['janji_pending'] }}</b><span>Janji menunggu persetujuan</span></div></div>
  </div>

  <div class="grid-2">
    <div class="card">
      <div class="card-head">
        <h2>Konsultan per program studi</h2>
        <a href="{{ route('admin.prodi.index') }}" class="btn btn-ghost btn-sm">Kelola prodi</a>
      </div>

      @forelse ($prodis as $p)
        <div class="bar-row">
          <div class="nm">{{ $p->nama_prodi }}</div>
          <div class="bar"><i style="width: {{ ($p->consultants_count / $maxKonsultan) * 100 }}%"></i></div>
          <div class="n">{{ $p->consultants_count }}</div>
        </div>
      @empty
        <div class="empty"><i class="fa-solid fa-graduation-cap"></i>Belum ada program studi.</div>
      @endforelse
    </div>

    <div class="card">
      <div class="card-head">
        <h2>Konsultan terbaru</h2>
        <a href="{{ route('admin.consultants.index') }}" class="btn btn-ghost btn-sm">Lihat semua</a>
      </div>

      @forelse ($latestConsultants as $c)
        <div class="mini">
          <div class="ava">
            @if ($c->foto_url)<img src="{{ $c->foto_url }}" alt="{{ $c->user->name }}">@else{{ strtoupper(mb_substr($c->user->name, 0, 1)) }}@endif
          </div>
          <div><b>{{ $c->user->name }}</b><small>{{ $c->prodi->nama_prodi }}</small></div>
          <div class="when">{{ $c->created_at->diffForHumans() }}</div>
        </div>
      @empty
        <div class="empty"><i class="fa-solid fa-chalkboard-user"></i>Belum ada konsultan.<br><br>
          <a href="{{ route('admin.consultants.create') }}" class="btn btn-sm">Tambah konsultan pertama</a>
        </div>
      @endforelse
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <h2>Mahasiswa terbaru</h2>
      <a href="{{ route('admin.students.index') }}" class="btn btn-ghost btn-sm">Lihat semua</a>
    </div>

    @forelse ($latestStudents as $s)
      <div class="mini">
        <div class="ava">{{ strtoupper(mb_substr($s->name, 0, 1)) }}</div>
        <div><b>{{ $s->name }}</b><small>{{ $s->email }}</small></div>
        <div class="when">{{ $s->created_at->diffForHumans() }}</div>
      </div>
    @empty
      <div class="empty"><i class="fa-solid fa-users"></i>Belum ada mahasiswa yang mendaftar.</div>
    @endforelse
  </div>
@endsection
