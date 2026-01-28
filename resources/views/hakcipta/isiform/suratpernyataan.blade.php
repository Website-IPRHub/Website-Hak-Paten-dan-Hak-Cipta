@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 2; @endphp
@include('hakcipta.isiform.menuformcipta')

<div class="paten-form-page">
  <div class="judul">
    <h2>Formulir Pendaftaran Ciptaan</h2>
    <p>Isi formulir ini untuk mendapatkan dokumen surat pernyataan hak cipta</p>
  </div>

  <form class="form" method="POST" action="{{ route('hakcipta.suratpernyataan.store') }}">
    @csrf

      <div class="field span-2">
        <label class="label">Judul Ciptaan <span class="req">*</span></label>
        <input
          type="text"
          class="input"
          name="berjudul"
          placeholder="Masukkan judul ciptaan..."
          value="{{ old('berjudul') }}"
          required
        >
        @error('berjudul') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field span-2">
        <label class="label">Produk Ciptaan Berupa? <span class="req">*</span></label>
        <input
          type="text"
          class="input"
          name="berupa"
          placeholder="Produk ciptaan berupa..."
          value="{{ old('berupa') }}"
          required
        >
        @error('berupa') <small class="err">{{ $message }}</small> @enderror
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

    {{-- ACTIONS BAR --}}
    <div class="actions-bar">
      <div class="actions-left">
        <button
          type="button"
          class="btn-prev"
          data-fallback="{{ route('formpendaftarancipta') }}"
          onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
        >
          &laquo; Sebelumnya
        </button>

        <a class="btn-next" href="{{ route('pengalihanhakcipta') }}">
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
</div>
@endsection
