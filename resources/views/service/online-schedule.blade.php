@extends('layouts.student')

@section('title', 'Pilih Jadwal Online Meet | Temu Edu')

@push('styles')
<style>
  .page { padding:30px 0 70px; }
  .wrap { max-width:760px; margin:0 auto; }
  .back { display:inline-flex; align-items:center; gap:8px; margin-bottom:16px; color:var(--muted); font-weight:600; font-size:14px; }
  .back:hover { color:var(--purple); }
  .who { display:flex; align-items:center; gap:16px; margin-bottom:22px; }
  .who img { width:60px; height:60px; border-radius:50%; background:var(--purple-soft); }
  .who h1 { font-size:24px; } .who p { margin:3px 0 0; color:var(--muted); font-size:14px; }
  h2.step { margin:26px 0 12px; font-size:18px; }
  .chips { display:flex; flex-wrap:wrap; gap:10px; }
  .chip { padding:10px 14px; border:1px solid #e3d9ee; border-radius:14px; background:#fff; font-weight:700; font-size:14px; text-align:center; }
  .chip small { display:block; margin-top:2px; color:var(--muted); font-weight:500; font-size:12px; }
  .chip:hover { background:#faf6ff; }
  .chip.active { background:var(--purple); border-color:var(--purple); color:#fff; } .chip.active small { color:#e7defa; }
  .times { display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:10px; }
  .time { position:relative; }
  .time input { position:absolute; opacity:0; }
  .time label { display:block; padding:12px; border:1px solid #e3d9ee; border-radius:14px; background:#fff; text-align:center; font-weight:700; cursor:pointer; }
  .time label small { display:block; color:var(--muted); font-weight:500; font-size:12px; }
  .time input:checked + label { background:var(--purple); border-color:var(--purple); color:#fff; } .time input:checked + label small { color:#e7defa; }
  .time input:focus-visible + label { outline:2px solid #b7a6dc; outline-offset:2px; }
  textarea { width:100%; min-height:80px; padding:12px 14px; border:1px solid #e3d9ee; border-radius:14px; font:inherit; resize:vertical; }
  .sum { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-top:22px; padding:16px 20px; border-radius:16px; background:var(--purple-soft); }
  .sum b { font-size:20px; color:var(--purple-dark); }
  .note { margin-top:10px; color:var(--muted); font-size:13px; line-height:1.6; }
  .empty { text-align:center; color:var(--muted); line-height:1.8; }
</style>
@endpush

@section('content')
  <main class="page">
    <div class="container wrap">
      <a href="{{ route('consultation.consultants', $consultant->prodi_id) }}" class="back"><i class="fa-solid fa-arrow-left"></i> Kembali ke daftar konsultan</a>

      <div class="who">
        <img src="{{ $consultant->avatar_url }}" alt="{{ $consultant->user->name }}">
        <div>
          <h1>Online Meet dengan {{ $consultant->user->name }}</h1>
          <p>{{ $minutes }} menit &middot; Rp{{ number_format(config('temuedu.services.online_meet.price'), 0, ',', '.') }}</p>
        </div>
      </div>

      @if (empty($days))
        <div class="card empty">
          <i class="fa-regular fa-calendar" style="font-size:38px;color:#d6c9e8;display:block;margin-bottom:12px"></i>
          Belum ada jam kosong untuk Online Meet dalam {{ config('temuedu.booking_days_ahead') }} hari ke depan.<br>
          @if ($summary['weekly'])
            Jam layanan konsultan:
            @foreach ($summary['weekly'] as $w)<br><b>{{ $w['hari'] }}</b> {{ $w['jam'] }}@endforeach
          @else
            Konsultan belum mengatur jadwal layanan.
          @endif
        </div>
      @else
        <h2 class="step">1. Pilih tanggal</h2>
        <div class="chips">
          @foreach ($days as $d)
            <a href="{{ route('service.online', ['consultantId' => $consultant->id, 'date' => $d['date']->format('Y-m-d')]) }}"
               class="chip {{ $selected && $selected->format('Y-m-d') === $d['date']->format('Y-m-d') ? 'active' : '' }}">
              {{ $d['date']->translatedFormat('D, d M') }}
              <small>{{ $d['count'] }} pilihan jam</small>
            </a>
          @endforeach
        </div>

        <form method="POST" action="{{ route('service.online.checkout', $consultant->id) }}">
          @csrf
          <input type="hidden" name="date" value="{{ $selected->format('Y-m-d') }}">

          <h2 class="step">2. Pilih jam mulai &middot; {{ $selected->translatedFormat('l, d F Y') }}</h2>
          <div class="times">
            @foreach ($options as $i => $t)
              @php $end = \Illuminate\Support\Carbon::createFromFormat('H:i', $t)->addMinutes($minutes)->format('H:i'); @endphp
              <div class="time">
                <input type="radio" name="time" id="t{{ $i }}" value="{{ $t }}" required @checked(old('time') === $t)>
                <label for="t{{ $i }}">{{ $t }}<small>sampai {{ $end }}</small></label>
              </div>
            @endforeach
          </div>

          <h2 class="step">3. Catatan <span style="font-weight:500;color:var(--muted);font-size:14px">(opsional)</span></h2>
          <textarea name="catatan" maxlength="300" placeholder="Contoh: ingin membahas laporan arus kas">{{ old('catatan') }}</textarea>

          <div class="sum">
            <div>Total pembayaran<br><b>Rp{{ number_format(config('temuedu.services.online_meet.price'), 0, ',', '.') }}</b></div>
            <button type="submit" class="btn">Lanjut ke Pembayaran <i class="fa-solid fa-arrow-right"></i></button>
          </div>
          <p class="note">Jadwal ditahan {{ config('temuedu.payment_hold_minutes') }} menit untukmu selama proses pembayaran. Setelah pembayaran berhasil, konsultan akan mengirim link meeting.</p>
        </form>
      @endif
    </div>
  </main>
@endsection
