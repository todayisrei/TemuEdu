<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Temu Edu - platform konsultasi tugas dan materi perkuliahan bersama tutor dari Fakultas Ekonomi dan Bisnis.">
  <title>Temu Edu — Belajar Jadi Lebih Mudah</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root {
      --ink: #29283a;
      --muted: #77758a;
      --purple: #7661a8;
      --purple-dark: #5d498e;
      --pink: #f7dfe8;
      --pink-soft: #fff4f7;
      --cream: #fffaf3;
      --orange: #f28b45;
      --line: #eee8f2;
      --white: #ffffff;
    }
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { margin: 0; color: var(--ink); background: #fff; font-family: "DM Sans", sans-serif; }
    h1,h2,h3,h4,h5 { font-family: "Jost", sans-serif; font-weight: 700; }
    a { text-decoration: none; }
    /* NAVBAR dibuat stabil meskipun ada perbedaan versi Bootstrap */
    .navbar { position:sticky; top:0; z-index:1030; display:block; padding:14px 0; background:rgba(255,255,255,.97); border-bottom:1px solid #f3edf5; box-shadow:0 4px 20px rgba(60,42,80,.035); }
    .navbar > .container { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; }
    .brand { display:inline-flex; align-items:center; gap:10px; color:var(--ink); font:800 25px "Jost",sans-serif; white-space:nowrap; }
    .brand:hover { color:var(--ink); }
    .brand-logo {
  height:75px;
  width:auto;
  object-fit:contain;
  display:block;
}
    .navbar-toggler { display:none; border:1px solid #e8deef; border-radius:12px; padding:9px 11px; background:#fff; color:var(--purple-dark); box-shadow:none !important; }
    .navbar-toggler-icon { display:block; width:22px; height:16px; background:none !important; position:relative; border-top:2px solid currentColor; border-bottom:2px solid currentColor; }
    .navbar-toggler-icon::after { content:""; position:absolute; left:0; right:0; top:5px; border-top:2px solid currentColor; }
    .navbar-collapse { display:flex; flex:1 1 auto; align-items:center; justify-content:flex-end; min-width:0; }
    .navbar-nav { display:flex; flex-direction:row; gap:8px; align-items:center; margin:0; padding:0; list-style:none; }
    .navbar-nav .nav-item { list-style:none; }
    .navbar-nav .nav-link { display:block; color:#5e5b70; font-weight:600; padding:10px 13px !important; white-space:nowrap; border-radius:10px; }
    .navbar-nav .nav-link:hover, .navbar-nav .nav-link:focus { color:var(--purple); background:#faf6ff; }
    .navbar .btn-main { white-space:nowrap; }
    .btn-main { display:inline-flex; align-items:center; justify-content:center; gap:9px; padding:13px 22px; border-radius:14px; background:var(--purple); color:#fff; font-weight:700; border:0; transition:.2s; }
    .btn-main:hover { background:var(--purple-dark); color:#fff; transform:translateY(-2px); }
    .btn-lightline { display:inline-flex; align-items:center; gap:9px; padding:12px 20px; border-radius:14px; border:1px solid #e8deef; color:var(--purple-dark); background:#fff; font-weight:700; }
    .hero { padding:76px 0 70px; background:radial-gradient(circle at 85% 20%, #fceaf0 0, transparent 30%), linear-gradient(115deg,#fffaf5 0%,#fff 56%,#f8f3ff 100%); overflow:hidden; }
    .eyebrow { display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid #f1e7f1; color:var(--purple-dark); padding:8px 13px; border-radius:100px; font-size:13px; font-weight:700; }
    .eyebrow i { color:var(--orange); }
    .hero h1 { font-size:clamp(39px,5vw,62px); line-height:1.08; letter-spacing:-1.3px; margin:20px 0 18px; }
    .hero h1 span { color:var(--purple); }
    .hero p { color:var(--muted); font-size:17px; line-height:1.8; max-width:530px; margin-bottom:27px; }
    .hero-note { display:flex; align-items:center; gap:12px; margin-top:25px; color:var(--muted); font-size:13px; }
    .avatar-stack { display:flex; }
    .avatar-stack span { width:30px; height:30px; display:grid; place-items:center; border:2px solid #fff; border-radius:50%; margin-left:-7px; background:#f7dfe8; font-size:13px; }
    .avatar-stack span:first-child { margin-left:0; background:#e8e1fa; }
    .hero-art { min-height:410px; display:flex; justify-content:center; align-items:center; position:relative; }
    .art-blob { position:absolute; width:360px; height:340px; border-radius:48% 52% 59% 41% / 43% 42% 58% 57%; background:#f9e8ee; transform:rotate(-7deg); }
    .mascot-card { z-index:1; position:relative; width:min(100%,360px); padding:26px 25px 22px; background:#fff; border:1px solid #f2e9f1; border-radius:28px; box-shadow:0 22px 65px rgba(82,61,110,.12); text-align:center; }
    .mascot-card .mini-tag { display:inline-block; padding:7px 12px; border-radius:100px; background:#fff3e9; color:#bd6a2d; font-size:12px; font-weight:700; }
    .mascot-svg {
    display: block;
    width: 280px;
    max-width: 100%;
    height: auto;
    margin: 8px auto 2px;
    object-fit: contain;
}
    .mascot-card h3 { font-size:23px; margin:5px 0 4px; }
    .mascot-card p { font-size:13px; line-height:1.5; margin:0; color:var(--muted); }
    .float-chip { z-index:2; position:absolute; padding:12px 15px; border-radius:15px; background:#fff; box-shadow:0 12px 35px #4d3c6715; border:1px solid #f2e9f1; font-size:13px; font-weight:700; }
    .float-chip i { color:var(--orange); margin-right:6px; }
    .chip-one { top:45px; left:0; } .chip-two { bottom:45px; right:-3px; }
    .section { padding:78px 0; }
    .section-head { max-width:650px; margin:0 auto 38px; text-align:center; }
    .section-head .kicker { color:var(--purple); font-weight:800; text-transform:uppercase; font-size:12px; letter-spacing:1.6px; }
    .section-head h2 { font-size:clamp(30px,4vw,42px); margin:10px 0 12px; }
    .section-head p { color:var(--muted); line-height:1.75; margin:0; }
    .stats { padding:23px 0; border-top:1px solid var(--line); border-bottom:1px solid var(--line); }
    .stat { text-align:center; padding:8px 10px; }
    .stat strong { display:block; color:var(--purple); font:800 29px "Jost",sans-serif; }
    .stat span { color:var(--muted); font-size:13px; }
    .soft-bg { background:var(--pink-soft); }
    .feature-card,.study-card,.tutor-card { height:100%; border:1px solid var(--line); border-radius:22px; background:#fff; padding:25px; transition:.2s; }
    .feature-card:hover,.study-card:hover,.tutor-card:hover { transform:translateY(-5px); box-shadow:0 15px 35px #58436d12; }
    .icon-box { width:52px; height:52px; display:grid; place-items:center; border-radius:17px; background:#f1ebfb; color:var(--purple); font-size:21px; margin-bottom:18px; }
    .icon-box.peach { background:#fff0e4; color:#db8240; }
    .icon-box.pink { background:#ffedf3; color:#c76d91; }
    .feature-card h3 { font-size:21px; margin-bottom:9px; }
    .feature-card p { color:var(--muted); line-height:1.7; font-size:14px; margin:0; }
    .course-tag { display:inline-block; padding:6px 10px; border-radius:8px; background:#f6f0ff; color:var(--purple-dark); font-size:12px; font-weight:700; margin-bottom:14px; }
    .study-card h3 { font-size:21px; }
    .study-card p { color:var(--muted); font-size:14px; line-height:1.7; }
    .study-card a { color:var(--purple); font-weight:700; font-size:14px; }
    .tutor-avatar { display:grid; place-items:center; height:76px; width:76px; border-radius:25px; margin-bottom:17px; font-size:28px; color:var(--purple-dark); background:#f3eafa; }
    .tutor-avatar.orange { background:#fff0e4; color:#c97432; }
    .tutor-avatar.pink { background:#ffedf3; color:#c76d91; }
    .tutor-card h3 { font-size:20px; margin-bottom:4px; }
    .tutor-card .major { color:var(--purple); font-weight:700; font-size:13px; }
    .tutor-card p { color:var(--muted); font-size:13px; line-height:1.6; margin:12px 0 0; }
    .cta { background:linear-gradient(115deg,#7661a8,#8a75b9); color:#fff; border-radius:28px; padding:44px; position:relative; overflow:hidden; }
    .cta h2 { font-size:clamp(28px,4vw,39px); margin-bottom:12px; }
    .cta p { color:#f0eafa; line-height:1.7; margin:0; }
    .cta .btn-main { background:#fff; color:var(--purple-dark); white-space:nowrap; }
    .footer { padding:42px 0 24px; background:#29283a; color:#d7d3e2; }
    .footer .brand { color:#fff; }
    .footer p { color:#bcb7ce; font-size:13px; line-height:1.7; max-width:340px; margin-top:14px; }
    .footer-links { display:flex; flex-wrap:wrap; gap:18px; justify-content:flex-end; }
    .footer-links a { color:#d7d3e2; font-size:13px; }
    .footer-links a:hover { color:#fff; }
    .copyright { border-top:1px solid #454257; margin-top:28px; padding-top:18px; font-size:12px; color:#aaa5bf; }
    @media (max-width: 991px) {
      .navbar-toggler { display:block; margin-left:auto; }
      .navbar-collapse { display:none; flex:0 0 100%; width:100%; padding-top:12px; }
      .navbar-collapse.show { display:flex; flex-direction:column; align-items:stretch; }
      .navbar-nav { display:flex; flex-direction:column; align-items:stretch; gap:2px; width:100%; padding:0 0 8px; }
      .navbar-nav .nav-link { padding:11px 12px !important; }
      .navbar .btn-main { margin-top:5px; align-self:flex-start; }
      .hero { padding-top:50px; }
      .hero-art { margin-top:25px; }
      .chip-one { left:5%; } .chip-two { right:5%; }
      .footer-links { justify-content:flex-start; margin-top:22px; }
    }
@media (max-width: 575px) {
    .section { padding:58px 0; }
    .hero h1 { font-size:40px; }
    .hero-art { min-height:370px; }
    .art-blob { width:290px; height:290px; }
    .mascot-card { width:300px; padding:20px; }
    .mascot-svg { width:230px; }
    .float-chip { font-size:11px; padding:10px 11px; }
    .chip-one { top:10px; left:0; } .chip-two { bottom:13px; right:0; }
    .cta { padding:30px 24px; }
}

    /* ===== scroll offset karena navbar sticky ===== */
    section[id] { scroll-margin-top: 80px; }

    /* ===== ULASAN (carousel geser) ===== */
    .review-wrap { position:relative; }
    .review-track { display:flex; gap:22px; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; padding:6px 4px 26px; cursor:grab; scrollbar-width:none; -webkit-overflow-scrolling:touch; }
    .review-track::-webkit-scrollbar { display:none; }
    .review-track.dragging { cursor:grabbing; scroll-snap-type:none; scroll-behavior:auto; user-select:none; }
    .review-card { flex:0 0 calc((100% - 44px) / 3); scroll-snap-align:start; display:flex; flex-direction:column; background:#fff; border:1px solid var(--line); border-radius:24px; padding:28px 26px 24px; position:relative; transition:transform .25s, box-shadow .25s; }
    .review-card:hover { transform:translateY(-4px); box-shadow:0 16px 36px #58436d14; }
    .review-card .q-icon { position:absolute; top:22px; right:24px; font-size:34px; color:#f0e8fa; }
    .review-card p { color:#4a475c; line-height:1.75; font-size:15px; margin:0 0 22px; flex:1; position:relative; }
    .reviewer { display:flex; align-items:center; gap:13px; padding-top:18px; border-top:1px dashed #eadff0; }
    .reviewer .ava { flex:0 0 46px; width:46px; height:46px; display:grid; place-items:center; border-radius:16px; font:700 16px "Jost",sans-serif; background:#f1ebfb; color:var(--purple-dark); }
    .reviewer .ava.c1 { background:#fff0e4; color:#c97432; }
    .reviewer .ava.c2 { background:#ffedf3; color:#c76d91; }
    .reviewer strong { display:block; font:700 15px "Jost",sans-serif; line-height:1.3; }
    .reviewer span.prodi { display:inline-block; margin-top:3px; padding:3px 9px; border-radius:7px; background:#f6f0ff; color:var(--purple-dark); font-size:11.5px; font-weight:700; }
    .review-ctrl { display:flex; align-items:center; justify-content:center; gap:18px; margin-top:4px; }
    .rv-btn { width:46px; height:46px; display:grid; place-items:center; border-radius:50%; border:1px solid #e8deef; background:#fff; color:var(--purple-dark); transition:.2s; cursor:pointer; }
    .rv-btn:hover:not(:disabled) { background:var(--purple); color:#fff; border-color:var(--purple); }
    .rv-btn:disabled { opacity:.4; cursor:default; }
    .rv-dots { display:flex; gap:8px; align-items:center; }
    .rv-dots button { width:9px; height:9px; padding:0; border:0; border-radius:50%; background:#dccfec; cursor:pointer; transition:.25s; }
    .rv-dots button.active { width:26px; border-radius:10px; background:var(--purple); }

    /* ===== FAQ (accordion) ===== */
    .faq-list { max-width:820px; margin:0 auto; display:flex; flex-direction:column; gap:14px; }
    .faq-item { background:#fff; border:1px solid var(--line); border-radius:20px; transition:border-color .25s, box-shadow .25s; overflow:hidden; }
    .faq-item.open { border-color:#d9c9ee; box-shadow:0 12px 30px #58436d12; }
    .faq-q { width:100%; display:flex; align-items:center; gap:16px; padding:20px 24px; background:none; border:0; text-align:left; color:var(--ink); font:700 18px "Jost",sans-serif; cursor:pointer; }
    .faq-q .num { flex:0 0 34px; height:34px; display:grid; place-items:center; border-radius:11px; background:#f1ebfb; color:var(--purple); font-size:14px; transition:.25s; }
    .faq-q .txt { flex:1; }
    .faq-q .chev { flex:0 0 32px; height:32px; display:grid; place-items:center; border-radius:50%; background:#faf6ff; color:var(--purple); font-size:13px; transition:transform .3s, background .25s, color .25s; }
    .faq-item.open .faq-q .num { background:var(--purple); color:#fff; }
    .faq-item.open .faq-q .chev { transform:rotate(180deg); background:var(--purple); color:#fff; }
    .faq-a { display:grid; grid-template-rows:0fr; transition:grid-template-rows .35s ease; }
    .faq-item.open .faq-a { grid-template-rows:1fr; }
    .faq-a > div { overflow:hidden; }
    .faq-a .inner { padding:0 24px 22px 74px; color:var(--muted); line-height:1.8; font-size:15px; }
    .faq-a .inner p { margin:0 0 10px; }
    .faq-a .inner p:last-child { margin:0; }

    /* link kecil di kartu tutor */
    .tutor-card .tlink { display:inline-flex; align-items:center; gap:7px; margin-top:14px; color:var(--purple); font-weight:700; font-size:14px; }
    .tutor-card .tlink:hover { color:var(--purple-dark); }

    @media (max-width: 991px) {
      .review-card { flex-basis:calc((100% - 22px) / 2); }
    }
    @media (max-width: 640px) {
      .review-card { flex-basis:86%; }
      .faq-q { font-size:16px; padding:17px 16px; gap:12px; }
      .faq-a .inner { padding:0 18px 20px 18px; }
    }
    button.btn-lightline { cursor:pointer; font:inherit; font-weight:700; }
  </style>
</head>
<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
      <a class="brand" href="#home" aria-label="Temu Edu beranda">
  <img src="{{ asset('img/logo-temuedu.png') }}" alt="Temu Edu" class="brand-logo">
</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka navigasi">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto me-lg-3">
          <li class="nav-item"><a class="nav-link" href="#home">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
          <li class="nav-item"><a class="nav-link" href="#bidang">Bidang Belajar</a></li>
          <li class="nav-item"><a class="nav-link" href="#tutor">Tutor</a></li>
          <li class="nav-item"><a class="nav-link" href="#ulasan">Ulasan</a></li>
          <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
        </ul>
        <div class="d-flex align-items-center flex-wrap gap-2">
          @auth
            @include('partials.notification-bell')
            @if (auth()->user()->role === 'user')
              <a class="btn-lightline" href="{{ route('profile.show') }}"><i class="fa-regular fa-user"></i> Profil</a>
            @endif
            @if (auth()->user()->role === 'admin')
              <a class="btn-lightline" href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
            @elseif (auth()->user()->role === 'konsultan')
              <a class="btn-lightline" href="{{ route('consultant.dashboard') }}">Dashboard Konsultan</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="m-0">
              @csrf
              <button type="submit" class="btn-lightline">Keluar</button>
            </form>
          @else
            <a class="btn-lightline" href="{{ route('login') }}">Masuk</a>
          @endauth
          <a class="btn-main" href="{{ route('consultation.prodi') }}">
    Mulai Konsultasi
    <i class="fa-solid fa-arrow-right"></i>
</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero" id="home">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <span class="eyebrow"><i class="fa-solid fa-sparkles"></i> Teman belajar mahasiswa</span>
          <h1>Tugas kuliah terasa lebih mudah, <span>bareng ahlinya.</span></h1>
          <p>Temukan tutor untuk berdiskusi, memahami materi, dan mencari sudut pandang baru seputar perkuliahan. Belajar jadi lebih terarah tanpa harus merasa sendirian.</p>
          <div class="d-flex flex-wrap gap-3">
            <a class="btn-main" href="#bidang">Lihat Bidang <i class="fa-solid fa-arrow-right"></i></a>
            <a class="btn-lightline" href="#layanan"><i class="fa-regular fa-circle-play"></i> Kenali Layanan</a>
          </div>
          <div class="hero-note">
            <div class="avatar-stack"><span><i class="fa-solid fa-user-graduate"></i></span><span><i class="fa-solid fa-book"></i></span><span><i class="fa-solid fa-lightbulb"></i></span></div>
            <span>Ruang diskusi yang suportif untuk perjalanan kuliahmu.</span>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="hero-art">
            <div class="art-blob"></div>
            <div class="float-chip chip-one"><i class="fa-solid fa-comments"></i> Diskusi interaktif</div>
            <div class="float-chip chip-two"><i class="fa-solid fa-heart"></i> Belajar tanpa takut</div>
            <div class="mascot-card">
              <span class="mini-tag"><i class="fa-solid fa-star"></i> Kenalan dengan Kiko!</span>
              <!-- Maskot rubah dibuat langsung dengan SVG agar tidak bergantung pada file gambar eksternal -->
              <!-- MASKOT TEMUEDU -->
<img
    src="{{ asset('img/temuedu-mascot.png') }}"
    alt="Maskot TemuEdu"
    class="mascot-svg">
              <h3>Lumo si teman belajar!</h3>
              <p>Siap nemenin kamu memahami materi satu langkah demi satu langkah.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- QUICK HIGHLIGHTS -->
  <section class="stats">
    <div class="container">
      <div class="row g-3">
        <div class="col-6 col-lg-3"><div class="stat"><strong>5</strong><span>Bidang studi FEB</span></div></div>
        <div class="col-6 col-lg-3"><div class="stat"><strong>1-on-1</strong><span>Diskusi lebih fokus</span></div></div>
        <div class="col-6 col-lg-3"><div class="stat"><strong>Fleksibel</strong><span>Sesuaikan kebutuhanmu</span></div></div>
        <div class="col-6 col-lg-3"><div class="stat"><strong>Supportif</strong><span>Ruang bertanya yang nyaman</span></div></div>
      </div>
    </div>
  </section>

  <!-- SERVICES -->
  <section class="section" id="layanan">
    <div class="container">
      <div class="section-head">
        <div class="kicker">Kenapa Temu Edu?</div>
        <h2>Bukan cuma cari jawaban, tapi memahami.</h2>
        <p>Kamu bisa berdiskusi sesuai kebutuhan belajar. Fokusnya membantu memahami konsep dan mengembangkan cara berpikir, bukan sekadar menyalin jawaban.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-4"><div class="feature-card"><div class="icon-box"><i class="fa-solid fa-book-open"></i></div><h3>Bedah Materi</h3><p>Bahas materi yang masih membingungkan dengan penjelasan yang lebih sederhana dan runtut.</p></div></div>
        <div class="col-md-4"><div class="feature-card"><div class="icon-box peach"><i class="fa-solid fa-pen-to-square"></i></div><h3>Diskusi Tugas</h3><p>Susun ide, pahami instruksi, dan diskusikan langkah pengerjaan tugas bersama tutor.</p></div></div>
        <div class="col-md-4"><div class="feature-card"><div class="icon-box pink"><i class="fa-solid fa-compass"></i></div><h3>Arah Belajar</h3><p>Kenali bagian yang perlu dipelajari lagi dan buat proses belajar terasa lebih terarah.</p></div></div>
      </div>
    </div>
  </section>

  <!-- STUDY FIELDS -->
  <section class="section soft-bg" id="bidang">
    <div class="container">
      <div class="section-head">
        <div class="kicker">Pilih bidangmu</div>
        <h2>Temukan teman diskusi sesuai jurusan.</h2>
        <p>Platform ini berfokus pada bidang Fakultas Ekonomi dan Bisnis (FEB). Pilih topik yang sedang kamu pelajari untuk menemukan tutor yang relevan.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4"><div class="study-card"><span class="course-tag">Akuntansi</span><h3>Akuntansi</h3><p>Diskusi dasar akuntansi, pencatatan transaksi, laporan keuangan, dan topik akuntansi lainnya.</p><a href="{{ route('discussion.intro') }}">Cari teman diskusi <i class="fa-solid fa-arrow-right ms-1"></i></a></div></div>
        <div class="col-md-6 col-lg-4"><div class="study-card"><span class="course-tag">Manajemen</span><h3>Manajemen</h3><p>Bahas pemasaran, keuangan, sumber daya manusia, operasional, dan pengantar manajemen.</p><a href="{{ route('discussion.intro') }}">Cari teman diskusi <i class="fa-solid fa-arrow-right ms-1"></i></a></div></div>
        <div class="col-md-6 col-lg-4"><div class="study-card"><span class="course-tag">Bisnis Digital</span><h3>Bisnis Digital</h3><p>Diskusi strategi bisnis digital, pemasaran online, model bisnis, dan pengembangan ide.</p><a href="{{ route('discussion.intro') }}">Cari teman diskusi <i class="fa-solid fa-arrow-right ms-1"></i></a></div></div>
        <div class="col-md-6 col-lg-6"><div class="study-card"><span class="course-tag">Ilmu Ekonomi</span><h3>Ilmu Ekonomi</h3><p>Pahami konsep mikroekonomi, makroekonomi, kebijakan ekonomi, serta analisis masalah ekonomi.</p><a href="{{ route('discussion.intro') }}">Cari teman diskusi <i class="fa-solid fa-arrow-right ms-1"></i></a></div></div>
        <div class="col-md-6 col-lg-6"><div class="study-card"><span class="course-tag">Ekonomi Syariah</span><h3>Ekonomi Islam / Syariah</h3><p>Diskusikan konsep ekonomi Islam, prinsip transaksi syariah, dan pengantar keuangan syariah.</p><a href="{{ route('discussion.intro') }}">Cari teman diskusi <i class="fa-solid fa-arrow-right ms-1"></i></a></div></div>
      </div>
    </div>
  </section>

  <!-- TUTORS -->
  <section class="section" id="tutor">
    <div class="container">
      <div class="section-head">
        <div class="kicker">Tutor pilihan</div>
        <h2>Belajar bareng yang sudah memahami bidangnya.</h2>
        <p>Profil tutor nantinya dapat dilengkapi dengan informasi lulusan, IPK, portofolio, dan LinkedIn agar mahasiswa bisa memilih dengan lebih yakin.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4"><div class="tutor-card"><div class="tutor-avatar"><i class="fa-solid fa-calculator"></i></div><h3>Tutor Akuntansi</h3><div class="major">Fokus: Akuntansi & laporan keuangan</div><p>Profil, IPK, portofolio, dan tautan LinkedIn tutor akan ditampilkan di bagian ini.</p><a class="tlink" href="{{ route('consultation.consultants', 1) }}">Lihat konsultan <i class="fa-solid fa-arrow-right"></i></a></div></div>
        <div class="col-md-6 col-lg-4"><div class="tutor-card"><div class="tutor-avatar orange"><i class="fa-solid fa-chart-line"></i></div><h3>Tutor Manajemen</h3><div class="major">Fokus: Bisnis & manajemen</div><p>Profil, IPK, portofolio, dan tautan LinkedIn tutor akan ditampilkan di bagian ini.</p><a class="tlink" href="{{ route('consultation.consultants', 2) }}">Lihat konsultan <i class="fa-solid fa-arrow-right"></i></a></div></div>
        <div class="col-md-6 col-lg-4"><div class="tutor-card"><div class="tutor-avatar pink"><i class="fa-solid fa-laptop"></i></div><h3>Tutor Bisnis Digital</h3><div class="major">Fokus: Digital & pemasaran</div><p>Profil, IPK, portofolio, dan tautan LinkedIn tutor akan ditampilkan di bagian ini.</p><a class="tlink" href="{{ route('consultation.consultants', 3) }}">Lihat konsultan <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </div>
      <div class="text-center mt-5">
        <a class="btn-main" href="{{ route('consultation.prodi') }}">Lihat Semua Konsultan <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- ULASAN -->
  @php
    $reviews = [
      ['Fajar Ramadhan', 'Ekonomi Islam', 'TemuEdu bisa jadi solusi buat aku ketika kesulitan memahami materi kuliah. Dengan adanya konsultasi langsung, aku bisa bertanya lebih detail sampai benar-benar paham.'],
      ['Nadia Melviana', 'Manajemen', 'Menurut aku, TemuEdu menarik karena mahasiswa bisa memilih cara konsultasi sesuai kebutuhan, baik lewat chat maupun video call. Jadi, lebih fleksibel buat menyesuaikan jadwal kuliah.'],
      ['Andika Pratama', 'Manajemen', 'Kadang sudah cari penjelasan di Google atau YouTube, tetapi masih belum paham. Adanya platform seperti TemuEdu bisa membantu mahasiswa mendapatkan penjelasan yang lebih terarah.'],
      ['Salsa Aulia Putri', 'Akuntansi', 'TemuEdu punya konsep yang bermanfaat karena mahasiswa bisa mencari konsultan sesuai bidang yang sedang dipelajari. Jadi, kita bisa lebih fokus memahami materi dan menyelesaikan tugas secara mandiri.'],
      ['Farhan Maulana', 'Akuntansi', 'Menurut aku, TemuEdu membantu mahasiswa yang masih ragu buat bertanya di kelas. Dengan konsultasi, kita bisa bertanya lebih bebas dan memahami materi dengan lebih baik.'],
      ['Rahma Dwi Lestari', 'Bisnis Digital', 'Konsep TemuEdu menarik karena kita bisa mencari bantuan sesuai mata kuliah yang sedang sulit dipahami. Cocok buat mahasiswa yang butuh penjelasan tambahan di luar jam kuliah.'],
      ['Vino Adityo Saputra', 'Bisnis Digital', 'Aku merasa platform seperti TemuEdu bisa membantu saat mengerjakan tugas yang cukup rumit. Kita bisa berdiskusi langsung dan mendapatkan arahan tanpa harus bergantung sepenuhnya pada jawaban dari internet.'],
      ['Putri Amelia Safitri', 'Ilmu Ekonomi', 'Fitur konsultasi online menurut aku praktis karena mahasiswa bisa berdiskusi tanpa harus datang langsung. Semoga ke depannya pilihan konsultasinya juga beragam dan jadwalnya mudah disesuaikan.'],
      ['Imam Fadhilah', 'Ilmu Ekonomi', 'TemuEdu punya ide yang bagus karena setiap mahasiswa punya kebutuhan belajar yang berbeda. Dengan adanya konsultan sesuai bidangnya, proses memahami materi bisa terasa lebih terarah.'],
      ['Laila Nur Azizah', 'Manajemen', 'Platform ini bisa menjadi alternatif ketika sudah mencoba belajar sendiri tetapi masih belum memahami materi. Menurut aku, konsultasi langsung bisa membantu menemukan bagian yang belum kita pahami.'],
    ];
  @endphp
  <section class="section soft-bg" id="ulasan">
    <div class="container">
      <div class="section-head">
        <div class="kicker">Ulasan pengguna</div>
        <h2>Kata mereka tentang TemuEdu.</h2>
        <p>Geser untuk membaca pengalaman dan harapan mahasiswa FEB terhadap konsultasi di TemuEdu.</p>
      </div>
      <div class="review-wrap">
        <div class="review-track" id="reviewTrack" tabindex="0" aria-label="Daftar ulasan pengguna">
          @foreach ($reviews as $i => $r)
            @php
              $parts = preg_split('/\s+/', trim($r[0]));
              $initial = strtoupper(mb_substr($parts[0], 0, 1) . (isset($parts[1]) ? mb_substr($parts[1], 0, 1) : ''));
            @endphp
            <article class="review-card">
              <i class="fa-solid fa-quote-right q-icon"></i>
              <p>&ldquo;{{ $r[2] }}&rdquo;</p>
              <div class="reviewer">
                <div class="ava c{{ $i % 3 }}">{{ $initial }}</div>
                <div>
                  <strong>{{ $r[0] }}</strong>
                  <span class="prodi">{{ $r[1] }}</span>
                </div>
              </div>
            </article>
          @endforeach
        </div>
        <div class="review-ctrl">
          <button class="rv-btn" id="rvPrev" type="button" aria-label="Ulasan sebelumnya"><i class="fa-solid fa-arrow-left"></i></button>
          <div class="rv-dots" id="rvDots"></div>
          <button class="rv-btn" id="rvNext" type="button" aria-label="Ulasan berikutnya"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  @php
    $faqs = [
      ['Apa itu TemuEdu?', ['TemuEdu merupakan platform konsultasi akademik yang membantu mahasiswa memahami materi perkuliahan, berdiskusi mengenai tugas, dan menemukan solusi atas kesulitan akademik bersama tutor yang sesuai dengan bidang keahliannya.']],
      ['Layanan apa saja yang tersedia di TemuEdu?', ['TemuEdu menyediakan layanan konsultasi tugas dan materi perkuliahan melalui tiga metode, yaitu chat, online meeting, dan offline meeting.', 'Selain itu, TemuEdu juga memiliki fitur forum diskusi yang memungkinkan mahasiswa untuk bertukar informasi, berbagi pendapat, dan berdiskusi mengenai materi maupun permasalahan akademik bersama mahasiswa lainnya.']],
      ['Bagaimana cara melakukan konsultasi di TemuEdu?', ['Mahasiswa dapat memulai konsultasi dengan memilih fitur yang tersedia sesuai kebutuhan. Selanjutnya, mahasiswa memilih program studi yang sesuai dengan bidang yang ingin dikonsultasikan, kemudian memilih konsultan yang tersedia dan sesuai dengan kebutuhannya.', 'Setelah itu, mahasiswa dapat memilih jadwal konsultasi dan melakukan konfirmasi pemesanan.']],
      ['Apakah TemuEdu mengerjakan tugas mahasiswa secara langsung?', ['TemuEdu berfokus pada konsultasi dan pendampingan akademik. Tutor membantu mahasiswa memahami soal, menyusun langkah pengerjaan, dan menemukan solusi, sehingga mahasiswa dapat mengerjakan tugasnya dengan lebih mandiri dan memahami materi yang dipelajari.']],
      ['Siapa saja yang bisa menggunakan TemuEdu?', ['TemuEdu ditujukan bagi mahasiswa yang membutuhkan bantuan untuk memahami materi kuliah, berdiskusi tentang tugas, atau memperoleh arahan akademik. Baik mahasiswa yang sedang menghadapi tugas harian maupun proyek perkuliahan dapat memanfaatkan layanan sesuai kebutuhannya.', 'Untuk saat ini, TemuEdu berfokus pada mahasiswa Fakultas Ekonomi dan Bisnis (FEB), khususnya program studi Akuntansi, Manajemen, Bisnis Digital, Ilmu Ekonomi, serta Ekonomi Islam/Ekonomi Syariah.']],
    ];
  @endphp
  <section class="section" id="faq">
    <div class="container">
      <div class="section-head">
        <div class="kicker">FAQ</div>
        <h2>Pertanyaan yang sering ditanyakan.</h2>
        <p>Klik pertanyaan untuk melihat jawabannya.</p>
      </div>
      <div class="faq-list" id="faqList">
        @foreach ($faqs as $i => $f)
          <div class="faq-item {{ $i === 0 ? 'open' : '' }}">
            <button class="faq-q" type="button" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="faq-a-{{ $i }}" id="faq-q-{{ $i }}">
              <span class="num">{{ $i + 1 }}</span>
              <span class="txt">{{ $f[0] }}</span>
              <span class="chev"><i class="fa-solid fa-chevron-down"></i></span>
            </button>
            <div class="faq-a" id="faq-a-{{ $i }}" role="region" aria-labelledby="faq-q-{{ $i }}">
              <div><div class="inner">
                @foreach ($f[1] as $para)
                  <p>{{ $para }}</p>
                @endforeach
              </div></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section pt-0" id="mulai">
    <div class="container">
      <div class="cta d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
        <div><h2>Yuk, mulai diskusi pertamamu!</h2><p>Ceritakan materi atau tugas yang ingin kamu pahami. Belajar pelan-pelan juga tetap sebuah kemajuan.</p></div>
        <a class="btn-main" href="{{ route('consultation.prodi') }}">Mulai Konsultasi <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="row align-items-start">
        <div class="col-lg-6">
          <a class="brand" href="#home"><span class="brand-mark"><i class="fa-solid fa-book-open-reader"></i></span><span>temu<span style="color:#c6b6f0">edu.</span></span></a>
          <p>Teman diskusi untuk membantu mahasiswa memahami materi dan mengerjakan tugas dengan lebih terarah. Dibuat untuk mendukung proses belajar yang nyaman dan kolaboratif.</p>
        </div>
        <div class="col-lg-6">
          <div class="footer-links">
            <a href="#home">Beranda</a><a href="#layanan">Layanan</a><a href="#bidang">Bidang Belajar</a><a href="#tutor">Tutor</a><a href="#ulasan">Ulasan</a><a href="#faq">FAQ</a>
          </div>
        </div>
      </div>
      <div class="copyright d-flex flex-column flex-md-row justify-content-between gap-2">
        <span>© 2026 Temu Edu. Dibuat untuk kebutuhan proyek website perkuliahan.</span>
        <span>Belajar bareng, berkembang bareng ♡</span>
      </div>
    </div>
  </footer>

  <script>
  (function () {
    // ---- Navbar (mobile) ----
    var toggler = document.querySelector('.navbar-toggler');
    var menu = document.getElementById('mainNav');
    if (toggler && menu) {
      toggler.addEventListener('click', function () {
        var open = menu.classList.toggle('show');
        toggler.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      menu.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () { menu.classList.remove('show'); });
      });
    }

    // ---- FAQ accordion (satu terbuka dalam satu waktu) ----
    var items = document.querySelectorAll('#faqList .faq-item');
    items.forEach(function (item) {
      item.querySelector('.faq-q').addEventListener('click', function () {
        var willOpen = !item.classList.contains('open');
        items.forEach(function (o) {
          o.classList.remove('open');
          o.querySelector('.faq-q').setAttribute('aria-expanded', 'false');
        });
        if (willOpen) {
          item.classList.add('open');
          item.querySelector('.faq-q').setAttribute('aria-expanded', 'true');
        }
      });
    });

    // ---- Carousel ulasan ----
    var track = document.getElementById('reviewTrack');
    if (!track) return;
    var cards = track.querySelectorAll('.review-card');
    var prev = document.getElementById('rvPrev');
    var next = document.getElementById('rvNext');
    var dotsBox = document.getElementById('rvDots');
    var dots = [];

    function step() {
      return cards.length > 1 ? cards[1].offsetLeft - cards[0].offsetLeft : track.clientWidth;
    }
    function perView() { return Math.max(1, Math.round(track.clientWidth / step())); }
    function maxIndex() { return Math.max(0, cards.length - perView()); }
    function current() { return Math.min(maxIndex(), Math.round(track.scrollLeft / step())); }

    function buildDots() {
      dotsBox.innerHTML = '';
      dots = [];
      for (var i = 0; i <= maxIndex(); i++) {
        (function (idx) {
          var b = document.createElement('button');
          b.type = 'button';
          b.setAttribute('aria-label', 'Ke ulasan ' + (idx + 1));
          b.addEventListener('click', function () { goTo(idx); });
          dotsBox.appendChild(b);
          dots.push(b);
        })(i);
      }
      update();
    }
    function update() {
      var c = current();
      dots.forEach(function (d, i) { d.classList.toggle('active', i === c); });
      prev.disabled = track.scrollLeft <= 2;
      next.disabled = track.scrollLeft >= track.scrollWidth - track.clientWidth - 2;
    }
    function goTo(i) {
      track.scrollTo({ left: Math.max(0, Math.min(i, maxIndex())) * step(), behavior: 'smooth' });
    }
    prev.addEventListener('click', function () { goTo(current() - 1); });
    next.addEventListener('click', function () { goTo(current() + 1); });
    track.addEventListener('scroll', function () { window.requestAnimationFrame(update); });
    track.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { e.preventDefault(); goTo(current() + 1); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); goTo(current() - 1); }
    });
    window.addEventListener('resize', buildDots);

    // Drag dengan mouse (sentuhan di HP sudah otomatis bisa digeser)
    var down = false, startX = 0, startLeft = 0, moved = false;
    track.addEventListener('pointerdown', function (e) {
      if (e.pointerType !== 'mouse') return;
      down = true; moved = false; startX = e.clientX; startLeft = track.scrollLeft;
      track.classList.add('dragging');
    });
    window.addEventListener('pointermove', function (e) {
      if (!down) return;
      var dx = e.clientX - startX;
      if (Math.abs(dx) > 4) moved = true;
      track.scrollLeft = startLeft - dx;
    });
    window.addEventListener('pointerup', function () {
      if (!down) return;
      down = false;
      track.classList.remove('dragging');
      goTo(Math.round(track.scrollLeft / step()));
    });
    track.addEventListener('dragstart', function (e) { e.preventDefault(); });

    buildDots();
  })();
  </script>
</body>
</html>