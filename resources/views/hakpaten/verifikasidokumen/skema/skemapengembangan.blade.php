@extends('layouts.app')

@section('title','Skema Pengembangan')

@section('content')
<div class="judul">
  <h2>Skema Penelitian Pengembangan (TKT 7 - 9)</h2>
  <p>Catatan: Isi form ini untuk menghasilkan surat pernyataan TKT 7-9.<br>
      Form ini diisi oleh Inventor 1 (Dosen)</p>
<p>Upload surat pernyataan skema pengembangan HARUS sudah dilengkapi dengan tanda tangan dan bermaterai</p>
</div>

@if(session('success'))
  <div class="alert-success">
    {{ session('success') }}
  </div>
@endif

{{-- FORM DOWNLOAD (wajib membungkus semua input) --}}
<form id="downloadForm" class="form" method="POST"
      action="{{ route('patenverif.skema.download', ['verif' => $verif->id]) }}">
  @csrf

  <div class="grid-2">
    {{-- KIRI (3 field) --}}
    <div class="col">
      <div class="field">
        <label class="label">Nama Lengkap <span class="req">*</span></label>
        <input class="input" name="nama_lengkap" placeholder="Masukkan nama lengkap Dosen (Inventor 1)"
               value="{{ old('nama_lengkap') }}" required>
        @error('nama_lengkap') <small class="error">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Program Studi <span class="req">*</span></label>
        <input class="input" name="program_studi" placeholder="Masukkan program studi"
               value="{{ old('program_studi') }}" required>
        @error('program_studi') <small class="error">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Judul Paten <span class="req">*</span></label>
        <input class="input" name="judul_paten" placeholder="Masukkan judul paten"
               value="{{ old('judul_paten', $verif->judul_paten ?? '') }}" required>
        @error('judul_paten') <small class="error">{{ $message }}</small> @enderror
      </div>
    </div>

    {{-- KANAN (3 field) --}}
    <div class="col">
      <div class="field">
        <label class="label">NIDN/NIP <span class="req">*</span></label>
        <input
                        type="text"
                        class="input nidn-nip-input"
                        name="nidn_nip"
                        placeholder="Masukkan NIDN/NIP Anda"
                        required
                      >
                      <small class="nip-warning">
                        NIDN/NIP harus terdiri dari 8 atau 18 digit angka
                      </small>
        @error('nidn_nip') <small class="error">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Fakultas <span class="req">*</span></label>
        <select class="input" name="fakultas" required>
          <option value="" disabled @selected(!old('fakultas'))>-- Pilih Fakultas --</option>
          <option value="Fakultas Teknik" @selected(old('fakultas')=='Fakultas Teknik')>Fakultas Teknik</option>
          <option value="Fakultas Sains dan Matematika" @selected(old('fakultas')=='Fakultas Sains dan Matematika')>Fakultas Sains dan Matematika</option>
          <option value="Fakultas Kesehatan Masyarakat" @selected(old('fakultas')=='Fakultas Kesehatan Masyarakat')>Fakultas Kesehatan Masyarakat</option>
          <option value="Fakultas Kedokteran" @selected(old('fakultas')=='Fakultas Kedokteran')>Fakultas Kedokteran</option>
          <option value="Fakultas Perikanan dan Ilmu Kelautan" @selected(old('fakultas')=='Fakultas Perikanan dan Ilmu Kelautan')>Fakultas Perikanan dan Ilmu Kelautan</option>
          <option value="Fakultas Peternakan dan Pertanian" @selected(old('fakultas')=='Fakultas Peternakan dan Pertanian')>Fakultas Peternakan dan Pertanian</option>
          <option value="Fakultas Psikologi" @selected(old('fakultas')=='Fakultas Psikologi')>Fakultas Psikologi</option>
          <option value="Fakultas Hukum" @selected(old('fakultas')=='Fakultas Hukum')>Fakultas Hukum</option>
          <option value="Fakultas Ilmu Sosial dan Ilmu Politik" @selected(old('fakultas')=='Fakultas Ilmu Sosial dan Ilmu Politik')>Fakultas Ilmu Sosial dan Ilmu Politik</option>
          <option value="Fakultas Ilmu Budaya" @selected(old('fakultas')=='Fakultas Ilmu Budaya')>Fakultas Ilmu Budaya</option>
          <option value="Fakultas Ekonomi dan Bisnis" @selected(old('fakultas')=='Fakultas Ekonomi dan Bisnis')>Fakultas Ekonomi dan Bisnis</option>
          <option value="Sekolah Vokasi" @selected(old('fakultas')=='Sekolah Vokasi')>Sekolah Vokasi</option>
          <option value="Sekolah Pasca Sarjana" @selected(old('fakultas')=='Sekolah Pasca Sarjana')>Sekolah Pasca Sarjana</option>
        </select>
        @error('fakultas') <small class="error">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Tanggal Pengisian <span class="req">*</span></label>
        <input type="date" class="input" id="tanggal_pengisian" name="tanggal_pengisian"
               value="{{ old('tanggal_pengisian', now()->format('Y-m-d')) }}" required>
        @error('tanggal_pengisian') <small class="error">{{ $message }}</small> @enderror
      </div>
    </div>
  </div>
</form>

{{-- ACTIONS BAR --}}
<div class="skm-actions">
  <div class="skm-actions__left">
    <button type="button" class="skm-btn skm-btn--prev"
      data-fallback="{{ route('hakpaten.draftpatenisiformulir') }}"
      onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
      &laquo; Sebelumnya
    </button>

    <a class="btn-next"
      href="{{ route('patenverif.draft', ['verif' => $verif->id]) }}">
      Selanjutnya &raquo;
    </a>
  </div>

  <div class="actions-right">
    {{-- tombol unduh submit ke form download --}}
    <div class="actions-right2" style="display:flex; gap:10px; align-items:center;">
        <select form="downloadForm" name="download_format" class="input" style="width:160px;">
          <option value="pdf"  {{ old('download_format','pdf')=='pdf' ? 'selected' : '' }}>PDF</option>
          <option value="docx" {{ old('download_format')=='docx' ? 'selected' : '' }}>DOCX</option>
        </select>

        <button form="downloadForm" class="unduh" type="submit" name="action" value="download">
          Unduh
        </button>

      </div>

    {{-- FORM UPLOAD (verif) --}}
    <div class="button-upload">
      <form id="draftForm"
            action="{{ route('patenverif.skema.upload', ['verif' => $verif->id]) }}"
            method="POST" enctype="multipart/form-data">
        @csrf

        <input id="draftFile"
              type="file"
              name="file"
              required
              hidden
              accept=".doc,.docx,.pdf">

        <button id="uploadButton" type="button" class="btn-upload">Upload</button>
        <span id="fileName">Belum Pilih File</span>
      </form>
    </div>
  </div>
</div>

@endsection
