
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Temu Edu')</title>

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root {
      --ink: #29283a;
      --muted: #77758a;
      --purple: #7661a8;
      --purple-dark: #5d498e;
      --purple-soft: #f1ebfb;
      --pink-soft: #fff4f7;
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

    a {
      color: inherit;
      text-decoration: none;
    }

    h1, h2, h3 {
      font-family: "Jost", sans-serif;
      margin: 0;
    }

    .container {
      width: min(1100px, calc(100% - 40px));
      margin: 0 auto;
    }

    /* NAVBAR */
    .sn {
      position: sticky;
      top: 0;
      z-index: 1030;
      background: #fff;
      border-bottom: 1px solid var(--line);
      padding: 14px 0;
    }

    .sn-in {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      flex-wrap: wrap;
    }

    /* LOGO TEMU EDU */
    .sn .brand {
      display: inline-flex;
      align-items: center;
      flex-shrink: 0;
      text-decoration: none;
    }

    .brand-logo {
      width: 150px;
      height: auto;
      max-height: 55px;
      object-fit: contain;
      display: block;
    }

    /* MENU NAVBAR */
    .sn-right {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .sn-link {
      padding: 9px 13px;
      border-radius: 10px;
      color: #5e5b70;
      font-weight: 600;
      font-size: 15px;
    }

    .sn-link:hover,
    .sn-link.active {
      color: var(--purple);
      background: #faf6ff;
    }

    /* TOMBOL */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      padding: 12px 22px;
      border-radius: 14px;
      border: 1px solid transparent;
      background: var(--purple);
      color: #fff;
      font: 700 15px "DM Sans", sans-serif;
      cursor: pointer;
      transition: .2s;
    }

    .btn:hover {
      background: var(--purple-dark);
      transform: translateY(-2px);
    }

    .btn-line {
      background: #fff;
      color: var(--purple-dark);
      border-color: #e3d9ee;
    }

    .btn-line:hover {
      background: #faf6ff;
      transform: none;
    }

    .btn-sm {
      padding: 8px 14px;
      font-size: 14px;
      border-radius: 11px;
    }

    .sn form {
      margin: 0;
    }

    /* NOTIFIKASI */
    .flash {
      margin: 18px 0 0;
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

    /* BADGE */
    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
      background: var(--purple-soft);
      color: var(--purple-dark);
    }

    .badge.ok {
      background: #e5f6ee;
      color: #2e9b69;
    }

    .badge.off {
      background: #f1f0f4;
      color: #85829a;
    }

    .badge.new {
      background: #e5484d;
      color: #fff;
    }

    /* CARD */
    .card {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 22px;
      padding: 26px;
    }

    /* RESPONSIVE */
    @media (max-width: 640px) {
      .sn-link .lbl {
        display: none;
      }

      .brand-logo {
        width: 125px;
        max-height: 45px;
      }

      .sn-in {
        gap: 10px;
      }

      .sn-right {
        gap: 4px;
      }

      .sn-link {
        padding: 8px 9px;
        font-size: 13px;
      }
    }
  </style>

  @stack('styles')
</head>

<body>

  @php
    $role = auth()->check() ? auth()->user()->role : null;
  @endphp

  <!-- NAVBAR -->
  <nav class="sn">
    <div class="container sn-in">

      <!-- LOGO -->
      <a href="{{ url('/') }}" class="brand" aria-label="Temu Edu beranda">
        <img
          src="{{ asset('img/logo-temuedu.png') }}"
          alt="Temu Edu"
          class="brand-logo"
        >
      </a>

      <!-- MENU NAVBAR -->
      <div class="sn-right">

        <a href="{{ url('/') }}" class="sn-link">
          Beranda
        </a>

        <a href="{{ route('discussion.intro') }}"
           class="sn-link {{ request()->routeIs('discussion.*') ? 'active' : '' }}">
          Cari Teman Diskusi
        </a>

        @if ($role === 'user')
          <a href="{{ route('consultation.history') }}"
             class="sn-link {{ request()->routeIs('consultation.history') ? 'active' : '' }}">
            Konsultasi Saya
          </a>
        @endif

        @auth
          @include('partials.notification-bell')

          @if ($role === 'user')
            <a href="{{ route('profile.show') }}"
               class="sn-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
              <i class="fa-regular fa-user"></i>
              <span class="lbl">Profil</span>
            </a>

          @elseif ($role === 'konsultan')
            <a href="{{ route('consultant.dashboard') }}" class="sn-link">
              Dashboard
            </a>

          @elseif ($role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="sn-link">
              Dashboard Admin
            </a>
          @endif

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-line btn-sm">
              Keluar
            </button>
          </form>

        @else
          <a href="{{ route('login') }}" class="btn btn-line btn-sm">
            Masuk
          </a>
        @endauth

      </div>
    </div>
  </nav>

  <!-- PESAN NOTIFIKASI -->
  <div class="container">
    @if (session('success'))
      <div class="flash ok">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="flash bad">
        {{ session('error') }}
      </div>
    @endif
  </div>

  <!-- KONTEN HALAMAN -->
  @yield('content')

  @stack('scripts')

</body>
</html>
