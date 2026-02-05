@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 2; @endphp
@include('hakcipta.verifikasi.menuciptaverif')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-isi">
      <div class="formulir-permohonan">
        <h2>Formulir Permohonan *</h2>
        <p>File dalam bentuk Word, Tanpa Tandatangan</p>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">
          <form
            id="draftForm"
            method="POST"
            action="{{ route('ciptaverif.upload.form', ['verif' => $verif->id]) }}"
            enctype="multipart/form-data"
          >
            @csrf

            <input
              id="draftFile"
              type="file"
              name="file"
              hidden
              required
              accept=".doc,.docx,.pdf"
              data-allowed="doc,docx,pdf"
            >

            <button id="uploadButton" type="button" class="btn-upload">
              Upload
            </button>

            <span id="fileName" class="file-name">
              @if($verif->surat_permohonan)
                {{ basename($verif->surat_permohonan) }}
              @else
                Belum pilih file
              @endif
            </span>

            <button id="submitUpload" type="submit" hidden>Kirim</button>
          </form>
        </div>
      </div>
    </div>

    <div class="actions-bar">
      <button type="button" class="btn-prev"
        data-fallback="{{ route('datadiricipta', ['verif' => $verif->id]) }}"
        onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
        &laquo; Sebelumnya
      </button>

      <a class="btn-next" href="{{ route('ciptaverif.suratpernyataan', ['verif' => $verif->id]) }}">
        Selanjutnya &raquo;
      </a>
    </div>
  </div>
</section>

@endsection
