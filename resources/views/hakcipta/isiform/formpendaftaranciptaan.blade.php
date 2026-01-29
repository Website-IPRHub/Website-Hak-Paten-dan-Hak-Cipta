@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 1; @endphp
@include('hakcipta.isiform.menuformcipta')

<div class="paten-form-page">
  <div class="judul">
    <h2>Formulir Pendaftaran Ciptaan</h2>
    <p>Isi formulir ini untuk mendapatkan dokumen formulir pendaftaran hak cipta</p>
  </div>

  <form class="form" method="POST" action="{{ route('isiform.store') }}">
    @csrf

    {{-- GRID UTAMA --}}
    <div class="grid-2">
      <div class="field">
        <label class="label">Jumlah pencipta <span class="req">*</span></label>
        <input
          type="number"
          class="input"
          id="jumlah_inventor"
          name="jumlah_inventor"
          min="1"
          max="20"
          value="{{ old('jumlah_inventor', 1) }}"
          required
        >
        @error('jumlah_inventor') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Link Ciptaan <span class="req">*</span></label>
        <input
          type="url"
          class="input"
          name="link_ciptaan"
          placeholder="Contoh: https://drive.google.com/..."
          value="{{ old('link_ciptaan') }}"
          required
        >
        @error('link_ciptaan') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field span-2">
        <label class="label">Judul Ciptaan <span class="req">*</span></label>
        <input
          type="text"
          class="input"
          name="judul_ciptaan"
          placeholder="Masukkan judul ciptaan"
          value="{{ old('judul_ciptaan') }}"
          required
        >
        @error('judul_ciptaan') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Tanggal Pengisian <span class="req">*</span></label>
        <input
          type="date"
          class="input"
          id="tanggal_pengisian"
          name="tanggal_pengisian"
          value="{{ old('tanggal_pengisian', now()->format('Y-m-d')) }}"
          required
        >
        @error('tanggal_pengisian') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field span-2">
        <label class="label">Tempat Pengisian <span class="req">*</span></label>
        <p class="hint">Tempat saat anda mengisi form ini.</p>
        <input
          type="text"
          class="input"
          name="tempat"
          placeholder="Contoh: Semarang"
          value="{{ old('tempat') }}"
          required
        >
        @error('tempat') <small class="err">{{ $message }}</small> @enderror
      </div>
    </div>

    {{-- KUASA (MUNCUL KALAU "MELALUI") --}}

    {{-- DATA PENCIPTA --}}
    <div class="nama mt-16">
      <div class="field">
        <label class="label">Data Pencipta <span class="req">*</span></label>
        <div id="inventor-container"></div>
        @error('inventor') <small class="err">{{ $message }}</small> @enderror
        @error('inventor.*') <small class="err">{{ $message }}</small> @enderror
      </div>
    </div>

    {{-- ULASAN (DI ATAS BUTTON) --}}
    <div class="field mt-16">
      <label class="label">Ulasan Ciptaan <span class="req">*</span></label>
      <p class="hint">Tulis singkat (± 2–3 kalimat).</p>
      <textarea
        class="input input-full"
        name="uraian"
        rows="4"
        maxlength="350"
        placeholder="Masukkan uraian produk ciptaan"
        required
        >{{ old('uraian') }}</textarea>

      @error('uraian') <small class="err">{{ $message }}</small> @enderror
    </div>

    {{-- ACTIONS BAR --}}
    <div class="actions-bar">
      <div class="actions-left">
        <button
          type="button"
          class="btn-prev"
          data-fallback="{{ route('menucipta') }}"
          onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
        >
          &laquo; Sebelumnya
        </button>

        <a class="btn-next" href="{{ route('suratpernyataan') }}">
  Selanjutnya &raquo;
</a>

      </div>

      <div class="actions-right" style="display:flex; gap:10px;">
        <button class="unduh" type="submit" name="action" value="download">
          Unduh
        </button>
      </div>
    </div>

  </form>

  {{-- TEMPLATE PENCIPTA --}}
  <template id="inventor-template">
    <div class="inventor-card">
      <p class="inventor-head">Pencipta <span class="inv-no"></span></p>

      <div class="grid-2">
        <div class="field">
          <label class="label">Nama Lengkap <span class="req">*</span></label>
          <input type="text" class="input" name="inventor[nama][]" placeholder="Nama lengkap" required>
        </div>

        <div class="field">
          <label class="label">No. HP <span class="req">*</span></label>
          <input type="text" class="input" name="inventor[no_hp][]" placeholder="Contoh: 08xxxxxxxxxx" required>
        </div>

        <div class="field">
          <label class="label">Telp Rumah</label>
          <input type="text" class="input" name="inventor[tlp_rumah][]" placeholder="Contoh: 021-1234567">
        </div>

        <div class="field">
          <label class="label">Email <span class="req">*</span></label>
          <input type="email" class="input" name="inventor[email][]" placeholder="nama@email.com" required>
        </div>

        <div class="field span-2">
          <label class="label">Alamat Lengkap (sesuai KTP) <span class="req">*</span></label>
          <textarea class="input" name="inventor[alamat][]" rows="3" placeholder="Alamat lengkap" required></textarea>
        </div>
      </div>
    </div>
  </template>

</div>
@endsection
