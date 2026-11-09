{{--
  Lonceng notifikasi pesan (mahasiswa & konsultan).
  Dipasang di navbar: @include('partials.notification-bell')
  Di ruang chat tambahkan id sesi:  @include('partials.notification-bell', ['roomId' => $consultation->id])

  Cara kerja: memanggil /notifications/unread tiap 8 detik (polling biasa, tanpa WebSocket).
  - badge angka pesan belum dibaca
  - dropdown ringkasan per sesi
  - toast di dalam website + notifikasi browser untuk pesan BARU dari lawan chat
  - tidak menampilkan notifikasi untuk ruang chat yang sedang dilihat
--}}
@auth
@if (in_array(auth()->user()->role, ['user', 'konsultan'], true))
@php
    $nbIsKonsultan = auth()->user()->role === 'konsultan';
    $nbViewUrl = $nbIsKonsultan ? route('consultant.consultations.index') : route('consultation.history');
@endphp

<div class="nb" id="nb"
     data-url="{{ route('notifications.unread') }}"
     data-room="{{ $roomId ?? 0 }}"
     data-uid="{{ auth()->id() }}">
    <button type="button" class="nb-btn" id="nbBtn" aria-label="Notifikasi" aria-expanded="false">
        <i class="fa-regular fa-bell"></i>
        <span class="nb-badge" id="nbBadge" hidden>0</span>
    </button>

    <div class="nb-panel" id="nbPanel" hidden>
        <div class="nb-head">Notifikasi</div>
        <div class="nb-summary" id="nbSummary">Tidak ada pesan baru</div>
        <div class="nb-list" id="nbList"></div>
        <div class="nb-perm" id="nbPerm" hidden></div>
        <a class="nb-foot" href="{{ $nbViewUrl }}">Lihat Konsultasi</a>
    </div>
</div>
<div class="nb-toasts" id="nbToasts" aria-live="polite"></div>

