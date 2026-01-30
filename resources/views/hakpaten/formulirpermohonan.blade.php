@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 3; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
  <div class="section-inner">

    {{-- CONTENT --}}
    <div class="content-box">

      <div class="formulir-permohonan">
        <h2>Formulir Permohonan *</h2>
        <p>File dalam bentuk Word, Tanpa Tandatangan</p>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">

          <form
            id="draftForm"
            method="POST"
            action="{{ route('formulirpermohonan.upload') }}"
            enctype="multipart/form-data"
          >
            @csrf

            <input
              type="file"
              name="file"
              hidden
              required
              accept=".doc,.docx"
              data-allowed="doc,docx"
              data-max-mb="10"
            >

            <button id="uploadButton" type="submit" class="btn-upload">
              Upload
            </button>

            <span id="fileName" class="file-name">
              @if($cipta->surat_permohonan)
                {{ basename($cipta->surat_permohonan) }}
              @else
                Belum pilih file
              @endif
            </span>

            <button id="submitUpload" type="submit" hidden>Kirim</button>
          </form>

        </div>
      </div>

    </div>
    {{-- END CONTENT --}}

    {{-- ACTION BAR (RAPI & SEJAJAR) --}}
    <div class="actions-bar">
      <button
        type="button"
        class="btn-prev"
        onclick="history.back()"
      >
        &laquo; Sebelumnya
      </button>

      <button
        type="submit"
        class="btn-selanjutnya"
        form="draftForm"
        data-btn-submit
        disabled
      >
        Selanjutnya &raquo;
      </button>
    </div>

  </div>
</section>

@endsection
