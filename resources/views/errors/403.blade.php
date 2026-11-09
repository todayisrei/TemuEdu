<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Akses ditolak | TemuEdu</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&family=Jost:wght@700;800&display=swap" rel="stylesheet">
  <style>
    body { margin:0; min-height:100vh; display:grid; place-items:center; background:#f7f4fb; font-family:"DM Sans",sans-serif; color:#29283a; }
    .box { max-width:440px; margin:20px; padding:42px 34px; text-align:center; background:#fff; border:1px solid #ece6f2; border-radius:26px; }
    .code { font:800 72px "Jost",sans-serif; color:#7661a8; line-height:1; }
    h1 { font:700 24px "Jost",sans-serif; margin:12px 0 8px; }
    p { color:#77758a; line-height:1.7; margin:0 0 24px; }
    a { display:inline-block; padding:12px 22px; border-radius:13px; background:#7661a8; color:#fff; font-weight:700; text-decoration:none; }
  </style>
</head>
<body>
  <div class="box">
    <div class="code">403</div>
    <h1>Akses ditolak</h1>
    <p>{{ $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}</p>
    <a href="{{ url('/') }}">Kembali ke beranda</a>
  </div>
</body>
</html>
