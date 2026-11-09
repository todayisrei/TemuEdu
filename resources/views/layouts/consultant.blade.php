<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard') | Konsultan TemuEdu</title>

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    @include('layouts.partials.panel-styles')

    /* Logo sidebar konsultan */
    .side-brand {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 5px;
      width: 100%;
    }

    .side-brand-logo {
      height: 90px;
      width: auto;
      max-width: 100%;
      object-fit: contain;
      object-position: left center;
      display: block;
    }

    .side-brand small {
      display: block;
    }

    /* Tambahan khusus konsultan */
    .side-nav a.sub {
      margin-left: 18px;
      padding: 8px 12px;
      font-size: 14px;
      font-weight: 500;
    }

    .side-nav a.sub i {
      font-size: 6px;
    }

    .tabs {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 18px;
    }

    .tabs a {
      padding: 8px 16px;
      border-radius: 20px;
      border: 1px solid #e3d9ee;
      background: #fff;
      font-weight: 700;
      font-size: 14px;
      color: var(--purple-dark);
    }

    .tabs a.active {
      background: var(--purple);
      border-color: var(--purple);
      color: #fff;
    }

    .badge.warn {
      background: #fff0e4;
      color: #c97432;
    }

    .badge.new {
      background: var(--orange);
      color: #fff;
    }

    .kv {
      display: grid;
      grid-template-columns: 150px 1fr;
      gap: 10px 16px;
      margin: 0;
    }

    .kv dt {
      color: var(--muted);
    }

    .kv dd {
      margin: 0;
      font-weight: 600;
      word-break: break-word;
    }

    .slot {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      padding: 12px 0;
      border-bottom: 1px solid #f4eff8;
    }

    .slot:last-child {
      border-bottom: 0;
    }

    .inline-form {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      align-items: center;
    }

    .inline-form input {
      padding: 8px 12px;
      border: 1px solid #e3d9ee;
      border-radius: 10px;
      font: inherit;
      min-width: 200px;
    }

    .stats.four {
      grid-template-columns: repeat(4, 1fr);
    }

    @media (max-width: 991px) {
      .stats.four {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 640px) {
      .stats.four {
        grid-template-columns: 1fr;
      }

      .kv {
        grid-template-columns: 1fr;
        gap: 2px 0;
      }

      .kv dd {
        margin-bottom: 10px;
      }
    }
  </style>

  @stack('styles')
</head>

<body>

  <aside class="side" id="side">

    {{-- Logo dan identitas Konsultan --}}
    <div class="side-brand">
      <img
        src="{{ asset('img/logo-adminte.png') }}"
        alt="Temu Edu"
        class="side-brand-logo"
      >
      <small>Konsultan</small>
    </div>

    <nav class="side-nav">

      @php
        $st = request('status');
        $onCons = request()->routeIs('consultant.consultations.*');
      @endphp

      <div class="label">Menu</div>

      <a
        href="{{ route('consultant.dashboard') }}"
        class="{{ request()->routeIs('consultant.dashboard') ? 'active' : '' }}"
      >
        <i class="fa-solid fa-gauge-high"></i>
        Dashboard
      </a>

      <a
        href="{{ route('consultant.consultations.index') }}"
        class="{{ $onCons && ! $st ? 'active' : '' }}"
      >
        <i class="fa-solid fa-comments"></i>
        Konsultasi
      </a>

      <a
        href="{{ route('consultant.consultations.index', ['status' => 'menunggu']) }}"
        class="sub {{ $onCons && $st === 'menunggu' ? 'active' : '' }}"
      >
        <i class="fa-solid fa-circle"></i>
        Menunggu
      </a>

      <a
        href="{{ route('consultant.consultations.index', ['status' => 'berlangsung']) }}"
        class="sub {{ $onCons && $st === 'berlangsung' ? 'active' : '' }}"
      >
        <i class="fa-solid fa-circle"></i>
        Berlangsung
      </a>

      <a
        href="{{ route('consultant.consultations.index', ['status' => 'selesai']) }}"
        class="sub {{ $onCons && $st === 'selesai' ? 'active' : '' }}"
      >
        <i class="fa-solid fa-circle"></i>
        Selesai
      </a>

      <a
        href="{{ route('consultant.schedules.index') }}"
        class="{{ request()->routeIs('consultant.schedules.*') ? 'active' : '' }}"
      >
        <i class="fa-solid fa-calendar-days"></i>
        Jadwal
      </a>

      <a
        href="{{ route('consultant.profile') }}"
        class="{{ request()->routeIs('consultant.profile') ? 'active' : '' }}"
      >
        <i class="fa-solid fa-id-card"></i>
        Profil
      </a>

      <div class="label">Lainnya</div>

      <a href="{{ url('/') }}" target="_blank">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
        Lihat Website
      </a>

    </nav>

    <div class="side-foot">

      <div class="who">
        <div class="ava">
          {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
        </div>

        <div>
          <b>{{ auth()->user()->name }}</b>
          <span>Konsultan</span>
        </div>
      </div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit">
          <i class="fa-solid fa-right-from-bracket"></i>
          Keluar
        </button>
      </form>

    </div>

  </aside>

  <div class="backdrop" id="backdrop"></div>

  <div class="main">

    <header class="top">

      <button
        class="menu-btn"
        id="menuBtn"
        type="button"
        aria-label="Buka menu"
      >
        <i class="fa-solid fa-bars"></i>
      </button>

      <h1>@yield('heading', 'Dashboard')</h1>

      @include('partials.notification-bell')

      @yield('top_action')

    </header>

    <main class="page">

      @if (session('success'))
        <div class="alert success">
          <i class="fa-solid fa-circle-check"></i>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      @if (session('error'))
        <div class="alert error">
          <i class="fa-solid fa-circle-exclamation"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert error">
          <i class="fa-solid fa-circle-exclamation"></i>

          <div>
            Ada data yang perlu diperbaiki:

            <ul>
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      @yield('content')

    </main>

  </div>

  <script>
    (function () {
      var side = document.getElementById('side');
      var bd = document.getElementById('backdrop');
      var btn = document.getElementById('menuBtn');

      function toggle(open) {
        side.classList.toggle('open', open);
        bd.classList.toggle('show', open);
      }

      btn.addEventListener('click', function () {
        toggle(!side.classList.contains('open'));
      });

      bd.addEventListener('click', function () {
        toggle(false);
      });
    })();
  </script>

  @stack('scripts')

</body>
</html>