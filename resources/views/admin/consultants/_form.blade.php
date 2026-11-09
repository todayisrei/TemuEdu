@php
  $isEdit = isset($consultant);
  $fotoUrl = $isEdit ? $consultant->foto_url : null;
@endphp

<div class="form-grid">

  <div class="full section-title">Akun login</div>

  <div class="field">
    <label for="name">Nama lengkap</label>
    <input type="text" id="name" name="name" value="{{ old('name', $isEdit ? $consultant->user->name : '') }}" required maxlength="255">
  </div>

  <div class="field">
    <label for="email">Email <small>(dipakai untuk login)</small></label>
    <input type="email" id="email" name="email" value="{{ old('email', $isEdit ? $consultant->user->email : '') }}" required maxlength="255">
  </div>

  <div class="field full">
    <label for="password">Password @if ($isEdit)<small>(kosongkan jika tidak ingin mengubah)</small>@endif</label>
    <div class="input-row">
      <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" {{ $isEdit ? '' : 'required' }} placeholder="Minimal 8 karakter">
      <button type="button" class="btn btn-ghost" id="togglePw" title="Tampilkan / sembunyikan"><i class="fa-solid fa-eye"></i></button>
      <button type="button" class="btn btn-ghost" id="genPw"><i class="fa-solid fa-wand-magic-sparkles"></i> Buat otomatis</button>
    </div>
    <div class="hint">Catat password ini lalu berikan ke konsultan. Setelah disimpan, password tidak bisa dilihat lagi (hanya bisa di-reset).</div>
  </div>

  <div class="full section-title" style="margin-top:14px">Profil konsultan</div>

  <div class="field">
    <label for="prodi_id">Program studi</label>
    <select id="prodi_id" name="prodi_id" required>
      <option value="">-- Pilih program studi --</option>
      @foreach ($prodis as $p)
        <option value="{{ $p->id }}" @selected(old('prodi_id', $isEdit ? $consultant->prodi_id : '') == $p->id)>{{ $p->nama_prodi }}</option>
      @endforeach
    </select>
  </div>

  <div class="field">
    <label for="harga">Tarif per sesi <small>(Rupiah)</small></label>
    <input type="number" id="harga" name="harga" min="0" step="1000" value="{{ old('harga', $isEdit ? $consultant->harga : 0) }}" required>
  </div>

  <div class="field">
    <label for="alumni">Asal kampus / alumni</label>
    <input type="text" id="alumni" name="alumni" value="{{ old('alumni', $isEdit ? $consultant->alumni : '') }}" maxlength="255" placeholder="Contoh: Universitas ...">
  </div>

  <div class="field">
    <label for="ipk">IPK</label>
    <input type="number" id="ipk" name="ipk" min="0" max="4" step="0.01" value="{{ old('ipk', $isEdit ? $consultant->ipk : '') }}" placeholder="Contoh: 3.85">
  </div>

  <div class="field">
    <label for="domisili">Domisili</label>
    <input type="text" id="domisili" name="domisili" value="{{ old('domisili', $isEdit ? $consultant->domisili : '') }}" maxlength="255" placeholder="Kota tempat tinggal">
  </div>

  <div class="field">
    <label for="portofolio_link">Link LinkedIn / portofolio</label>
    <input type="url" id="portofolio_link" name="portofolio_link" value="{{ old('portofolio_link', $isEdit ? $consultant->portofolio_link : '') }}" maxlength="255" placeholder="https://...">
  </div>

  <div class="field full">
    <label for="kompetensi">Kompetensi / bidang keahlian</label>
    <textarea id="kompetensi" name="kompetensi" maxlength="2000" placeholder="Contoh: Akuntansi keuangan, perpajakan, SPSS...">{{ old('kompetensi', $isEdit ? $consultant->kompetensi : '') }}</textarea>
  </div>

  <div class="field full">
    <label for="foto">Foto profil <small>(JPG/PNG/WEBP, maks. 2 MB)</small></label>
    <div class="foto-box">
      <div class="preview" id="fotoPreview">
        @if ($fotoUrl)<img src="{{ $fotoUrl }}" alt="Foto saat ini">@else<i class="fa-solid fa-user"></i>@endif
      </div>
      <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp">
    </div>
  </div>

  <div class="field full">
    <label class="check">
      <input type="hidden" name="is_active" value="0">
      <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $isEdit ? $consultant->is_active : true))>
      Konsultan aktif (tampil di daftar konsultan untuk mahasiswa)
    </label>
  </div>
</div>

<div class="form-actions">
  <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
  <a href="{{ route('admin.consultants.index') }}" class="btn btn-ghost">Batal</a>
</div>

@push('scripts')
<script>
  (function () {
    var pw = document.getElementById('password');

    document.getElementById('togglePw').addEventListener('click', function () {
      pw.type = pw.type === 'password' ? 'text' : 'password';
    });

    document.getElementById('genPw').addEventListener('click', function () {
      var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789', out = '';
      var arr = new Uint32Array(10);
      crypto.getRandomValues(arr);
      arr.forEach(function (n) { out += chars[n % chars.length]; });
      pw.value = out;
      pw.type = 'text'; // tampilkan supaya bisa dicatat
    });

    document.getElementById('foto').addEventListener('change', function (e) {
      var f = e.target.files[0];
      if (!f) return;
      var r = new FileReader();
      r.onload = function (ev) {
        document.getElementById('fotoPreview').innerHTML = '<img src="' + ev.target.result + '" alt="Pratinjau">';
      };
      r.readAsDataURL(f);
    });
  })();
</script>
@endpush
