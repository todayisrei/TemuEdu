<div class="form-grid">
  <div class="field full">
    <label for="nama_prodi">Nama program studi</label>
    <input type="text" id="nama_prodi" name="nama_prodi" value="{{ old('nama_prodi', $prodi->nama_prodi ?? '') }}" placeholder="Contoh: Bisnis Digital" required maxlength="100">
    @error('nama_prodi') <div class="err">{{ $message }}</div> @enderror
  </div>
</div>

<div class="form-actions">
  <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
  <a href="{{ route('admin.prodi.index') }}" class="btn btn-ghost">Batal</a>
</div>
