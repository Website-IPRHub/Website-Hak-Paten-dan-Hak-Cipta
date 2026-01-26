@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 2; @endphp
@include('hakpaten.verifikasidokumen.menuverif')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-isi">
      <div class="draft-paten">
        <h2>Draft Paten <span class="req">*</span></h2>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">
          <form
            id="draftForm"
            method="POST"
            action="{{ route('patenverif.upload.draft', ['verif' => $verif->id]) }}"
            enctype="multipart/form-data"
          >
            @csrf

            <input
              id="draftFile"
              type="file"
              name="file"
              hidden
              required
              accept=".doc,.docx"
            >

            <button id="uploadButton" type="button" class="btn-upload">
              Upload
            </button>

            <span id="fileName" class="file-name">Belum pilih file</span>

            {{-- submit beneran (disembunyikan, dipencet via JS setelah pilih file) --}}
            <button id="submitUpload" type="submit" style="display:none;">Kirim</button>
          </form>
        </div>
      </div>
    </div>

    <div class="actions-bar">
      <button type="button" class="btn-prev"
        data-fallback="{{ route('patenverif.datadiri',['verif' => $verif->id]) }}"
        onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
        &laquo; Sebelumnya
      </button>

      <a
        id="nextLink"
        href="{{ route('patenverif.formpermohonan', ['verif' => $verif->id]) }}"
        class="btn-selanjutnya is-disabled"
      >
        Selanjutnya »
      </a>
    </div>
  </div>
</section>
@endsection
