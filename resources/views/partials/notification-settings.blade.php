{{-- Pengaturan notifikasi pesan. Pemakaian: @include('partials.notification-settings', ['user' => $user]) --}}
<div class="ns">
    <div class="ns-row">
        <div class="ns-text">
            <b>Notifikasi pesan</b>
            <span>Terima notifikasi browser ketika ada pesan baru.</span>
        </div>

        <form method="POST" action="{{ route('profile.notifications') }}" id="nsForm">
            @csrf
            <input type="hidden" name="notification_enabled" id="nsValue" value="{{ $user->notification_enabled ? '1' : '0' }}">
            <label class="ns-switch" title="Aktif / nonaktif">
                <input type="checkbox" id="nsToggle" {{ $user->notification_enabled ? 'checked' : '' }}>
                <span class="ns-slider"></span>
                <span class="ns-state" id="nsState">{{ $user->notification_enabled ? 'ON' : 'OFF' }}</span>
            </label>
        </form>
    </div>
    <div class="ns-browser" id="nsBrowser"></div>
</div>

<style>
    .ns { margin-top: 22px; padding: 18px 20px; border: 1px solid #ece6f2; border-radius: 16px; background: #faf8fd; font-family: "DM Sans", sans-serif; }
    .ns-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .ns-text b { display: block; font-size: 15.5px; color: #29283a; }
    .ns-text span { display: block; margin-top: 3px; color: #77758a; font-size: 13.5px; }
    .ns-switch { display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; color: #5d498e; }
    .ns-switch input { position: absolute; opacity: 0; width: 0; height: 0; }
    .ns-slider { position: relative; width: 48px; height: 28px; border-radius: 20px; background: #d9d3e3; transition: .2s; }
    .ns-slider::after { content: ""; position: absolute; top: 3px; left: 3px; width: 22px; height: 22px; border-radius: 50%; background: #fff; transition: .2s; box-shadow: 0 1px 3px rgba(0, 0, 0, .25); }
    .ns-switch input:checked + .ns-slider { background: #7661a8; }
    .ns-switch input:checked + .ns-slider::after { transform: translateX(20px); }
    .ns-switch input:focus-visible + .ns-slider { outline: 2px solid #b7a6dc; outline-offset: 2px; }
    .ns-state { min-width: 28px; font-size: 13px; }
    .ns-browser { margin-top: 12px; padding-top: 12px; border-top: 1px dashed #e3d9ee; color: #77758a; font-size: 13px; line-height: 1.6; }
</style>

<script>
(function () {
    var toggle = document.getElementById('nsToggle');
    var value = document.getElementById('nsValue');
    var form = document.getElementById('nsForm');
    var info = document.getElementById('nsBrowser');

    function describe() {
        if (!('Notification' in window)) {
            info.textContent = 'Browser ini tidak mendukung notifikasi. Kamu tetap melihat badge dan pemberitahuan di dalam website.';
        } else if (Notification.permission === 'granted') {
            info.textContent = 'Izin notifikasi browser: diizinkan.';
        } else if (Notification.permission === 'denied') {
            info.textContent = 'Izin notifikasi browser: diblokir. Ubah lewat ikon gembok di address bar jika ingin mengaktifkannya. Fitur lain tetap berjalan normal.';
        } else {
            info.textContent = 'Izin notifikasi browser: belum diminta. Akan diminta saat kamu mengaktifkan notifikasi.';
        }
    }

    toggle.addEventListener('change', function () {
        value.value = toggle.checked ? '1' : '0';
        // minta izin browser (butuh klik pengguna), tetap simpan pengaturan apa pun hasilnya
        if (toggle.checked && 'Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(function () { form.submit(); }, function () { form.submit(); });
        } else {
            form.submit();
        }
    });

    describe();
})();
</script>
