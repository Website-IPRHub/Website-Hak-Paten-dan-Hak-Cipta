@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 8; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
  <div class="section-inner">

    {{-- CONTENT --}}
    <div class="content-box">

      <div class="gambar-prototipe">
        <h2>Upload Gambar Prototipe (Jika Ada)</h2>
        <p>Format JPG / JPEG / PNG (opsional)</p>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">

          <form
            id="draftForm"
            action="{{ route('uploadgambarprototipe.uploadPrototipe') }}"
            method="POST"
            enctype="multipart/form-data"
            data-upload-form
          >
            @csrf

            <input
              type="file"
              name="file"
              hidden
              data-allowed="jpg,jpeg,png"
              data-max-mb="10"
            >

            <button type="button" class="btn-upload" data-btn-pick>
              Pilih File
            </button>

            <span class="file-name" data-file-name>
              Belum pilih file
            </span>

          </form>

        </div>
      </div>

    </div>
    {{-- END CONTENT --}}

    {{-- ACTION BAR --}}
    <div class="actions-bar">

      {{-- ⬅️ SEBELUMNYA --}}
      <button
        type="button"
        class="btn-prev"
        data-fallback="{{ route('tandaterima') }}"
        onclick="(history.length > 1)
          ? history.back()
          : (window.location.href=this.dataset.fallback)"
      >
        &laquo; Sebelumnya
      </button>

      {{-- ➡️ SELANJUTNYA --}}
      <button
        type="submit"
        class="btn-selanjutnya"
        form="draftForm"
        data-btn-submit
      >
        Selanjutnya &raquo;
      </button>

    </div>

  </div>
</section>

@endsection
