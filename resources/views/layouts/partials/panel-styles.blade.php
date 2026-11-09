    :root { --ink:#29283a; --muted:#77758a; --purple:#7661a8; --purple-dark:#5d498e; --purple-soft:#f1ebfb; --bg:#f7f4fb; --line:#ece6f2; --green:#2e9b69; --red:#d6455d; --orange:#f28b45; --side:#29283a; }
    * { box-sizing:border-box; }
    body { margin:0; background:var(--bg); color:var(--ink); font-family:"DM Sans",sans-serif; font-size:15px; }
    a { color:inherit; text-decoration:none; }
    h1,h2,h3 { font-family:"Jost",sans-serif; margin:0; }

    /* ---------- kerangka ---------- */
    .side { position:fixed; inset:0 auto 0 0; width:250px; background:var(--side); color:#d7d3e2; display:flex; flex-direction:column; z-index:50; transition:transform .25s; }
    .side-brand { padding:22px 22px 18px; font:800 24px "Jost",sans-serif; color:#fff; }
    .side-brand em { font-style:normal; color:#c6b6f0; }
    .side-brand small { display:block; margin-top:2px; font:600 11px "DM Sans",sans-serif; letter-spacing:.12em; text-transform:uppercase; color:#8f8aa6; }
    .side-nav { flex:1; padding:8px 14px; overflow-y:auto; }
    .side-nav .label { margin:16px 10px 8px; font-size:11px; letter-spacing:.1em; text-transform:uppercase; color:#8f8aa6; font-weight:700; }
    .side-nav a { display:flex; align-items:center; gap:12px; padding:11px 12px; margin-bottom:3px; border-radius:12px; color:#cfcade; font-weight:600; transition:.2s; }
    .side-nav a i { width:20px; text-align:center; font-size:15px; }
    .side-nav a:hover { background:#38364b; color:#fff; }
    .side-nav a.active { background:var(--purple); color:#fff; }
    .side-foot { padding:16px 18px; border-top:1px solid #3d3b50; font-size:13px; }
    .side-foot .who { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
    .side-foot .who b { display:block; color:#fff; font-size:14px; }
    .side-foot .who span { color:#8f8aa6; font-size:12px; }
    .side-foot button { width:100%; padding:10px; border:1px solid #4b4960; border-radius:11px; background:transparent; color:#d7d3e2; font:600 14px "DM Sans",sans-serif; cursor:pointer; }
    .side-foot button:hover { background:#38364b; }
    .backdrop { display:none; position:fixed; inset:0; background:#1d1b2a99; z-index:40; }

    .main { margin-left:250px; min-height:100vh; }
    .top { position:sticky; top:0; z-index:30; display:flex; align-items:center; gap:14px; padding:16px 32px; background:#ffffffee; backdrop-filter:blur(6px); border-bottom:1px solid var(--line); }
    .top h1 { font-size:24px; flex:1; }
    .menu-btn { display:none; width:42px; height:42px; border:1px solid var(--line); border-radius:12px; background:#fff; color:var(--purple-dark); cursor:pointer; }
    .page { padding:28px 32px 50px; max-width:1200px; }

    /* ---------- komponen ---------- */
    .card { background:#fff; border:1px solid var(--line); border-radius:20px; padding:24px; }
    .card + .card { margin-top:22px; }
    .card-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
    .card-head h2 { font-size:19px; }
    .muted { color:var(--muted); }
    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:22px; }
    .grid-2 .card + .card { margin-top:0; }
    .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:22px; }
    .stat { display:flex; align-items:center; gap:16px; background:#fff; border:1px solid var(--line); border-radius:20px; padding:20px; }
    .stat .ico { flex:0 0 52px; width:52px; height:52px; display:grid; place-items:center; border-radius:16px; background:var(--purple-soft); color:var(--purple); font-size:21px; }
    .stat .ico.o { background:#fff0e4; color:var(--orange); } .stat .ico.g { background:#e5f6ee; color:var(--green); } .stat .ico.p { background:#ffedf3; color:#c76d91; }
    .stat b { display:block; font:800 30px "Jost",sans-serif; line-height:1.1; }
    .stat span { color:var(--muted); font-size:13px; }

    .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:11px 18px; border-radius:12px; border:1px solid transparent; background:var(--purple); color:#fff; font:700 14px "DM Sans",sans-serif; cursor:pointer; transition:.2s; }
    .btn:hover { background:var(--purple-dark); }
    .btn-ghost { background:#fff; color:var(--purple-dark); border-color:#e3d9ee; } .btn-ghost:hover { background:#faf6ff; }
    .btn-danger { background:#fff; color:var(--red); border-color:#f3cdd5; } .btn-danger:hover { background:#fff3f5; }
    .btn-sm { padding:7px 12px; font-size:13px; border-radius:10px; }
    .actions { display:flex; gap:6px; flex-wrap:wrap; align-items:center; } .actions form { margin:0; }

    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; }
    th { text-align:left; padding:11px 14px; font-size:12px; letter-spacing:.06em; text-transform:uppercase; color:var(--muted); border-bottom:1px solid var(--line); white-space:nowrap; }
    td { padding:14px; border-bottom:1px solid #f4eff8; vertical-align:middle; }
    tr:last-child td { border-bottom:0; }
    .person { display:flex; align-items:center; gap:12px; min-width:210px; }
    .person b { display:block; } .person small { color:var(--muted); }
    .ava { flex:0 0 42px; width:42px; height:42px; border-radius:14px; overflow:hidden; display:grid; place-items:center; background:var(--purple-soft); color:var(--purple-dark); font:700 15px "Jost",sans-serif; }
    .ava img { width:100%; height:100%; object-fit:cover; }
    .badge { display:inline-block; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:700; background:var(--purple-soft); color:var(--purple-dark); }
    .badge.ok { background:#e5f6ee; color:var(--green); } .badge.off { background:#f1f0f4; color:#85829a; }

    .toolbar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px; }
    .toolbar input, .toolbar select { padding:10px 14px; border:1px solid #e3d9ee; border-radius:12px; background:#fff; font:inherit; color:var(--ink); }
    .toolbar input[type=text] { flex:1; min-width:200px; }

    /* ---------- form ---------- */
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px 22px; }
    .full { grid-column:1 / -1; }
    .field label { display:block; margin-bottom:7px; font-weight:700; font-size:14px; }
    .field label small { font-weight:500; color:var(--muted); }
    .field input[type=text], .field input[type=email], .field input[type=password], .field input[type=number], .field input[type=url], .field input[type=file], .field select, .field textarea { width:100%; padding:12px 14px; border:1px solid #e3d9ee; border-radius:12px; background:#fff; font:inherit; color:var(--ink); }
    .field textarea { min-height:110px; resize:vertical; }
    .field input:focus, .field select:focus, .field textarea:focus, .toolbar input:focus, .toolbar select:focus { outline:2px solid #d9c9ee; border-color:var(--purple); }
    .field .hint { margin-top:6px; font-size:12.5px; color:var(--muted); }
    .field .err { margin-top:6px; font-size:13px; color:var(--red); font-weight:600; }
    .input-row { display:flex; gap:8px; } .input-row input { flex:1; }
    .check { display:flex; align-items:center; gap:10px; font-weight:600; }
    .check input { width:18px; height:18px; accent-color:var(--purple); }
    .foto-box { display:flex; align-items:center; gap:16px; }
    .foto-box .preview { flex:0 0 84px; width:84px; height:84px; border-radius:22px; background:var(--purple-soft); color:var(--purple); display:grid; place-items:center; font-size:28px; overflow:hidden; }
    .foto-box .preview img { width:100%; height:100%; object-fit:cover; }
    .form-actions { display:flex; gap:10px; margin-top:26px; }
    .section-title { margin:6px 0 2px; font:700 16px "Jost",sans-serif; color:var(--purple-dark); }

    .alert { display:flex; gap:12px; align-items:flex-start; padding:14px 18px; margin-bottom:20px; border-radius:14px; font-weight:600; }
    .alert.success { background:#e5f6ee; color:#1f7a50; } .alert.error { background:#fff0f2; color:#b43048; }
    .alert ul { margin:4px 0 0; padding-left:18px; font-weight:500; }
    .empty { padding:44px 10px; text-align:center; color:var(--muted); }
    .empty i { display:block; margin-bottom:12px; font-size:34px; color:#d6c9e8; }

    .bar-row { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
    .bar-row .nm { flex:0 0 150px; font-weight:600; font-size:14px; }
    .bar-row .bar { flex:1; height:10px; background:#f1ebfb; border-radius:10px; overflow:hidden; }
    .bar-row .bar i { display:block; height:100%; background:var(--purple); border-radius:10px; }
    .bar-row .n { flex:0 0 28px; text-align:right; font-weight:700; }
    .mini { display:flex; align-items:center; gap:12px; padding:11px 0; border-bottom:1px solid #f4eff8; }
    .mini:last-child { border-bottom:0; }
    .mini b { display:block; font-size:14px; } .mini small { color:var(--muted); }
    .mini .when { margin-left:auto; font-size:12px; color:var(--muted); white-space:nowrap; }

    .pager { display:flex; gap:6px; justify-content:center; margin-top:22px; flex-wrap:wrap; }
    .pg { min-width:38px; height:38px; display:grid; place-items:center; padding:0 10px; border:1px solid #e3d9ee; border-radius:11px; background:#fff; font-weight:700; color:var(--purple-dark); }
    a.pg:hover { background:#faf6ff; } .pg.active { background:var(--purple); border-color:var(--purple); color:#fff; } .pg.disabled { opacity:.45; }

    @media (max-width: 991px) {
      .side { transform:translateX(-100%); } .side.open { transform:none; } .backdrop.show { display:block; }
      .main { margin-left:0; } .menu-btn { display:block; }
      .top, .page { padding-left:18px; padding-right:18px; }
      .stats { grid-template-columns:1fr 1fr; } .grid-2 { grid-template-columns:1fr; }
    }
    @media (max-width: 640px) {
      .stats { grid-template-columns:1fr; } .form-grid { grid-template-columns:1fr; }
      .bar-row .nm { flex-basis:100px; font-size:13px; }
    }
