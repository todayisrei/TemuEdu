@extends('layouts.student')

@section('title', 'Pembayaran | Temu Edu')

@push('styles')
<style>
  .page { padding:34px 0 70px; }
  .wrap { max-width:560px; margin:0 auto; }
  .head { display:flex; align-items:center; gap:14px; margin-bottom:18px; }
  .ico { width:54px; height:54px; display:grid; place-items:center; border-radius:18px; background:var(--purple-soft); color:var(--purple); font-size:22px; }
  .head h1 { font-size:24px; } .head p { margin:2px 0 0; color:var(--muted); font-size:14px; }
  dl.rows { margin:0; } dl.rows div { display:flex; justify-content:space-between; gap:14px; padding:11px 0; border-bottom:1px dashed #eee8f2; }
  dl.rows dt { color:var(--muted); } dl.rows dd { margin:0; font-weight:700; text-align:right; }
  .total { display:flex; align-items:center; justify-content:space-between; margin:18px 0; padding:16px 18px; border-radius:16px; background:var(--purple-soft); }
  .total b { font-size:24px; color:var(--purple-dark); }
  .btn.block { width:100%; padding:15px; font-size:16px; }
  .links { margin-top:14px; text-align:center; font-size:14px; } .links a { color:var(--purple); font-weight:700; }
  .warn { padding:13px 16px; border-radius:14px; background:#fff4e5; color:#9a5b12; font-size:14px; line-height:1.6; margin-bottom:16px; }
  .sim { padding:11px 14px; border-radius:12px; background:#eef6ff; color:#2a5a8a; font-size:13px; line-height:1.6; margin-bottom:16px; }
  .note { margin-top:16px; color:var(--muted); font-size:13px; line-height:1.7; text-align:center; }
  .methods { display:grid; gap:10px; margin:6px 0 4px; }
  .method { position:relative; }
  .method input { position:absolute; opacity:0; }
  .method label { display:flex; align-items:center; gap:12px; padding:14px 16px; border:1px solid #e3d9ee; border-radius:14px; background:#fff; font-weight:600; cursor:pointer; transition:.15s; }
  .method label i { width:22px; text-align:center; color:var(--purple); }
  .method input:checked + label { border-color:var(--purple); background:#faf6ff; box-shadow:0 0 0 2px #7661a826; }
  .method input:focus-visible + label { outline:2px solid #b7a6dc; outline-offset:2px; }
  h2.m { margin:20px 0 8px; font-size:16px; }
  .tabs { display:flex; flex-wrap:wrap; gap:8px; margin:6px 0 14px; }
  .tab { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid #e3d9ee; border-radius:12px; background:#fff; font:600 14px "DM Sans",sans-serif; color:var(--ink); cursor:pointer; }
  .tab i { color:var(--purple); }
  .tab.active { border-color:var(--purple); background:#faf6ff; box-shadow:0 0 0 2px #7661a826; }
  .panel[hidden] { display:none; }
  .qrbox { width:min(260px,100%); margin:6px auto 4px; padding:16px; border:1px solid #e3d9ee; border-radius:18px; background:#fff; text-align:center; }
  .qrtitle { font:800 17px "Jost",sans-serif; letter-spacing:.06em; color:var(--purple-dark); margin-bottom:10px; }
  .qrimg { display:grid; place-items:center; min-height:190px; }
  .qrimg img, .qrimg canvas { max-width:100%; height:auto; }
  .qrmerchant { margin-top:10px; font-weight:700; } .qramount { color:var(--purple-dark); font-weight:800; font-size:18px; }
  .hint { margin:10px 0 0; text-align:center; color:var(--muted); font-size:13.5px; line-height:1.6; }
  .chips { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
  .chip { padding:8px 16px; border:1px solid #e3d9ee; border-radius:20px; background:#fff; font:700 13.5px "DM Sans",sans-serif; cursor:pointer; }
  .chip.active { background:var(--purple); border-color:var(--purple); color:#fff; }
  .vabox { padding:16px 18px; border:1px solid #e3d9ee; border-radius:16px; background:#faf8fd; }
  .vabox small { color:var(--muted); }
  .vanum { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin:6px 0; font:800 24px/1.2 "Jost",sans-serif; letter-spacing:.06em; color:var(--purple-dark); word-break:break-all; }
  .copy { padding:7px 12px; border:1px solid #e3d9ee; border-radius:10px; background:#fff; font:700 13px "DM Sans",sans-serif; color:var(--purple-dark); cursor:pointer; letter-spacing:0; }
  .vameta { color:var(--muted); font-size:13.5px; }
  .steps { margin:14px 0 0; padding-left:20px; color:#4a475c; font-size:14px; line-height:1.8; }
  .cardbox { padding:18px; border:1px dashed #d9c9ee; border-radius:14px; background:#faf8fd; color:var(--muted); text-align:center; line-height:1.7; }
  .sim-note { margin-top:16px; padding:11px 14px; border-radius:12px; background:#eef6ff; color:#2a5a8a; font-size:13px; line-height:1.6; }
</style>
@endpush

@section('content')
  @php
    $payable = $payment->isPayable();
    $hasSchedule = $payment->appointment && $payment->appointment->schedule;
    $icons = ['qris' => 'fa-qrcode', 'bank_transfer' => 'fa-building-columns', 'ewallet' => 'fa-wallet', 'credit_card' => 'fa-credit-card'];
  @endphp
  <main class="page">
    <div class="container wrap">
      <div class="card">
        <div class="head">
          <div class="ico"><i class="fa-solid {{ $service['icon'] }}"></i></div>
          <div>
            <h1>Pembayaran {{ $service['label'] }}</h1>
            <p>Order {{ $payment->order_id }}</p>
          </div>
        </div>

        @if ($simulation)
          <div class="sim"><b>Mode simulasi.</b> Tidak ada uang sungguhan yang dipakai. Klik Bayar dan pembayaran langsung dianggap berhasil.</div>
        @elseif (! $configured)
          <div class="warn"><b>Pembayaran belum dikonfigurasi.</b> Admin perlu mengisi MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di file .env.</div>
        @endif

        @if (! $payable)
          <div class="warn">Pembayaran ini {{ strtolower($payment->label()) }}. Silakan ulangi dari halaman Konsultasi Saya.</div>
        @endif

        <dl class="rows">
          <div><dt>Layanan</dt><dd>{{ $service['label'] }}</dd></div>
          <div><dt>Konsultan</dt><dd>{{ $payment->consultant->user->name }}</dd></div>
          <div><dt>Durasi</dt><dd>{{ $service['minutes'] }} menit</dd></div>
          @if ($hasSchedule)
            <div><dt>Jadwal</dt><dd>{{ $payment->appointment->schedule->tanggal->translatedFormat('D, d M Y') }}, {{ substr($payment->appointment->schedule->jam_mulai, 0, 5) }}-{{ substr($payment->appointment->schedule->jam_selesai, 0, 5) }}</dd></div>
          @endif
          @if ($payment->appointment && $payment->appointment->tipe === 'offline_meet' && $payment->appointment->lokasi)
            <div><dt>Lokasi</dt><dd>{{ $payment->appointment->lokasi }}</dd></div>
          @endif
          <div><dt>Status</dt><dd>{{ $payment->label() }}</dd></div>
        </dl>

        <div class="total"><span>Total pembayaran</span><b>Rp{{ number_format($payment->amount, 0, ',', '.') }}</b></div>

        @if ($payable && $simulation)
          <form method="POST" action="{{ route('payment.simulate', $payment->order_id) }}" id="simForm">
            @csrf
            <input type="hidden" name="method" id="simMethod" value="qris">
            <input type="hidden" name="channel" id="simChannel" value="">

            <h2 class="m">Pilih metode pembayaran</h2>
            <div class="tabs" role="tablist">
              @foreach ($methods as $key => $label)
                <button type="button" class="tab {{ $loop->first ? 'active' : '' }}" data-method="{{ $key }}">
                  <i class="fa-solid {{ $icons[$key] ?? 'fa-money-bill' }}"></i> {{ $label }}
                </button>
              @endforeach
            </div>

            {{-- QRIS --}}
            <div class="panel" data-panel="qris">
              <div class="qrbox">
                <div class="qrtitle">QRIS</div>
                <div id="qrQris" class="qrimg"></div>
                <div class="qrmerchant">TemuEdu</div>
                <div class="qramount">Rp{{ number_format($payment->amount, 0, ',', '.') }}</div>
              </div>
              <p class="hint">Scan QR di atas dengan aplikasi e-wallet atau m-banking, lalu selesaikan pembayaran.</p>
            </div>

            {{-- Transfer bank / Virtual Account --}}
            <div class="panel" data-panel="bank_transfer" hidden>
              <div class="chips">
                @foreach ($banks as $key => $bank)
                  <button type="button" class="chip {{ $loop->first ? 'active' : '' }}" data-bank="{{ $key }}">{{ $bank['label'] }}</button>
                @endforeach
              </div>
              <div class="vabox">
                <small>Nomor Virtual Account <span id="vaBank">{{ collect($banks)->first()['label'] }}</span></small>
                <div class="vanum"><span id="vaNumber">{{ collect($vaNumbers)->first() }}</span>
                  <button type="button" class="copy" id="vaCopy"><i class="fa-regular fa-copy"></i> Salin</button>
                </div>
                <div class="vameta">a.n. TemuEdu &middot; Nominal <b>Rp{{ number_format($payment->amount, 0, ',', '.') }}</b></div>
              </div>
              <ol class="steps">
                <li>Buka aplikasi m-banking atau ATM bank pilihanmu.</li>
                <li>Pilih <b>Transfer &rarr; Virtual Account</b>.</li>
                <li>Masukkan nomor VA di atas dan pastikan nominalnya sesuai.</li>
                <li>Selesaikan pembayaran, lalu kembali ke sini.</li>
              </ol>
            </div>

            {{-- E-wallet --}}
            <div class="panel" data-panel="ewallet" hidden>
              <div class="chips">
                @foreach ($wallets as $key => $label)
                  <button type="button" class="chip {{ $loop->first ? 'active' : '' }}" data-wallet="{{ $key }}">{{ $label }}</button>
                @endforeach
              </div>
              <div class="qrbox">
                <div class="qrtitle" id="walletTitle">{{ collect($wallets)->first() }}</div>
                <div id="qrWallet" class="qrimg"></div>
                <div class="qrmerchant">TemuEdu</div>
                <div class="qramount">Rp{{ number_format($payment->amount, 0, ',', '.') }}</div>
              </div>
              <p class="hint">Buka aplikasi e-wallet, pilih <b>Scan</b>, lalu arahkan ke QR di atas.</p>
            </div>

            {{-- Kartu --}}
            <div class="panel" data-panel="credit_card" hidden>
              <div class="cardbox"><i class="fa-solid fa-credit-card"></i> Pembayaran kartu kredit / debit (simulasi). Tidak perlu mengisi data kartu.</div>
            </div>

            <div class="sim-note"><i class="fa-solid fa-circle-info"></i> Ini <b>simulasi</b>: QR dan nomor VA hanya contoh, tidak bisa dipakai membayar sungguhan.
              Setelah "membayar", klik tombol di bawah untuk menandai pembayaran selesai.</div>

            @error('method')<div class="warn" style="margin-top:10px">{{ $message }}</div>@enderror
            @error('channel')<div class="warn" style="margin-top:10px">{{ $message }}</div>@enderror

            <button type="submit" class="btn block" id="simSubmit" style="margin-top:16px"><i class="fa-solid fa-circle-check"></i> Saya sudah bayar (simulasi)</button>
          </form>
          <div class="links"><a href="{{ route('consultation.history') }}">Batal, kembali</a></div>
        @elseif ($payable && $configured)
          <button type="button" class="btn block" id="payBtn"><i class="fa-solid fa-lock"></i> Bayar Sekarang</button>
          <div class="links"><a href="{{ route('payment.finish', $payment->order_id) }}">Cek status pembayaran</a> &middot; <a href="{{ route('consultation.history') }}">Kembali</a></div>
          @if ($payment->expires_at)
            <p class="note">Selesaikan pembayaran sebelum {{ $payment->expires_at->format('H:i') }} WIB. Layanan aktif otomatis setelah pembayaran berhasil.</p>
          @endif
        @else
          <a href="{{ route('consultation.history') }}" class="btn block">Ke Konsultasi Saya</a>
        @endif
      </div>
    </div>
  </main>
@endsection

@if ($payable && $simulation)
  @push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
  (function () {
      var ORDER = @json($payment->order_id);
      var AMOUNT = @json((int) $payment->amount);
      var VA = @json($vaNumbers);
      var BANKS = @json(collect($banks)->map(fn ($b) => $b['label']));
      var WALLETS = @json($wallets);
      var LABEL = {
          qris: 'Saya sudah scan & bayar (simulasi)',
          bank_transfer: 'Saya sudah transfer (simulasi)',
          ewallet: 'Saya sudah bayar (simulasi)',
          credit_card: 'Bayar dengan kartu (simulasi)'
      };

      var methodInput = document.getElementById('simMethod');
      var channelInput = document.getElementById('simChannel');
      var submitBtn = document.getElementById('simSubmit');
      var tabs = document.querySelectorAll('.tab');
      var panels = document.querySelectorAll('.panel');
      var bank = Object.keys(VA)[0];
      var wallet = Object.keys(WALLETS)[0];
      var made = {};

      function qr(id) {
          var el = document.getElementById(id);
          if (!el || made[id]) return;
          if (window.QRCode) {
              new QRCode(el, { text: 'TEMUEDU-SIM|' + ORDER + '|' + AMOUNT, width: 190, height: 190 });
              made[id] = true;
          } else {
              el.textContent = '[QR contoh] ' + ORDER;   // jika pustaka QR gagal dimuat (offline)
          }
      }

      function show(method) {
          tabs.forEach(function (t) { t.classList.toggle('active', t.getAttribute('data-method') === method); });
          panels.forEach(function (p) { p.hidden = p.getAttribute('data-panel') !== method; });
          methodInput.value = method;
          channelInput.value = method === 'bank_transfer' ? bank : (method === 'ewallet' ? wallet : '');
          submitBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + (LABEL[method] || 'Saya sudah bayar (simulasi)');
          if (method === 'qris') qr('qrQris');
          if (method === 'ewallet') qr('qrWallet');
      }

      tabs.forEach(function (t) { t.addEventListener('click', function () { show(t.getAttribute('data-method')); }); });

      document.querySelectorAll('[data-bank]').forEach(function (c) {
          c.addEventListener('click', function () {
              bank = c.getAttribute('data-bank');
              document.querySelectorAll('[data-bank]').forEach(function (x) { x.classList.toggle('active', x === c); });
              document.getElementById('vaNumber').textContent = VA[bank];
              document.getElementById('vaBank').textContent = BANKS[bank];
              channelInput.value = bank;
          });
      });

      document.querySelectorAll('[data-wallet]').forEach(function (c) {
          c.addEventListener('click', function () {
              wallet = c.getAttribute('data-wallet');
              document.querySelectorAll('[data-wallet]').forEach(function (x) { x.classList.toggle('active', x === c); });
              document.getElementById('walletTitle').textContent = WALLETS[wallet];
              channelInput.value = wallet;
          });
      });

      document.getElementById('vaCopy').addEventListener('click', function () {
          var text = document.getElementById('vaNumber').textContent.trim();
          var btn = this;
          function done() { btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin'; setTimeout(function () { btn.innerHTML = '<i class="fa-regular fa-copy"></i> Salin'; }, 1500); }
          if (navigator.clipboard && navigator.clipboard.writeText) {
              navigator.clipboard.writeText(text).then(done, done);
          } else {
              var ta = document.createElement('textarea'); ta.value = text; document.body.appendChild(ta); ta.select();
              try { document.execCommand('copy'); } catch (e) {}
              document.body.removeChild(ta); done();
          }
      });

      window.addEventListener('load', function () { show('qris'); });
  })();
  </script>
  @endpush
@endif

@if ($payable && $configured && ! $simulation)
  @push('scripts')
  <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
  <script>
  (function () {
      var TOKEN = @json($payment->snap_token);
      var FINISH = @json(route('payment.finish', $payment->order_id));

      function go() { window.location.href = FINISH; }

      function pay() {
          if (!window.snap) { alert('Gagal memuat Midtrans. Periksa koneksi internet lalu muat ulang halaman.'); return; }
          window.snap.pay(TOKEN, {
              onSuccess: go,      // status tetap diverifikasi ulang di server (payment.finish)
              onPending: go,
              onError: go,
              onClose: function () { /* tetap di halaman; bisa klik "Bayar Sekarang" lagi */ }
          });
      }

      document.getElementById('payBtn').addEventListener('click', pay);
      window.addEventListener('load', function () { setTimeout(pay, 400); });
  })();
  </script>
  @endpush
@endif
