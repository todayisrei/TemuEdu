```blade
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard') | Admin TemuEdu</title>

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    @include('layouts.partials.panel-styles')

    /* Logo TemuEdu pada sidebar Administrator */

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
  </style>

  @stack('styles')
</head>
<body>
  <aside class="side" id="side">
    <div class="side-brand">
      <img
        src="{{ asset('img/logo-adminte.png') }}"
        alt="Temu Edu"
        class="side-brand-logo"
      >
      <small>Administrator</small>
    </div>

    <nav class="side-nav">
      <div class="label">Menu utama</div>

      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
      </a>

      <a href="{{ route('admin.consultants.index') }}" class="{{ request()->routeIs('admin.consultants.*') ? 'active' : '' }}">
        <i class="fa-solid fa-chalkboard-user"></i> Konsultan
      </a>

      <a href="{{ route('admin.prodi.index') }}" class="{{ request()->routeIs('admin.prodi.*') ? 'active' : '' }}">
        <i class="fa-solid fa-graduation-cap"></i> Program Studi
      </a>

      <a href="{{ route('admin.consultations.index') }}" class="{{ request()->routeIs('admin.consultations.*') ? 'active' : '' }}">
        <i class="fa-solid fa-comments"></i> Konsultasi
      </a>

      <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
        <i class="fa-solid fa-wallet"></i> Pembayaran
      </a>

      <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i> Mahasiswa
      </a>

      <div class="label">Lainnya</div>

      <a href="{{ url('/') }}" target="_blank">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website
      </a>
    </nav>

    <div class="side-foot">
      <div class="who">
        <div class="ava">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
        <div>
          <b>{{ auth()->user()->name }}</b>
          <span>Administrator</span>
        </div>
      </div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">
          <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </button>
      </form>
    </div>
  </aside>

  <div class="backdrop" id="backdrop"></div>

  <div class="main">
    <header class="top">
      <button class="menu-btn" id="menuBtn" type="button" aria-label="Buka menu">
        <i class="fa-solid fa-bars"></i>
      </button>

      <h1>@yield('heading', 'Dashboard')</h1>

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
```