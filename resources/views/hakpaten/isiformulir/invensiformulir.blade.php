@extends('layouts.app')

@section('title','Test Header')

@section('content')

@php $activeStep = 3; @endphp
@include('hakpaten.isiformulir.menuformulir')

<div class="inv-wrap">
  <div class="judul">
    <h2>Formulir Pendaftaran Paten</h2>
    <p>Isi formulir ini untuk mendapatkan dokumen surat pernyataan kepemilikan invensi oleh inventor</p>
  </div>

  <form class="form inv-form-full" method="POST" action="{{ route('invensi.store') }}">
    @csrf

    <div class="col">
      <div class="field">
        <label class="label">Jumlah inventor <span class="req">*</span></label>
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
        @error('jumlah_inventor') <small style="color:red">{{ $message }}</small> @enderror
      </div>
    </div>

    <div class="col">
      <div class="field">
        <label class="label">Judul Invensi <span class="req">*</span></label>
        <input type="text" class="input" name="judul_invensi"
              placeholder="Masukkan judul invensi"
              value="{{ old('judul_invensi') }}" required>
        @error('judul_invensi') <small style="color:red">{{ $message }}</small> @enderror
      </div>
    </div>
    
    <div class="col">
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
        @error('tanggal_pengisian') <small style="color:red">{{ $message }}</small> @enderror
      </div>
    </div>

    <div class="nama">
      <div class="field">
        <label class="label">Data Inventor <span class="req">*</span></label>
        <div id="inventor-container"></div>
        @error('inventor') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.*') <small style="color:red">{{ $message }}</small> @enderror
      </div>
    </div>

    <template id="inventor-template">
    <div class="inventor-card">
        <p class="inventor-head">Inventor <span class="inv-no"></span></p>

        <div class="inventor-grid">
        {{-- KIRI --}}
        <div class="inventor-col">
            <div class="field">
              <label class="label">Nama Lengkap</label>
              <input type="text" class="input" name="inventor[nama][]" placeholder="Nama lengkap" required>
            </div>

            <div class="field">
              <label class="label">Kewarganegaraan</label>
              <input type="text" class="input" name="inventor[kewarganegaraan][]" placeholder="Contoh: Indonesia" required>
            </div>

            <div class="field">
              <label class="label">Alamat Lengkap (sesuai KTP)</label>
              <textarea class="input" name="inventor[alamat][]" rows="3" placeholder="Alamat lengkap" required></textarea>
            </div>
        </div>

        {{-- KANAN --}}
        <div class="inventor-col">
            <div class="field">
              <label class="label">No. HP</label>
              <input type="text" class="input" name="inventor[no_hp][]" placeholder="08xxxxxxxxxx" required>
            </div>

            <div class="field">
              <label class="label">Email</label>
              <input type="email" class="input" name="inventor[email][]" placeholder="nama@email.com" required>
            </div>

            <div class="field">
              <label class="label">Kode Pos</label>
              <input type="text" class="input" name="inventor[kode_pos][]" placeholder="Contoh: XXXXX" required>
            </div>
        </div>
        </div>
    </div>
    </template>

    <div class="actions-bar">
      <div class="actions-left">
        <button type="button" class="btn-prev" data-fallback="{{ route('hakpaten.draftpatenisiformulir') }}" onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">&laquo; Sebelumnya</button>
        <a id="nextLink" href="{{ route('hakpaten.pengalihanhakformulir') }}" class="btn-selanjutnya is-disabled">Selanjutnya &raquo;</a>
      </div>


      <div class="actions-right">
        <button class="unduh" type="submit" name="action" value="download">Unduh</button>
      </div>
    </div>

  </form>
</div>

@endsection
