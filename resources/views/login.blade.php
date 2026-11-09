
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk | Temu Edu</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root {
      --purple: #9275c5;
      --purple-dark: #6e559c;
      --pink: #f5dce8;
      --ink: #29263a;
      --muted: #777386;
      --line: #eee7f4;
      --orange: #f39a55;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: 'Nunito', sans-serif;
      color: var(--ink);
      background: linear-gradient(135deg, #fbf8ff 0%, #fff8fb 52%, #fffaf4 100%);
    }

    .page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 28px;
    }

    .login-shell {
      width: min(100%, 980px);
      min-height: 590px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      background: #fff;
      border: 1px solid rgba(231,220,241,.8);
      border-radius: 28px;
      overflow: hidden;
      box-shadow: 0 24px 70px rgba(83,61,112,.12);
    }

    /* BAGIAN KIRI */
    .welcome {
      position: relative;
      overflow: hidden;
      padding: 48px;
      background: linear-gradient(145deg, #f1e9fc, #fff0f5);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* LOGO TEMU EDU */
    .brand {
      display: inline-flex;
      align-items: center;
      width: fit-content;
      text-decoration: none;
    }

    .brand-logo {
      width: 160px;
      height: auto;
      max-height: 60px;
      object-fit: contain;
      display: block;
    }

    .welcome h1 {
      font-family: 'Poppins', sans-serif;
      font-size: clamp(30px, 4vw, 43px);
      line-height: 1.2;
      font-weight: 800;
      letter-spacing: -1.5px;
      margin: 28px 0 14px;
    }

    .welcome h1 span {
      color: var(--purple-dark);
    }

    .welcome p {
      max-width: 360px;
      color: #6e687d;
      line-height: 1.8;
      font-size: 15px;
    }

    /* MASKOT */
    .mascot-area {
      display: flex;
      align-items: end;
      justify-content: center;
      min-height: 190px;
      position: relative;
    }

    .mascot {
      width: 350px;
      max-width: 90%;
      height: auto;
      object-fit: contain;
      filter: drop-shadow(0 14px 18px #a77c6930);
    }

    .bubble {
      position: absolute;
      right: 0;
      top: 30px;
      padding: 12px 16px;
      background: #fff;
      border-radius: 16px 16px 4px 16px;
      font-size: 13px;
      font-weight: 800;
      color: var(--purple-dark);
      box-shadow: 0 8px 25px #9275c51b;
    }

    /* BAGIAN FORM */
    .form-side {
      padding: 50px 54px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-side h2 {
      font-family: 'Poppins', sans-serif;
      font-size: 29px;
      font-weight: 800;
      letter-spacing: -.7px;
      margin: 0 0 8px;
    }

    .sub {
      color: var(--muted);
      font-size: 14px;
      margin-bottom: 28px;
    }

    .field-label {
      font-size: 13px;
      font-weight: 800;
      margin-bottom: 8px;
      display: block;
    }

    .field {
      width: 100%;
      height: 49px;
      border: 1px solid #e8e1ee;
      border-radius: 13px;
      padding: 0 15px;
      font: inherit;
      outline: none;
      background: #fff;
      transition: .2s;
    }

    .field:focus {
      border-color: var(--purple);
      box-shadow: 0 0 0 4px #9275c51a;
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap .field {
      padding-right: 45px;
    }

    .toggle-pass {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: transparent;
      color: #8a8498;
      cursor: pointer;
    }

    .form-group {
      margin-bottom: 17px;
    }

    .form-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      font-size: 13px;
      margin: 3px 0 22px;
    }

    .remember {
      display: flex;
      gap: 8px;
      align-items: center;
      color: #777386;
    }

    .remember input {
      accent-color: var(--purple);
    }

    a {
      color: var(--purple-dark);
      text-decoration: none;
      font-weight: 800;
    }

    .btn-login {
      width: 100%;
      height: 50px;
      border: 0;
      border-radius: 13px;
      background: linear-gradient(110deg, #9275c5, #aa8bd4);
      color: #fff;
      font-weight: 900;
      box-shadow: 0 9px 20px #9275c52d;
      transition: .2s;
      cursor: pointer;
    }

    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 12px 24px #9275c53b;
    }

    .signup {
      text-align: center;
      font-size: 14px;
      color: var(--muted);
      margin-top: 22px;
    }

    .back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: #7a7387;
      margin-top: 28px;
    }

    .message {
      display: none;
      background: #f0eafa;
      color: #5d4689;
      padding: 12px 14px;
      border-radius: 12px;
      font-size: 13px;
      margin-bottom: 16px;
    }

    .message.show {
      display: block;
    }

    .small-note {
      font-size: 11px;
      color: #a19aaa;
      text-align: center;
      margin-top: 16px;
      line-height: 1.6;
    }

    /* RESPONSIVE */
    @media (max-width: 760px) {
      .page {
        padding: 16px;
      }

      .login-shell {
        grid-template-columns: 1fr;
        max-width: 480px;
      }

      .welcome {
        padding: 25px 28px 10px;
        min-height: 245px;
      }

      .brand-logo {
        width: 135px;
        max-height: 48px;
      }

      .welcome h1 {
        font-size: 29px;
        margin: 20px 0 8px;
      }

      .welcome p {
        font-size: 13px;
        margin-bottom: 0;
      }

      .mascot-area {
        position: absolute;
        right: 16px;
        bottom: 8px;
        min-height: 0;
        width: 180px;
        opacity: .95;
      }

      .mascot {
        width: 180px;
        max-width: 100%;
      }

      .bubble {
        display: none;
      }

      .form-side {
        padding: 32px 28px 28px;
      }

      .form-side h2 {
        font-size: 25px;
      }

      .sub {
        margin-bottom: 22px;
      }
    }
  </style>
</head>

<body>
  <main class="page">
    <div class="login-shell">

      <!-- BAGIAN KIRI -->
      <section class="welcome">

        <!-- LOGO TEMU EDU -->
        <a class="brand" href="{{ url('/') }}" aria-label="Temu Edu beranda">
          <img
            src="{{ asset('img/logo-temuedu.png') }}"
            alt="Temu Edu"
            class="brand-logo"
          >
        </a>

        <div>
          <h1>Senang melihatmu <span>kembali!</span></h1>
          <p>
            Masuk dan lanjutkan perjalanan belajarmu. Kalau ada tugas yang bikin bingung,
            kita cari jawabannya bareng-bareng.
          </p>
        </div>

        <!-- MASKOT -->
        <div class="mascot-area">
          <div class="bubble">Semangat belajarnya! ✨</div>

          <img
            src="{{ asset('img/logo-login.png') }}"
            alt="Logo Temu Edu"
            class="mascot"
          >
        </div>

      </section>

      <!-- BAGIAN FORM LOGIN -->
      <section class="form-side">

        <h2>Masuk ke akunmu</h2>
        <p class="sub">Isi detail akun untuk melanjutkan ke Temu Edu.</p>

        <div id="notice" class="message @if ($errors->any()) show @endif" role="alert">
          @if ($errors->any())
            {{ $errors->first() }}
          @endif
        </div>

        <form id="loginForm" method="POST" action="{{ route('login') }}">
          @csrf

          <div class="form-group">
            <label class="field-label" for="email">Email</label>
            <input
              class="field"
              type="email"
              id="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="nama@email.com"
              autocomplete="email"
              required
              autofocus
            >
          </div>

          <div class="form-group">
            <label class="field-label" for="password">Kata sandi</label>

            <div class="input-wrap">
              <input
                class="field"
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan kata sandi"
                autocomplete="current-password"
                required
              >

              <button
                class="toggle-pass"
                type="button"
                id="togglePass"
                aria-label="Tampilkan kata sandi"
              >
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-row">
            <label class="remember">
              <input type="checkbox" id="remember" name="remember" value="1">
              Ingat saya
            </label>

            <a href="#" id="forgotLink">Lupa kata sandi?</a>
          </div>

          <button class="btn-login" type="submit">
            Masuk sekarang
            <i class="fa-solid fa-arrow-right ms-1"></i>
          </button>
        </form>

        <p class="signup">
          Belum punya akun?
          <a href="{{ route('register') }}">Daftar di sini</a>
        </p>

        <a class="back" href="{{ url('/') }}">
          <i class="fa-solid fa-arrow-left"></i>
          Kembali ke beranda
        </a>

      </section>
    </div>
  </main>

  <script>
    const notice = document.getElementById('notice');

    function showNotice(text) {
      notice.textContent = text;
      notice.classList.add('show');
    }

    document.getElementById('togglePass').addEventListener('click', () => {
      const p = document.getElementById('password');
      const isPass = p.type === 'password';

      p.type = isPass ? 'text' : 'password';

      const icon = document.querySelector('#togglePass i');
      icon.className = isPass
        ? 'fa-regular fa-eye-slash'
        : 'fa-regular fa-eye';
    });

    document.getElementById('forgotLink').addEventListener('click', e => {
      e.preventDefault();
      showNotice('Fitur lupa kata sandi belum tersedia. Silakan hubungi administrator.');
    });
  </script>
</body>
</html>
