
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar | Temu Edu</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>
        :root {
            --purple: #9275c5;
            --purple-dark: #6e559c;
            --purple-soft: #f1e9fc;
            --pink-soft: #fff0f5;
            --ink: #29263a;
            --muted: #777386;
            --line: #e8e1ee;
            --white: #ffffff;
            --danger: #dc3545;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            color: var(--ink);

            background:
                linear-gradient(
                    135deg,
                    #fbf8ff 0%,
                    #fff8fb 52%,
                    #fffaf4 100%
                );
        }

        /* =========================
           PAGE
        ========================= */

        .page {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 28px;
        }

        /* =========================
           MAIN CARD
        ========================= */

        .register-shell {
            width: min(100%, 980px);
            min-height: 590px;

            display: grid;
            grid-template-columns: 1fr 1fr;

            background: #fff;

            border: 1px solid rgba(231, 220, 241, .8);

            border-radius: 28px;

            overflow: hidden;

            box-shadow:
                0 24px 70px rgba(83, 61, 112, .12);
        }

        /* =========================
           LEFT SIDE
        ========================= */

        .welcome {
            position: relative;

            overflow: hidden;

            padding: 48px;

            background:
                linear-gradient(
                    145deg,
                    #f1e9fc,
                    #fff0f5
                );

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

        .welcome-content {
            margin-top: 20px;
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

            margin: 0;

            color: #6e687d;

            line-height: 1.8;

            font-size: 15px;
        }

        /* =========================
           MASCOT
        ========================= */

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

            filter:
                drop-shadow(0 14px 18px #a77c6930);
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

            box-shadow:
                0 8px 25px #9275c51b;
        }

        /* =========================
           RIGHT SIDE
        ========================= */

        .form-side {
            padding: 50px 54px;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-side h2 {
            margin: 0 0 8px;

            font-family: 'Poppins', sans-serif;

            font-size: 29px;

            font-weight: 800;

            letter-spacing: -.7px;
        }

        .sub {
            margin: 0 0 28px;

            color: var(--muted);

            font-size: 14px;
        }

        /* =========================
           FORM
        ========================= */

        .form-group {
            margin-bottom: 17px;
        }

        .field-label {
            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 800;
        }

        .field {
            width: 100%;
            height: 49px;

            border: 1px solid var(--line);

            border-radius: 13px;

            padding: 0 15px;

            font-family: 'Nunito', sans-serif;

            font-size: 14px;

            color: var(--ink);

            background: white;

            outline: none;

            transition: 0.2s;
        }

        .field::placeholder {
            color: #aaa5b2;
        }

        .field:focus {
            border-color: var(--purple);

            box-shadow:
                0 0 0 4px rgba(146, 117, 197, 0.10);
        }

        .field-error {
            margin-top: 6px;

            color: var(--danger);

            font-size: 12px;
        }

        /* =========================
           PASSWORD
        ========================= */

        .input-wrap {
            position: relative;
        }

        .input-wrap .field {
            padding-right: 48px;
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

            font-size: 14px;
        }

        /* =========================
           BUTTON
        ========================= */

        .btn-register {
            width: 100%;
            height: 50px;

            margin-top: 5px;

            border: 0;

            border-radius: 13px;

            background:
                linear-gradient(
                    110deg,
                    #9275c5,
                    #aa8bd4
                );

            color: white;

            font-family: 'Nunito', sans-serif;

            font-size: 14px;

            font-weight: 900;

            cursor: pointer;

            box-shadow:
                0 9px 20px rgba(146, 117, 197, 0.18);

            transition: 0.2s;
        }

        .btn-register:hover {
            transform: translateY(-1px);

            box-shadow:
                0 12px 24px rgba(146, 117, 197, 0.23);
        }

        /* =========================
           LOGIN LINK
        ========================= */

        .login-link {
            text-align: center;

            font-size: 14px;

            color: var(--muted);

            margin: 20px 0 0;
        }

        .login-link a {
            color: var(--purple-dark);

            text-decoration: none;

            font-weight: 800;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* =========================
           BACK
        ========================= */

        .back {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            margin-top: 22px;

            color: #7a7387;

            font-size: 13px;

            text-decoration: none;
        }

        .back:hover {
            color: var(--purple-dark);
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 760px) {

            .page {
                padding: 16px;
            }

            .register-shell {
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
                max-width: 260px;
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

<div class="page">

    <div class="register-shell">

        <!-- =====================================
             LEFT / WELCOME
        ====================================== -->

        <section class="welcome">

            <!-- LOGO TEMU EDU -->

            <a
                href="{{ url('/') }}"
                class="brand"
                aria-label="Temu Edu beranda"
            >
                <img
                    src="{{ asset('img/logo-temuedu.png') }}"
                    alt="Temu Edu"
                    class="brand-logo"
                >
            </a>


            <div class="welcome-content">

                <h1>
                    Yuk, mulai
                    <span>belajar bareng.</span>
                </h1>

                <p>
                    Buat akun Temu Edu dan temukan teman diskusi
                    yang sesuai dengan kebutuhan belajarmu.
                </p>

            </div>


            <!-- MASKOT KIKO -->

            <div class="mascot-area">

                <div class="bubble">
                    Siap belajar? ✨
                </div>

                <img
                    src="{{ asset('img/logo-login.png') }}"
                    alt="Maskot Kiko Temu Edu"
                    class="mascot"
                >

            </div>

        </section>


        <!-- =====================================
             RIGHT / REGISTER FORM
        ====================================== -->

        <section class="form-side">

            <h2>Buat Akun Baru</h2>

            <p class="sub">
                Daftar untuk mulai berkonsultasi dan belajar bersama.
            </p>


            @if ($errors->any())
                <div
                    style="
                        background:#fff0f0;
                        color:#b42318;
                        padding:12px 14px;
                        border-radius:12px;
                        font-size:13px;
                        margin-bottom:18px;
                    "
                    role="alert"
                >
                    {{ $errors->first() }}
                </div>
            @endif


            <form
                action="{{ route('register') }}"
                method="POST"
            >

                @csrf


                <!-- NAMA -->

                <div class="form-group">

                    <label
                        for="name"
                        class="field-label"
                    >
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="field"
                        placeholder="Masukkan nama lengkap"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label
                        for="email"
                        class="field-label"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="field"
                        placeholder="nama@email.com"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                    >

                    @error('email')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label
                        for="password"
                        class="field-label"
                    >
                        Kata Sandi
                    </label>

                    <div class="input-wrap">

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="field"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-pass"
                            onclick="togglePassword('password', this)"
                            aria-label="Tampilkan kata sandi"
                        >
                            <i class="fa-regular fa-eye"></i>
                        </button>

                    </div>

                    @error('password')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- KONFIRMASI PASSWORD -->

                <div class="form-group">

                    <label
                        for="password_confirmation"
                        class="field-label"
                    >
                        Konfirmasi Kata Sandi
                    </label>

                    <div class="input-wrap">

                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="field"
                            placeholder="Ulangi kata sandi"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-pass"
                            onclick="togglePassword('password_confirmation', this)"
                            aria-label="Tampilkan konfirmasi kata sandi"
                        >
                            <i class="fa-regular fa-eye"></i>
                        </button>

                    </div>

                </div>


                <!-- SUBMIT -->

                <button
                    type="submit"
                    class="btn-register"
                >
                    Daftar Sekarang
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

            </form>


            <!-- LOGIN LINK -->

            <p class="login-link">
                Sudah punya akun?
                <a href="{{ route('login') }}">
                    Masuk di sini
                </a>
            </p>


            <!-- BACK TO HOME -->

            <a
                href="{{ url('/') }}"
                class="back"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke beranda
            </a>

        </section>

    </div>

</div>


<!-- =====================================
     JAVASCRIPT
====================================== -->

<script>
    function togglePassword(inputId, button) {

        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');

        if (input.type === 'password') {

            input.type = 'text';

            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');

        } else {

            input.type = 'password';

            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');

        }
    }
</script>

</body>
</html>
