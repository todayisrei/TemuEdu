<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konsultasi dengan {{ $peer['nama'] }} | Temu Edu</title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@500;600;700;800&display=swap"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
    <style>
        :root {
            --purple: #9275c5;
            --purple-dark: #6f579e;
            --purple-soft: #f2eafa;
            --ink: #29263a;
            --muted: #777386;
            --line: #eee8f2;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            height: 100vh;
            background: #f9f7fb;
            color: var(--ink);
            font-family: "DM Sans", sans-serif;
        }

        .navbar {
            height: 70px;
            display: flex;
            align-items: center;
            background: white;
            border-bottom: 1px solid var(--line);
        }

        .nav-inner {
            width: min(900px, calc(100% - 30px));
            margin: auto;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back { color: var(--muted); text-decoration: none; }
        .back:hover { color: var(--purple); }

        .consultant-info {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
        }

        .name { font-weight: 800; }
        .status { color: #42a66f; font-size: 12px; }

        .chat-wrapper {
            width: min(900px, calc(100% - 30px));
            height: calc(100vh - 100px);
            margin: 15px auto;
            display: flex;
            flex-direction: column;
            background: white;
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
        }

        .messages {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 65%;
            padding: 12px 16px;
            border-radius: 17px;
            font-size: 14px;
            line-height: 1.5;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .message.consultant {
            align-self: flex-start;
            background: #f3eff7;
            border-bottom-left-radius: 5px;
        }

        .message.user {
            align-self: flex-end;
            background: var(--purple);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .time {
            display: block;
            margin-top: 5px;
            font-size: 10px;
            opacity: .6;
            text-align: right;
        }

        .input-area {
            display: flex;
            gap: 10px;
            padding: 15px;
            border-top: 1px solid var(--line);
        }

        .input-area input {
            flex: 1;
            height: 46px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 0 15px;
            outline: none;
        }

        .input-area input:focus { border-color: var(--purple); }
        .input-area input:disabled { background: #f6f4f8; cursor: not-allowed; }

        .send {
            width: 46px;
            height: 46px;
            border: 0;
            border-radius: 12px;
            background: var(--purple);
            color: white;
            cursor: pointer;
        }

        .send:hover { background: var(--purple-dark); }
        .send:disabled { opacity: .5; cursor: not-allowed; }

        @media (max-width: 600px) {
            .message { max-width: 85%; }
            .messages { padding: 20px; }
        }

        .head-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .timer {
            padding: 6px 12px;
            border-radius: 20px;
            background: var(--purple-soft);
            color: var(--purple-dark);
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        .timer.warn { background: #fff0e4; color: #c97432; }
        .timer.off { background: #f1f0f4; color: #85829a; }

        .btn-end {
            height: 34px;
            padding: 0 14px;
            border: 1px solid #f3cdd5;
            border-radius: 10px;
            background: #fff;
            color: #d6455d;
            font: 700 13px "DM Sans", sans-serif;
            cursor: pointer;
        }

        .btn-end:hover { background: #fff3f5; }

        .message.system {
            align-self: center;
            max-width: 90%;
            padding: 8px 14px;
            border-radius: 12px;
            background: #fff7e6;
            color: #8a6420;
            font-size: 12.5px;
            text-align: center;
        }

        .notice-bar {
            padding: 11px 18px;
            border-top: 1px solid var(--line);
            background: #f9f7fb;
            color: var(--muted);
            font-size: 13.5px;
            text-align: center;
        }

        .notice-bar.err { background: #fff0f2; color: #b43048; }
        .notice-bar.ok {
            background: #e5f6ee;
            color: #1f7a50;
            border-top: 0;
            border-bottom: 1px solid #cfeadb;
        }

        .notice-bar.err { border-top: 0; border-bottom: 1px solid #f3cdd5; }

        .empty-hint {
            margin: auto;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
            max-width: 320px;
            line-height: 1.7;
        }

        @media (max-width: 600px) {
            .timer { font-size: 11.5px; padding: 5px 9px; }
            .name { font-size: 14px; }
        }

        .btn-meet {
            height: 34px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0 14px;
            border: 1px solid #e3d9ee;
            border-radius: 10px;
            background: #fff;
            color: var(--purple-dark);
            font: 700 13px "DM Sans", sans-serif;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-meet:hover { background: #faf6ff; }

        .modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(41, 38, 58, .45);
        }

        .modal[hidden] { display: none; }

        .modal-box {
            width: min(520px, 100%);
            max-height: 90vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 20px;
            padding: 22px;
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            font-size: 18px;
        }

        .modal-head button {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 50%;
            background: #f3eff7;
            font-size: 18px;
            cursor: pointer;
        }

        .modal h4 { margin: 18px 0 8px; font-size: 14px; color: var(--muted); }

        .appt {
            padding: 12px 14px;
            margin-bottom: 8px;
            border: 1px solid var(--line);
            border-radius: 14px;
            font-size: 13.5px;
            line-height: 1.6;
        }

        .appt .st {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 20px;
            background: var(--purple-soft);
            color: var(--purple-dark);
            font-size: 12px;
            font-weight: 700;
        }

        .appt .st.ok { background: #e5f6ee; color: #1f7a50; }
        .appt .st.bad { background: #f1f0f4; color: #85829a; }
        .appt form { margin-top: 6px; }

        .link-btn {
            border: 0;
            background: none;
            color: #d6455d;
            font: 700 13px "DM Sans", sans-serif;
            cursor: pointer;
            padding: 0;
        }

        .meet-form label {
            display: block;
            margin: 12px 0 6px;
            font-weight: 700;
            font-size: 13.5px;
        }

        .meet-form select, .meet-form textarea {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid var(--line);
            border-radius: 12px;
            font: inherit;
        }

        .meet-form textarea { min-height: 70px; resize: vertical; }
        .radios { display: flex; gap: 10px; }

        .radios label {
            flex: 1;
            margin: 0;
            padding: 11px;
            border: 1px solid var(--line);
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
        }

        .radios input { margin-right: 6px; }

        .btn-submit {
            width: 100%;
            margin-top: 16px;
            height: 44px;
            border: 0;
            border-radius: 12px;
            background: var(--purple);
            color: #fff;
            font: 700 15px "DM Sans", sans-serif;
            cursor: pointer;
        }

        .btn-submit:hover { background: var(--purple-dark); }

        @media (max-width: 600px) {
            .btn-meet span, .btn-meet .lbl { display: none; }
        }
        .notice-bar.warn { background: #fff4e5; color: #9a5b12; border-top: 0; border-bottom: 1px solid #f3dfbd; }
        .appt .st.warn { background: #fff0e4; color: #c97432; }
        .meet-hint { margin: 0 0 10px; color: var(--muted); font-size: 13.5px; line-height: 1.7; }
        .meet-hint a { color: var(--purple); font-weight: 700; }
        .meet-form input[type="date"], .meet-form input[type="time"], .meet-form input[type="text"] { width: 100%; padding: 11px 13px; border: 1px solid var(--line); border-radius: 12px; font: inherit; }
        .btn-pay { margin-top: 8px; padding: 9px 16px; border: 0; border-radius: 10px; background: var(--purple); color: #fff; font: 700 13.5px "DM Sans", sans-serif; cursor: pointer; }
        .btn-pay:hover { background: var(--purple-dark); }
    </style>
</head>

<body>
<nav class="navbar">
    <div class="nav-inner">
        <a href="{{ $back }}" class="back" aria-label="Kembali">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <div class="consultant-info">
            <img src="{{ $peer['foto'] }}" class="avatar" alt="{{ $peer['nama'] }}">
            <div>
                <div class="name">{{ $peer['nama'] }}</div>
                <div class="status">{{ $peer['sub'] }}</div>
            </div>
        </div>

        <div class="head-right">
            @include('partials.notification-bell', ['roomId' => $consultation->id])
            <span class="timer" id="timer"></span>
            @if ($role === 'student')
                <button type="button" class="btn-meet" id="meetBtn">
                    <i class="fa-regular fa-calendar-check"></i> Janji Meet
                </button>
            @else
                <a href="{{ $detailUrl }}" class="btn-meet">
                    <i class="fa-solid fa-circle-info"></i> Detail
                </a>
            @endif
            <button type="button" class="btn-end" id="endBtn">Akhiri</button>
        </div>
    </div>
</nav>

<div class="chat-wrapper">
    @if (session('success'))
        <div class="notice-bar ok">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="notice-bar err">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="notice-bar err">{{ $errors->first() }}</div>
    @endif

    @if (! $availabilityInfo['now'] && $consultation->isChatOpen())
        <div class="notice-bar warn">
            @if ($role === 'student')
                Konsultan sedang di luar jam layanan{{ $availabilityInfo['next'] ? ' (berikutnya: ' . $availabilityInfo['next'] . ')' : '' }}.
                @if (! $state['started'] && $state['timed'])
                    Pesan pertama baru bisa dikirim saat jam layanan.
                @else
                    Balasan akan datang pada jam layanan berikutnya.
                @endif
            @else
                Kamu sedang di luar jam layanan, balasan dinonaktifkan sampai jam layanan berikutnya.
            @endif
        </div>
    @endif

    <div class="messages" id="messages">
        @forelse ($messages as $message)
            <div
                class="message {{ $message['tipe'] === 'system' ? 'system' : ($message['mine'] ? 'user' : 'consultant') }}"
                data-id="{{ $message['id'] }}"
            >{{ $message['text'] }}@if ($message['tipe'] !== 'system')<span class="time">{{ $message['time'] }}</span>@endif</div>
        @empty
            <div class="empty-hint" id="emptyHint">
                @if ($role === 'student')
                    @if ($state['timed'])
                        Tulis pertanyaanmu di bawah. Timer konsultasi {{ $state['minutes'] }} menit mulai berjalan saat pesan pertama dikirim.
                    @else
                        Tulis pesanmu di bawah untuk berkoordinasi dengan konsultan.
                    @endif
                @else
                    Belum ada pesan.
                @endif
            </div>
        @endforelse
    </div>

    <div class="notice-bar err" id="errBar" hidden></div>
    <div class="notice-bar" id="closedBar" hidden></div>

    <form class="input-area" id="chatForm" autocomplete="off">
        <input type="text" id="chatInput" maxlength="2000" placeholder="Tulis pesan...">
        <button type="submit" class="send" id="sendBtn" aria-label="Kirim">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
</div>

@if ($role === 'student')
<div class="modal" id="meetModal" hidden>
    <div class="modal-box">
        <div class="modal-head">
            <b>Janji Meet dengan {{ $peer['nama'] }}</b>
            <button type="button" id="meetClose" aria-label="Tutup">&times;</button>
        </div>

        @if ($appointments->isNotEmpty())
            <h4>Janji pada sesi ini</h4>
            @foreach ($appointments as $a)
                @php
                    $label = [
                        'pending' => 'Menunggu konsultan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ][$a->status] ?? $a->status;
                    $cls = in_array($a->status, ['approved', 'completed'], true)
                        ? 'ok'
                        : (in_array($a->status, ['rejected', 'cancelled'], true) ? 'bad' : '');
                    $canPay = $a->payment_status === 'unpaid' && (
                        ($a->tipe === 'offline_meet' && $a->status === 'approved') ||
                        ($a->tipe === 'online_meet' && $a->status === 'pending'));
                    $canCancel = $a->payment_status === 'unpaid' && in_array($a->status, ['pending', 'approved'], true);
                @endphp
                <div class="appt">
                    <b>{{ $a->tipe === 'online_meet' ? 'Online Meet' : 'Offline Meet' }}</b>
                    &middot; {{ $a->schedule->tanggal->translatedFormat('d M Y') }},
                    {{ substr($a->schedule->jam_mulai, 0, 5) }}-{{ substr($a->schedule->jam_selesai, 0, 5) }}<br>
                    <span class="st {{ $cls }}">{{ $label }}</span>
                    @if ($a->payment_status === 'paid')
                        <span class="st ok">Lunas</span>
                    @elseif (! in_array($a->status, ['rejected', 'cancelled'], true))
                        <span class="st warn">Belum dibayar</span>
                    @endif

                    @if ($a->tipe === 'offline_meet' && $a->lokasi && ! in_array($a->status, ['rejected', 'cancelled'], true))
                        <div>Lokasi: {{ $a->lokasi }}</div>
                    @endif
                    @if ($a->payment_status === 'paid' && $a->status === 'approved' && $a->tipe === 'online_meet' && $a->link_meeting)
                        <div>Link: <a href="{{ $a->link_meeting }}" target="_blank" rel="noopener">{{ $a->link_meeting }}</a></div>
                    @endif

                    @if ($canPay)
                        <form method="POST" action="{{ route('payment.appointment', $a->id) }}">
                            @csrf
                            <button type="submit" class="btn-pay">Bayar Rp{{ number_format($a->harga ?? config('temuedu.services.' . $a->tipe . '.price'), 0, ',', '.') }}</button>
                        </form>
                    @endif
                    @if ($canCancel)
                        <form method="POST"
                              action="{{ route('consultation.appointments.cancel', [$consultation, $a->id]) }}"
                              onsubmit="return confirm('Batalkan janji meet ini?')">
                            @csrf
                            <button type="submit" class="link-btn">Batalkan janji</button>
                        </form>
                    @endif
                </div>
            @endforeach
        @endif

        @if ($canPropose)
            <h4>Ajukan Offline Meet</h4>
            <p class="meet-hint">
                {{ config('temuedu.services.offline_meet.minutes') }} menit &middot;
                Rp{{ number_format(config('temuedu.services.offline_meet.price'), 0, ',', '.') }}.
                Setelah kamu dan konsultan sepakat soal waktu &amp; lokasi di chat, ajukan di sini.
                Pembayaran dilakukan setelah konsultan menyetujui.
            </p>

            @if (! empty($availabilityInfo['weekly']))
                <p class="meet-hint"><b>Jam layanan konsultan:</b>
                    @foreach ($availabilityInfo['weekly'] as $w){{ $w['hari'] }} {{ $w['jam'] }}@if (! $loop->last); @endif @endforeach
                </p>
            @endif

            <form method="POST" action="{{ route('consultation.appointments.store', $consultation) }}" class="meet-form">
                @csrf
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" min="{{ today()->format('Y-m-d') }}" value="{{ old('tanggal') }}" required>

                <label for="jam_mulai">Jam mulai</label>
                <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai') }}" required>

                <label for="lokasi">Lokasi pertemuan</label>
                <input type="text" name="lokasi" id="lokasi" maxlength="255" value="{{ old('lokasi') }}" placeholder="Contoh: Perpustakaan lantai 2" required>

                <label for="catatan">Catatan <span style="font-weight:500;color:var(--muted)">(opsional)</span></label>
                <textarea name="catatan" id="catatan" maxlength="500" placeholder="Contoh: ingin membahas bab 3 skripsi">{{ old('catatan') }}</textarea>

                <button type="submit" class="btn-submit">Ajukan Pertemuan</button>
            </form>
        @elseif ($consultation->service === 'offline_meet')
            <p class="meet-hint" style="margin-top:14px">
                @if ($appointments->isNotEmpty())
                    Sudah ada pengajuan pertemuan pada sesi ini. Pantau statusnya di atas.
                @else
                    Sesi koordinasi sudah berakhir. Mulai Offline Meet baru dari halaman konsultan.
                @endif
            </p>
        @else
            <h4>Online / Offline Meet</h4>
            <p class="meet-hint">
                Online Meet dan Offline Meet dipesan dari halaman konsultan (pilih layanan, lalu bayar).
                <a href="{{ route('consultation.consultants', $consultation->prodi_id) }}">Pilih layanan</a>
            </p>
        @endif
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('meetModal');
    var meetBtn = document.getElementById('meetBtn');
    var meetClose = document.getElementById('meetClose');

    if (!modal || !meetBtn || !meetClose) return;

    meetBtn.addEventListener('click', function () {
        modal.hidden = false;
    });

    meetClose.addEventListener('click', function () {
        modal.hidden = true;
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.hidden = true;
        }
    });

@if ($autoOpenMeet)
    // Tampilkan modal otomatis saat mahasiswa masuk ke ruang koordinasi Offline Meet.
    modal.hidden = false;
@endif
})();
</script>
@endif

<script>
(function () {
    var URLS = {{ Illuminate\Support\Js::from([
        'poll' => route('consultation.messages', $consultation),
        'send' => route('consultation.messages.store', $consultation),
        'close' => route('consultation.close', $consultation),
    ]) }};

    var ROLE = {{ Illuminate\Support\Js::from($role) }};
    var state = {{ Illuminate\Support\Js::from($state) }};
    var lastId = {{ Illuminate\Support\Js::from(collect($messages)->max('id') ?? 0) }};
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var box = document.getElementById('messages');
    var form = document.getElementById('chatForm');
    var input = document.getElementById('chatInput');
    var sendBtn = document.getElementById('sendBtn');
    var timerEl = document.getElementById('timer');
    var endBtn = document.getElementById('endBtn');
    var errBar = document.getElementById('errBar');
    var closedBar = document.getElementById('closedBar');

    var seen = {};
    box.querySelectorAll('[data-id]').forEach(function (el) {
        seen[el.getAttribute('data-id')] = true;
    });

    var endAt = null;
    var polling = false;

    function scrollDown() {
        box.scrollTop = box.scrollHeight;
    }

    function addMessage(m) {
        if (seen[m.id]) return;
        seen[m.id] = true;
        if (m.id > lastId) lastId = m.id;

        var hint = document.getElementById('emptyHint');
        if (hint) hint.remove();

        var d = document.createElement('div');
        d.setAttribute('data-id', m.id);
        d.className = 'message ' + (m.tipe === 'system' ? 'system' : (m.mine ? 'user' : 'consultant'));
        d.appendChild(document.createTextNode(m.text));

        if (m.tipe !== 'system') {
            var t = document.createElement('span');
            t.className = 'time';
            t.textContent = m.time;
            d.appendChild(t);
        }

        box.appendChild(d);
    }

    function addMany(list) {
        var nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 90;
        var mine = false;
        list.forEach(function (m) {
            if (m.mine) mine = true;
            addMessage(m);
        });
        if (list.length && (nearBottom || mine)) scrollDown();
    }

    function pad(n) {
        return (n < 10 ? '0' : '') + n;
    }

    function renderTimer() {
        if (!state.open) {
            timerEl.textContent = state.status === 'closed' ? 'Sesi diakhiri' : 'Sesi berakhir';
            timerEl.className = 'timer off';
            return;
        }

        if (endAt === null) {
            timerEl.textContent = state.timed ? 'Timer ' + state.minutes + ' menit belum dimulai' : 'Chat koordinasi';
            timerEl.className = 'timer';
            return;
        }

        var left = Math.max(0, Math.round((endAt - Date.now()) / 1000));
        timerEl.textContent = 'Sisa ' + pad(Math.floor(left / 60)) + ':' + pad(left % 60);
        timerEl.className = 'timer' + (left <= 300 ? ' warn' : '');

        if (left === 0) poll();
    }

    function applyState(s) {
        state = s;
        endAt = (s.open && s.remaining !== null) ? Date.now() + s.remaining * 1000 : null;

        input.disabled = !s.open;
        sendBtn.disabled = !s.open;
        endBtn.hidden = !s.open;
        closedBar.hidden = s.open;

        if (!s.open) {
            closedBar.textContent = s.status === 'closed'
                ? 'Sesi konsultasi telah diakhiri.'
                : 'Waktu konsultasi telah habis. Mahasiswa dapat memulai sesi baru dari daftar konsultan.';
            input.placeholder = 'Sesi telah berakhir';
        }

        renderTimer();
    }

    function showError(text) {
        errBar.textContent = text;
        errBar.hidden = !text;
    }

    function request(url, opts) {
        opts = opts || {};
        opts.credentials = 'same-origin';
        opts.headers = Object.assign({
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
        }, opts.headers || {});
        return fetch(url, opts);
    }

    function poll() {
        if (polling) return;
        polling = true;

        request(URLS.poll + '?after=' + lastId)
            .then(function (r) {
                if (r.status === 401 || r.status === 419) {
                    location.reload();
                    return null;
                }
                if (!r.ok) return null;
                return r.json();
            })
            .then(function (data) {
                if (!data) return;
                addMany(data.messages);
                applyState(data.state);
            })
            .catch(function () {
                // Jaringan putus: coba lagi di putaran berikutnya.
            })
            .then(function () {
                polling = false;
            });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var text = input.value.trim();
        if (!text || !state.open) return;

        sendBtn.disabled = true;
        showError('');

        request(URLS.send, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ isi: text })
        })
        .then(function (r) {
            if (r.status === 419) {
                showError('Sesi halaman habis. Muat ulang halaman ini.');
                return null;
            }
            return r.json().then(function (data) {
                return { ok: r.ok, data: data };
            });
        })
        .then(function (res) {
            if (!res) return;

            if (res.ok) {
                input.value = '';
                addMany(res.data.messages);
                applyState(res.data.state);
            } else {
                var msg = res.data.error || res.data.message || 'Pesan gagal dikirim.';
                showError(msg);
                if (res.data.state) applyState(res.data.state);
            }
        })
        .catch(function () {
            showError('Koneksi bermasalah. Pesan belum terkirim.');
        })
        .then(function () {
            sendBtn.disabled = !state.open;
            input.focus();
        });
    });

    endBtn.addEventListener('click', function () {
        if (!confirm('Akhiri sesi konsultasi ini? Setelah diakhiri, chat tidak dapat dilanjutkan.')) return;

        request(URLS.close, { method: 'POST' })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                applyState(data.state);
                poll();
            })
            .catch(function () {
                showError('Gagal mengakhiri sesi.');
            });
    });

    applyState(state);
    scrollDown();
    setInterval(renderTimer, 1000);
    setInterval(function () {
        if (!document.hidden) poll();
    }, 3000);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) poll();
    });
})();
</script>

</body>
</html>
