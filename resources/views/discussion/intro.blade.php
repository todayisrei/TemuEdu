
@extends('layouts.student')

@section('title', 'Cari Teman Diskusi | Temu Edu')

@push('styles')
<style>
  /* HERO SECTION */
  .hero {
    position: relative;
    overflow: hidden;
    padding: 56px 0 70px;
  }

  .hero::before {
    content: "";
    position: absolute;
    right: -140px;
    top: -120px;
    width: 520px;
    height: 520px;
    border-radius: 50%;
    background: radial-gradient(circle at 40% 40%, #efe6fb, #f8f1ff 60%, transparent 72%);
    z-index: -1;
  }

  .hero-grid {
    display: grid;
    grid-template-columns: 1.1fr .9fr;
    gap: 40px;
    align-items: center;
  }

  /* TEKS HERO */
  .kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 20px;
    background: var(--purple-soft);
    color: var(--purple-dark);
    font-weight: 700;
    font-size: 13.5px;
  }

  .hero h1 {
    margin: 18px 0 14px;
    font-size: clamp(34px, 5vw, 52px);
    line-height: 1.12;
    letter-spacing: -.5px;
  }

  .hero h1 em {
    font-style: normal;
    color: var(--purple);
  }

  .lead {
    max-width: 520px;
    margin: 0 0 26px;
    color: var(--muted);
    font-size: 18px;
    line-height: 1.75;
  }

  /* LANGKAH DISKUSI */
  .steps {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 0 0 30px;
    padding: 0;
    list-style: none;
  }

  .steps li {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 14px;
    border: 1px solid var(--line);
    border-radius: 14px;
    background: #fff;
    font-weight: 600;
    font-size: 14.5px;
  }

  .steps b {
    width: 24px;
    height: 24px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: var(--purple);
    color: #fff;
    font-size: 12.5px;
  }

  /* TOMBOL */
  .cta {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
  }

  .cta .btn {
    padding: 15px 28px;
    font-size: 16.5px;
    box-shadow: 0 12px 26px #7661a830;
  }

  .hint {
    margin-top: 16px;
    color: var(--muted);
    font-size: 14px;
  }

  .hint a {
    color: var(--purple);
    font-weight: 700;
  }

  /* SAPAAN MASKOT */
.mascot-greeting {
    position: absolute;
    top: 22px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
    width: 85%;
    text-align: center;
}

.mascot-greeting h3 {
    margin: 0;
    color: var(--purple-dark);
    font-size: 22px;
    font-weight: 800;
    line-height: 1.4;
}

.mascot-greeting p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

  /* CARD MASKOT */
  .mascot {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(145deg, #ffffff, #faf6ff);
    border: 1px solid #eee7f7;
    border-radius: 32px;
    padding: 25px;
    min-height: 430px;
    box-shadow: 0 18px 45px rgba(93, 73, 142, .10);
  }

  /* BACKGROUND BULAT DI BELAKANG MASKOT */
  .mascot .blob {
    position: absolute;
    width: 75%;
    aspect-ratio: 1;
    border-radius: 42% 58% 55% 45% / 48% 42% 58% 52%;
    background: linear-gradient(145deg, #efe6fb, #fde7ef);
  }

  /* GAMBAR MASKOT */
  .mascot img {
    position: relative;
    z-index: 1;
    width: min(500px, 100%);
    height: auto;
    filter: drop-shadow(0 18px 26px #5d498e22);
  }

  /* RESPONSIVE / MOBILE */
  @media (max-width: 860px) {
    .hero {
      padding: 36px 0 50px;
    }

    .hero-grid {
      grid-template-columns: 1fr;
      text-align: center;
    }

    .lead {
      margin-left: auto;
      margin-right: auto;
    }

    .steps,
    .cta {
      justify-content: center;
    }

    .mascot {
      order: -1;
      width: min(100%, 430px);
      min-height: 320px;
      margin: 0 auto;
      padding: 20px;
    }

    .mascot img {
      width: min(350px, 90%);
    }
  }

  /* MOBILE KECIL */
  @media (max-width: 480px) {
    .mascot {
      min-height: 270px;
      border-radius: 24px;
      padding: 16px;
    }

    .mascot img {
      width: 90%;
    }

    .hero h1 {
      font-size: 36px;
    }

    .lead {
      font-size: 16px;
    }

    .cta {
      flex-direction: column;
      align-items: stretch;
    }

    .cta .btn {
      width: 100%;
      text-align: center;
    }
  }
</style>
@endpush

@section('content')
<section class="hero">
  <div class="container hero-grid">

    <!-- BAGIAN TEKS -->
<div>
  <span class="kicker">
    <i class="fa-regular fa-comments"></i>
    Ruang berbagi dan bertukar pikiran
  </span>

  <h1>Diskusi <em>Bareng</em>, Belajar Lebih Asyik</h1>

  <p class="lead">
    Punya pertanyaan seputar perkuliahan atau ingin berbagi pengetahuan?
    Yuk, bergabung dalam ruang diskusi TemuEdu. Bertukar ide,
    berbagi pengalaman, dan saling membantu bersama teman-teman mahasiswa lainnya.
  </p>

  <ol class="steps">
    <li><b>1</b> Bebas bertanya</li>
    <li><b>2</b> Berbagi pengetahuan</li>
    <li><b>3</b> Belajar bersama</li>
  </ol>

  <div class="cta">
    <a href="https://t.me/+XNkY0L3AcFVkMzZl"
       target="_blank"
       rel="noopener noreferrer"
       class="btn">
      Gabung Ruang Diskusi
      <i class="fa-solid fa-arrow-right"></i>
    </a>

    <a href="{{ url('/') }}" class="btn btn-line">
      Kembali ke beranda
    </a>
  </div>

  @guest
    <p class="hint">
      Kamu perlu
      <a href="{{ route('login') }}">masuk</a>
      atau
      <a href="{{ route('register') }}">daftar</a>
      dulu untuk bergabung.
    </p>
  @endguest
</div>

    <!-- CARD MASKOT -->
    <div class="mascot">
      <div class="blob"></div>
      <div class="mascot-greeting">
  <h3>Yuk, jangan ragu untuk berbagi!</h3>
  <p>Setiap pertanyaan bisa jadi awal pengetahuan baru ✨</p>
</div>
      <img
        src="{{ asset('img/maskot2.png') }}"
        alt="Maskot TemuEdu"
      >
    </div>

  </div>
</section>
@endsection