<style>
    .nb { position: relative; display: inline-flex; font-family: "DM Sans", sans-serif; }
    .nb-btn { position: relative; width: 40px; height: 40px; display: grid; place-items: center; border: 1px solid #e8deef; border-radius: 50%; background: #fff; color: #5d498e; font-size: 17px; cursor: pointer; transition: .2s; }
    .nb-btn:hover { background: #faf6ff; }
    .nb-badge { position: absolute; top: -5px; right: -5px; min-width: 20px; height: 20px; padding: 0 5px; border-radius: 20px; background: #e5484d; color: #fff; font: 700 11px/20px "DM Sans", sans-serif; text-align: center; border: 2px solid #fff; box-sizing: content-box; }
    .nb-badge[hidden] { display: none; }
    .nb-panel { position: absolute; top: 48px; right: 0; z-index: 2000; width: 340px; max-width: 92vw; background: #fff; border: 1px solid #ece6f2; border-radius: 18px; box-shadow: 0 18px 44px rgba(60, 42, 80, .16); overflow: hidden; text-align: left; color: #29283a; }
    .nb-panel[hidden] { display: none; }
    .nb-head { padding: 14px 18px 4px; font: 800 17px "Jost", sans-serif; }
    .nb-summary { padding: 0 18px 10px; color: #77758a; font-size: 13.5px; }
    .nb-list { max-height: 300px; overflow-y: auto; border-top: 1px solid #f4eff8; }
    .nb-item { display: block; padding: 12px 18px; border-bottom: 1px solid #f4eff8; text-decoration: none; color: inherit; }
    .nb-item:hover { background: #faf6ff; }
    .nb-item b { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 14px; }
    .nb-item .cnt { flex: 0 0 auto; min-width: 22px; padding: 1px 7px; border-radius: 20px; background: #e5484d; color: #fff; font-size: 11.5px; text-align: center; }
    .nb-item span { display: block; margin-top: 3px; color: #77758a; font-size: 13px; line-height: 1.5; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .nb-item small { color: #a29fb2; font-size: 11.5px; }
    .nb-empty { padding: 20px 18px; color: #77758a; font-size: 13.5px; text-align: center; }
    .nb-perm { padding: 11px 18px; background: #f9f7fb; border-top: 1px solid #f4eff8; color: #77758a; font-size: 12.5px; line-height: 1.6; }
    .nb-perm button { margin-top: 6px; padding: 7px 12px; border: 0; border-radius: 10px; background: #7661a8; color: #fff; font: 700 12.5px "DM Sans", sans-serif; cursor: pointer; }
    .nb-foot { display: block; padding: 13px 18px; text-align: center; background: #faf6ff; color: #5d498e; font-weight: 700; font-size: 14px; text-decoration: none; }
    .nb-foot:hover { background: #f3ecfb; }
    .nb-toasts { position: fixed; right: 18px; bottom: 18px; z-index: 3000; display: flex; flex-direction: column; gap: 10px; width: min(330px, calc(100vw - 36px)); pointer-events: none; }
    .nb-toast { pointer-events: auto; display: block; padding: 13px 16px; border: 1px solid #e3d9ee; border-left: 4px solid #7661a8; border-radius: 14px; background: #fff; box-shadow: 0 12px 30px rgba(60, 42, 80, .18); color: #29283a; text-decoration: none; font-family: "DM Sans", sans-serif; animation: nbIn .25s ease; }
    .nb-toast b { display: block; font-size: 14px; }
    .nb-toast span { display: block; margin-top: 3px; color: #77758a; font-size: 13px; line-height: 1.5; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    @keyframes nbIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
    @media (max-width: 560px) { .nb-panel { position: fixed; top: 64px; left: 12px; right: 12px; width: auto; max-width: none; } }
</style>

<script>
(function () {
    var root = document.getElementById('nb');
    if (!root) return;

    var URL_UNREAD = root.getAttribute('data-url');
    var ROOM = parseInt(root.getAttribute('data-room') || '0', 10);
    var KEY = 'temuedu:lastNotified:' + root.getAttribute('data-uid');
    var BASE_TITLE = document.title;

    var btn = document.getElementById('nbBtn');
    var badge = document.getElementById('nbBadge');
    var panel = document.getElementById('nbPanel');
    var summary = document.getElementById('nbSummary');
    var list = document.getElementById('nbList');
    var permBox = document.getElementById('nbPerm');
    var toasts = document.getElementById('nbToasts');

    var enabled = true;
    var stopped = false;
    var busy = false;

    function getLast() {
        try { var v = localStorage.getItem(KEY); return v === null ? null : parseInt(v, 10); } catch (e) { return null; }
    }
    function setLast(v) {
        try { localStorage.setItem(KEY, String(v)); } catch (e) { /* abaikan */ }
    }

    function canBrowserNotify() {
        return ('Notification' in window) && Notification.permission === 'granted';
    }

    function el(tag, cls, text) {
        var n = document.createElement(tag);
        if (cls) n.className = cls;
        if (text !== undefined) n.textContent = text;
        return n;
    }

    function render(d) {
        enabled = d.enabled;

        if (d.total > 0) {
            badge.textContent = d.total > 99 ? '99+' : d.total;
            badge.hidden = false;
            summary.textContent = d.total + ' pesan belum dibaca';
            document.title = '(' + d.total + ') ' + BASE_TITLE;
        } else {
            badge.hidden = true;
            summary.textContent = 'Tidak ada pesan baru';
            document.title = BASE_TITLE;
        }

        list.innerHTML = '';
        if (!d.items.length) {
            list.appendChild(el('div', 'nb-empty', 'Semua pesan sudah dibaca.'));
        }
        d.items.forEach(function (i) {
            var a = el('a', 'nb-item');
            a.href = i.url;
            var b = el('b', null, i.name);
            b.appendChild(el('em', 'cnt', i.count));
            b.lastChild.style.fontStyle = 'normal';
            a.appendChild(b);
            a.appendChild(el('span', null, i.preview));
            a.appendChild(el('small', null, i.time));
            list.appendChild(a);
        });

        renderPermission();
    }

    function renderPermission() {
        permBox.innerHTML = '';
        permBox.hidden = true;
        if (!enabled || !('Notification' in window)) return;

        if (Notification.permission === 'default') {
            permBox.hidden = false;
            permBox.appendChild(document.createTextNode('Ingin diberi tahu saat ada pesan baru walau tab ini tidak dibuka di depan?'));
            var b = el('button', null, 'Aktifkan notifikasi browser');
            b.type = 'button';
            b.addEventListener('click', function () {
                Notification.requestPermission().then(renderPermission);
            });
            permBox.appendChild(document.createElement('br'));
            permBox.appendChild(b);
        } else if (Notification.permission === 'denied') {
            permBox.hidden = false;
            permBox.textContent = 'Notifikasi browser diblokir. Kamu tetap melihat badge dan pemberitahuan di halaman ini.';
        }
    }

    function showToast(i) {
        var a = el('a', 'nb-toast');
        a.href = i.url;
        a.appendChild(el('b', null, 'Pesan baru dari ' + i.name));
        a.appendChild(el('span', null, i.preview));
        toasts.appendChild(a);
        while (toasts.children.length > 3) toasts.removeChild(toasts.firstChild);
        setTimeout(function () { if (a.parentNode) a.parentNode.removeChild(a); }, 7000);
    }

    function browserNotify(i) {
        if (!canBrowserNotify()) return;
        try {
            // 'tag' sama = notifikasi untuk sesi yang sama saling menggantikan (tidak dobel)
            var n = new Notification('Pesan baru dari ' + i.name, { body: i.preview, tag: 'temuedu-c' + i.consultation_id });
            n.onclick = function () { window.focus(); window.location.href = i.url; n.close(); };
        } catch (e) { /* browser tidak mendukung: abaikan */ }
    }

    // Beri tahu hanya untuk pesan yang BELUM pernah diberitahukan (id lebih besar dari catatan terakhir)
    function notifyNew(d) {
        var last = getLast();

        if (last === null) {          // pertama kali: catat posisi saja, jangan membanjiri notifikasi lama
            setLast(d.latest_id);
            return;
        }

        var fresh = d.items.filter(function (i) { return i.last_id > last; });
        if (d.latest_id > last) setLast(d.latest_id);
        if (!fresh.length || !d.enabled) return;

        fresh.forEach(function (i) {
            // ruang chat yang sedang dilihat tidak perlu notifikasi (pesan masuk lewat chat)
            if (ROOM && i.consultation_id === ROOM && !document.hidden) return;
            if (!document.hidden) showToast(i);
            browserNotify(i);
        });
    }

    function poll() {
        if (stopped || busy) return;
        busy = true;

        fetch(URL_UNREAD, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) {
                var type = r.headers.get('content-type') || '';
                if (!r.ok || type.indexOf('json') === -1) { stopped = true; return null; } // logout / tidak berhak
                return r.json();
            })
            .then(function (d) { if (d) { render(d); notifyNew(d); } })
            .catch(function () { /* jaringan putus: coba lagi nanti */ })
            .then(function () { busy = false; });
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var open = panel.hidden;
        panel.hidden = !open;
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) poll();
    });
    panel.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', function () { panel.hidden = true; btn.setAttribute('aria-expanded', 'false'); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') panel.hidden = true; });
    document.addEventListener('visibilitychange', function () { if (!document.hidden) poll(); });

    poll();
    setInterval(poll, 8000);
})();
</script>
@endif
@endauth
