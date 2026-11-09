
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Konsultan {{ $namaProdi }} | Temu Edu</title>

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
            --purple-soft: #f5effb;
            --ink: #29263a;
            --muted: #777386;
            --line: #eee8f2;
            --green: #3aa76d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #fffafc;
            color: var(--ink);
            font-family: "DM Sans", sans-serif;
        }

        .container {
            width: min(1050px, calc(100% - 40px));
            margin: auto;
        }

        /* NAVBAR */
        .navbar {
            background: white;
            border-bottom: 1px solid var(--line);
            padding: 12px 0;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 45px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
            text-decoration: none;
            line-height: 1;
        }

        .brand-logo {
            display: block;
            width: 135px;
            height: 45px;
            object-fit: contain;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            line-height: 1;
            white-space: nowrap;
        }

        .back:hover {
            color: var(--purple);
        }

        /* CONTENT */
        .content {
            padding: 55px 0;
        }

        .heading {
            margin-bottom: 30px;
        }

        .heading h1 {
            margin: 0 0 7px;
            font-family: "Jost", sans-serif;
            font-size: 34px;
        }

        .heading p {
            margin: 0;
            color: var(--muted);
        }

        /* CONSULTANT LIST */
        .consultant-list {
            display: grid;
            gap: 18px;
        }

        .consultant-card {
            display: flex;
            align-items: center;
            gap: 22px;
            padding: 24px;
            background: white;
            border: 1px solid var(--line);
            border-radius: 20px;
            transition: .2s;
        }

        .consultant-card:hover {
            border-color: #dac9e8;
            box-shadow: 0 15px 35px rgba(100, 70, 130, .08);
        }

        .avatar {
            width: 90px;
            height: 90px;
            flex: 0 0 90px;
            border-radius: 50%;
            object-fit: cover;
        }

        .info {
            flex: 1;
        }

        .info h3 {
            margin: 0 0 5px;
            font-family: "Jost", sans-serif;
            font-size: 21px;
        }

        .prodi {
            color: var(--purple-dark);
            font-size: 13px;
            font-weight: 700;
        }

        .kompetensi {
            margin: 10px 0;
            color: var(--muted);
            font-size: 14px;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            color: var(--muted);
            font-size: 13px;
        }

        .meta i {
            color: var(--purple);
        }

        .action {
            text-align: right;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            color: var(--green);
            font-size: 12px;
            font-weight: 700;
        }

        .status::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--green);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 18px;
            border-radius: 12px;
            background: var(--purple);
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .btn:hover {
            background: var(--purple-dark);
        }

        .empty {
            padding: 50px;
            text-align: center;
            background: white;
            border: 1px solid var(--line);
            border-radius: 20px;
            color: var(--muted);
        }

        /* FLASH MESSAGE */
        .flash {
            margin: 0 0 20px;
            padding: 13px 18px;
            border-radius: 14px;
            font-weight: 600;
        }

        .flash.ok {
            background: #e5f6ee;
            color: #1f7a50;
        }

        .flash.bad {
            background: #fff0f2;
            color: #b43048;
        }

        /* AVAILABILITY */
        .status.off {
            color: #85829a;
            background: #f1f0f4;
        }

        .next {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            text-align: right;
            max-width: 150px;
        }

        button.btn {
            border: 0;
            cursor: pointer;
            font-family: inherit;
            font-size: inherit;
        }

        /* SERVICE MODAL */
        .svc-modal {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(41, 38, 58, .5);
        }

        .svc-modal[hidden] {
            display: none;
        }

        .svc-box {
            width: min(520px, 100%);
            max-height: 92vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 22px;
            padding: 24px;
        }

        .svc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .svc-head b {
            font: 800 21px "Jost", sans-serif;
        }

        .svc-head button {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 50%;
            background: #f3eff7;
            font-size: 20px;
            cursor: pointer;
        }

        .svc-who {
            margin: 0 0 12px;
            color: var(--muted);
            font-size: 14px;
        }

        .svc-avail {
            padding: 10px 14px;
            margin-bottom: 14px;
            border-radius: 12px;
            background: #e5f6ee;
            color: #1f7a50;
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.6;
        }

        .svc-avail.off {
            background: #fff4e5;
            color: #9a5b12;
        }

        .svc-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            margin-bottom: 10px;
            border: 1px solid var(--line);
            border-radius: 16px;
        }

        .svc-ico {
            flex: 0 0 44px;
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: var(--purple-soft);
            color: var(--purple);
            font-size: 18px;
        }

        .svc-info {
            flex: 1;
            min-width: 0;
        }

        .svc-info b {
            display: block;
            font-size: 16px;
        }

        .svc-info span {
            display: block;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .svc-price {
            font-weight: 800;
            color: var(--purple-dark);
        }

        .svc-form {
            margin: 0;
        }

        .svc-btn {
            display: inline-block;
            padding: 10px 14px;
            border: 0;
            border-radius: 12px;
            background: var(--purple);
            color: #fff;
            font: 700 13.5px "DM Sans", sans-serif;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .svc-btn:hover {
            background: var(--purple-dark);
        }

        .svc-btn:disabled {
            background: #cfc8dc;
            cursor: not-allowed;
        }

        .svc-sched {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed #e3d9ee;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        .svc-sched b {
            color: var(--ink);
        }

        /* RESPONSIVE */
        @media (max-width: 700px) {
            .consultant-card {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .action {
                width: 100%;
                text-align: left;
            }

            .next {
                text-align: left;
            }
        }

        @media (max-width: 550px) {
            .container {
                width: min(100% - 28px, 1050px);
            }

            .brand-logo {
                width: 115px;
                height: 40px;
            }

            .nav-actions {
                gap: 10px;
            }

            .back {
                font-size: 12px;
            }

            .heading h1 {
                font-size: 28px;
            }

            .content {
                padding: 40px 0;
            }
        }

        @media (max-width: 520px) {
            .svc-item {
                flex-wrap: wrap;
            }

            .svc-form,
            .svc-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar sticky-top">
        <div class="container nav-inner">

            <a href="{{ url('/') }}" class="brand" aria-label="Temu Edu beranda">
                <img
                    src="{{ asset('img/logo-temuedu.png') }}"
                    alt="Temu Edu"
                    class="brand-logo"
                >
            </a>

            <div class="nav-actions">

                @include('partials.notification-bell')

                @if (auth()->check() && auth()->user()->role === 'user')
                    <a href="{{ route('consultation.history') }}" class="back">
                        Konsultasi Saya
                    </a>

                    <a href="{{ route('profile.show') }}" class="back" title="Profil">
                        <i class="fa-regular fa-user"></i>
                    </a>
                @endif

                <a href="{{ route('consultation.prodi') }}" class="back">
                    <i class="fa-solid fa-arrow-left"></i>
                    Pilih Prodi
                </a>

            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="content">

        <div class="container">

            @if (session('error'))
                <div class="flash bad">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="flash ok">{{ session('success') }}</div>
            @endif

            <div class="heading">

                <h1>
                    Konsultan {{ $namaProdi }}
                </h1>

                <p>
                    Pilih konsultan yang sesuai dengan kebutuhanmu.
                </p>

            </div>

            @if (count($filteredConsultants) > 0)

                <div class="consultant-list">

                    @foreach ($filteredConsultants as $consultant)

                        <div class="consultant-card">

                            <img
                                src="{{ $consultant['foto'] }}"
                                alt="{{ $consultant['nama'] }}"
                                class="avatar"
                            >

                            <div class="info">

                                <h3>
                                    {{ $consultant['nama'] }}
                                </h3>

                                <div class="prodi">
                                    {{ $consultant['prodi'] }}
                                </div>

                                <div class="kompetensi">
                                    {{ $consultant['kompetensi'] }}
                                </div>

                                <div class="meta">

                                    <span>
                                        <i class="fa-solid fa-star"></i>
                                        {{ $consultant['rating'] }}
                                    </span>

                                    <span>
                                        <i class="fa-solid fa-graduation-cap"></i>
                                        IPK {{ $consultant['ipk'] }}
                                    </span>

                                    <span>
                                        <i class="fa-solid fa-wallet"></i>
                                        Mulai Rp{{ number_format(min(array_column($services, 'price')), 0, ',', '.') }}
                                    </span>

                                </div>

                            </div>

                            <div class="action">

                                @if ($consultant['tersedia'])
                                    <div class="status">
                                        Tersedia sekarang
                                    </div>
                                @else
                                    <div class="status off">
                                        Di luar jam layanan
                                    </div>

                                    @if ($consultant['berikutnya'])
                                        <div class="next">
                                            Berikutnya: {{ $consultant['berikutnya'] }}
                                        </div>
                                    @endif
                                @endif

                                <button
                                    type="button"
                                    class="btn js-svc"
                                    data-id="{{ $consultant['id'] }}"
                                    data-name="{{ $consultant['nama'] }}"
                                    data-available="{{ $consultant['tersedia'] ? '1' : '0' }}"
                                    data-next="{{ $consultant['berikutnya'] }}"
                                    data-jadwal="{{ json_encode($consultant['jadwal']) }}"
                                >
                                    Konsultasi
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="empty">

                    <i
                        class="fa-solid fa-user-slash"
                        style="font-size:35px;margin-bottom:15px;"
                    ></i>

                    <p>
                        Belum ada konsultan untuk program studi ini.
                    </p>

                </div>

            @endif

        </div>

    </main>

    <!-- SERVICE MODAL -->
    <div class="svc-modal" id="svcModal" hidden>

        <div class="svc-box">

            <div class="svc-head">
                <b>Pilih Layanan Konsultasi</b>
                <button type="button" id="svcClose" aria-label="Tutup">&times;</button>
            </div>

            <p class="svc-who" id="svcWho"></p>

            <div class="svc-avail" id="svcAvail"></div>

            @foreach ($services as $key => $svc)

                <div class="svc-item">

                    <div class="svc-ico">
                        <i class="fa-solid {{ $svc['icon'] }}"></i>
                    </div>

                    <div class="svc-info">
                        <b>{{ $svc['label'] }}</b>
                        <span>{{ $svc['minutes'] }} menit</span>
                        <span class="svc-price">
                            Rp{{ number_format($svc['price'], 0, ',', '.') }}
                        </span>

                        <span>
                            @if ($key === 'chat')
                                Bayar dulu, lalu chat dengan konsultan.
                            @elseif ($key === 'online_meet')
                                Pilih tanggal &amp; jam, lalu bayar.
                            @else
                                Atur waktu &amp; lokasi lewat chat, bayar setelah disetujui.
                            @endif
                        </span>
                    </div>

                    @if ($key === 'chat')

                        <form
                            method="POST"
                            class="svc-form"
                            data-tpl="{{ route('service.chat', '__ID__') }}"
                        >
                            @csrf
                            <button type="submit" class="svc-btn" id="svcChatBtn">
                                Mulai Chat
                            </button>
                        </form>

                    @elseif ($key === 'online_meet')

                        <a
                            class="svc-btn"
                            data-tpl-href="{{ route('service.online', '__ID__') }}"
                            href="#"
                        >
                            Pilih Jadwal
                        </a>

                    @else

                        <form
                            method="POST"
                            class="svc-form"
                            data-tpl="{{ route('service.offline', '__ID__') }}"
                        >
                            @csrf
                            <button type="submit" class="svc-btn">
                                Ajukan Pertemuan
                            </button>
                        </form>

                    @endif

                </div>

            @endforeach

            <div class="svc-sched" id="svcSched"></div>

        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        (function () {
            var modal = document.getElementById('svcModal');
            var who = document.getElementById('svcWho');
            var avail = document.getElementById('svcAvail');
            var sched = document.getElementById('svcSched');
            var chatBtn = document.getElementById('svcChatBtn');

            function close() {
                modal.hidden = true;
            }

            document.getElementById('svcClose').addEventListener('click', close);

            modal.addEventListener('click', function (e) {
                if (e.target === modal) close();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') close();
            });

            document.querySelectorAll('.js-svc').forEach(function (btn) {

                btn.addEventListener('click', function () {

                    var id = btn.getAttribute('data-id');
                    var available = btn.getAttribute('data-available') === '1';
                    var next = btn.getAttribute('data-next');
                    var jadwal = [];

                    try {
                        jadwal = JSON.parse(btn.getAttribute('data-jadwal') || '[]');
                    } catch (e) {}

                    who.textContent = 'Konsultan: ' + btn.getAttribute('data-name');

                    avail.className = 'svc-avail' + (available ? '' : ' off');

                    avail.textContent = available
                        ? 'Konsultan tersedia sekarang.'
                        : 'Konsultan sedang di luar jam layanan' +
                          (next ? ' - berikutnya: ' + next + '.' : '. Jadwal layanan belum diatur.') +
                          ' Chat baru bisa dimulai saat jam layanan; Online Meet & Offline Meet tetap bisa dijadwalkan.';

                    if (chatBtn) {
                        chatBtn.disabled = !available;
                    }

                    document.querySelectorAll('#svcModal [data-tpl]').forEach(function (f) {
                        f.action = f.getAttribute('data-tpl').replace('__ID__', id);
                    });

                    document.querySelectorAll('#svcModal [data-tpl-href]').forEach(function (a) {
                        a.href = a.getAttribute('data-tpl-href').replace('__ID__', id);
                    });

                    sched.innerHTML = '';

                    if (jadwal.length) {

                        var t = document.createElement('b');
                        t.textContent = 'Jam layanan konsultan';
                        sched.appendChild(t);

                        jadwal.forEach(function (j) {
                            var row = document.createElement('div');
                            row.textContent = j.hari + ': ' + j.jam;
                            sched.appendChild(row);
                        });
                    }

                    modal.hidden = false;
                });

            });

        })();
    </script>

</body>
</html>
