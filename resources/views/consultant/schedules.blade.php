@extends('layouts.consultant')

@section('title', 'Jadwal')
@section('heading', 'Jadwal Layanan')

@section('content')
  <div class="card" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:22px">
    <div style="flex:1;min-width:240px">
      <h2 style="font-size:19px">Jam layanan kamu</h2>
      <p class="muted" style="margin:6px 0 0;font-size:14px;line-height:1.7">
        Jadwal ini berlaku untuk <b>semua layanan</b>: kamu hanya bisa membalas Chat pada jam layanan,
        mahasiswa memilih jam Online Meet dari jadwal ini, dan waktu Offline Meet harus berada di dalamnya.
      </p>
    </div>
    @if ($availableNow)
      <span class="badge ok">Sedang dalam jam layanan</span>
    @else
      <span class="badge warn">Di luar jam layanan{{ $next ? ' &middot; berikutnya: ' . $next['label'] : '' }}</span>
    @endif
  </div>

  <div class="grid-2">
    <div class="card">
      <div class="card-head"><h2>Tambah jam layanan</h2></div>
      <form method="POST" action="{{ route('consultant.schedules.store') }}">
        @csrf
        <div class="form-grid">
          <div class="field full">
            <label for="day_of_week">Hari</label>
            <select id="day_of_week" name="day_of_week" required>
              @foreach ($days as $n => $nama)
                <option value="{{ $n }}" @selected((int) old('day_of_week', 1) === $n)>{{ $nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="start_time">Jam mulai</label>
            <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" required style="width:100%;padding:12px 14px;border:1px solid #e3d9ee;border-radius:12px;font:inherit">
          </div>
          <div class="field">
            <label for="end_time">Jam selesai</label>
            <input type="time" id="end_time" name="end_time" value="{{ old('end_time', '12:00') }}" required style="width:100%;padding:12px 14px;border:1px solid #e3d9ee;border-radius:12px;font:inherit">
          </div>
          <div class="field full">
            <label class="check">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1" checked> Aktif (tersedia untuk layanan)
            </label>
          </div>
        </div>
        <div class="form-actions"><button type="submit" class="btn"><i class="fa-solid fa-plus"></i> Tambah jadwal</button></div>
      </form>
    </div>

    <div class="card">
      <div class="card-head"><h2>Jadwal hari ini</h2></div>
      @forelse ($todayAppointments as $a)
        <div class="slot">
          <b style="min-width:110px">{{ substr($a->schedule->jam_mulai, 0, 5) }} - {{ substr($a->schedule->jam_selesai, 0, 5) }}</b>
          <div style="flex:1;min-width:150px">
            <b>{{ $a->user->name }}</b><br>
            <span class="muted">{{ $a->tipe === 'online_meet' ? 'Online Meet' : 'Offline Meet' }}</span>
          </div>
          @if ($a->status === 'approved')<span class="badge ok">Disetujui</span>@else<span class="badge warn">Menunggu</span>@endif
          <a href="{{ route('consultant.consultations.show', $a->consultation_id) }}" class="btn btn-ghost btn-sm">Detail</a>
        </div>
      @empty
        <div class="empty"><i class="fa-regular fa-calendar"></i>Tidak ada janji meet hari ini.</div>
      @endforelse
    </div>
  </div>

  <div class="card" style="margin-top:22px">
    <div class="card-head"><h2>Jadwal mingguan</h2></div>

    @if ($windows->isEmpty())
      <div class="empty"><i class="fa-regular fa-calendar"></i>Belum ada jam layanan. Tambahkan agar mahasiswa bisa memesan Chat dan Online Meet.</div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr><th>Hari</th><th>Jam</th><th>Status</th><th style="width:330px">Aksi</th></tr></thead>
          <tbody>
            @foreach ($windows as $day => $rows)
              @foreach ($rows as $w)
                <tr>
                  <td><b>{{ $days[(int) $day] ?? $day }}</b></td>
                  <td>{{ substr($w->start_time, 0, 5) }} &ndash; {{ substr($w->end_time, 0, 5) }}</td>
                  <td>@if ($w->is_active)<span class="badge ok">Aktif</span>@else<span class="badge off">Nonaktif</span>@endif</td>
                  <td>
                    <div class="actions">
                      <details style="position:relative">
                        <summary class="btn btn-ghost btn-sm" style="list-style:none;cursor:pointer"><i class="fa-solid fa-pen"></i> Ubah</summary>
                        <form method="POST" action="{{ route('consultant.schedules.update', $w->id) }}" class="inline-form" style="margin-top:8px">
                          @csrf
                          @method('PUT')
                          <select name="day_of_week" style="padding:8px 10px;border:1px solid #e3d9ee;border-radius:10px;font:inherit">
                            @foreach ($days as $n => $nama)<option value="{{ $n }}" @selected((int) $w->day_of_week === $n)>{{ $nama }}</option>@endforeach
                          </select>
                          <input type="time" name="start_time" value="{{ substr($w->start_time, 0, 5) }}" required style="min-width:110px">
                          <input type="time" name="end_time" value="{{ substr($w->end_time, 0, 5) }}" required style="min-width:110px">
                          <input type="hidden" name="is_active" value="{{ $w->is_active ? 1 : 0 }}">
                          <button type="submit" class="btn btn-sm">Simpan</button>
                        </form>
                      </details>
                      <form method="POST" action="{{ route('consultant.schedules.toggle', $w->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-ghost btn-sm">{{ $w->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                      </form>
                      <form method="POST" action="{{ route('consultant.schedules.destroy', $w->id) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <div class="card" style="margin-top:22px">
    <div class="card-head"><h2>Janji meet mendatang</h2></div>
    @forelse ($appointments as $a)
      <div class="slot">
        <div style="flex:1;min-width:190px">
          <b>{{ $a->schedule->tanggal->translatedFormat('D, d M Y') }}</b>, {{ substr($a->schedule->jam_mulai, 0, 5) }}-{{ substr($a->schedule->jam_selesai, 0, 5) }}<br>
          <span class="muted">{{ $a->user->name }} &middot; {{ $a->tipe === 'online_meet' ? 'Online Meet' : 'Offline Meet' }}</span>
        </div>
        @if ($a->payment_status === 'paid')<span class="badge ok">Lunas</span>@else<span class="badge warn">Belum dibayar</span>@endif
        @if ($a->status === 'approved')<span class="badge ok">Disetujui</span>@else<span class="badge warn">Menunggu</span>@endif
        <a href="{{ route('consultant.consultations.show', $a->consultation_id) }}" class="btn btn-ghost btn-sm">Detail</a>
      </div>
    @empty
      <div class="empty"><i class="fa-regular fa-calendar-check"></i>Belum ada janji meet.</div>
    @endforelse
  </div>
@endsection
