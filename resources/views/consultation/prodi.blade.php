
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pilih Program Studi | Temu Edu</title>

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
            width: min(1100px, calc(100% - 40px));
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
            padding: 65px 0;
        }

        .heading {
            text-align: center;
            margin-bottom: 40px;
        }

        .heading h1 {
            margin: 0 0 10px;
            font-family: "Jost", sans-serif;
            font-size: 38px;
        }

        .heading p {
            margin: 0;
            color: var(--muted);
        }

        /* PROGRAM STUDI */
        .prodi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .prodi-card {
            padding: 28px;
            background: white;
            border: 1px solid var(--line);
            border-radius: 20px;
            text-decoration: none;
            color: var(--ink);
            transition: .2s;
        }

        .prodi-card:hover {
            transform: translateY(-4px);
            border-color: #d9c9e8;
            box-shadow: 0 15px 35px rgba(100, 70, 130, .08);
        }

        .icon {
            width: 55px;
            height: 55px;
            display: grid;
            place-items: center;
            margin-bottom: 20px;
            border-radius: 16px;
            background: var(--purple-soft);
            color: var(--purple);
            font-size: 22px;
        }

        .prodi-card h3 {
            margin: 0 0 8px;
            font-family: "Jost", sans-serif;
            font-size: 20px;
        }

        .prodi-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 14px;
        }

        /* TABLET */
        @media (max-width: 800px) {
            .prodi-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* MOBILE */
        @media (max-width: 550px) {
            .container {
                width: min(100% - 28px, 1100px);
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

            .prodi-grid {
                grid-template-columns: 1fr;
            }

            .heading h1 {
                font-size: 30px;
            }

            .content {
                padding: 45px 0;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar sticky-top">
        <div class="container nav-inner">

            <a class="brand" href="{{ url('/') }}" aria-label="Temu Edu beranda">
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

                <a href="{{ url('/') }}" class="back">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali
                </a>

            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="content">

        <div class="container">

            @if (session('error'))
                <div style="margin:0 0 20px;padding:13px 18px;border-radius:14px;background:#fff0f2;color:#b43048;font-weight:600">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div style="margin:0 0 20px;padding:13px 18px;border-radius:14px;background:#e5f6ee;color:#1f7a50;font-weight:600">
                    {{ session('success') }}
                </div>
            @endif

            <div class="heading">

                <h1>
                    Mau konsultasi tentang apa?
                </h1>

                <p>
                    Pilih program studi untuk menemukan konsultan yang sesuai.
                </p>

            </div>

            <div class="prodi-grid">

                @foreach ($prodis as $prodi)

                    <a
                        href="{{ route('consultation.consultants', $prodi['id']) }}"
                        class="prodi-card"
                    >

                        <div class="icon">
                            <i class="fa-solid {{ $prodi['icon'] }}"></i>
                        </div>

                        <h3>
                            {{ $prodi['nama'] }}
                        </h3>

                        <p>
                            {{ $prodi['deskripsi'] }}
                        </p>

                    </a>

                @endforeach

            </div>

        </div>

    </main>

</body>
</html>
