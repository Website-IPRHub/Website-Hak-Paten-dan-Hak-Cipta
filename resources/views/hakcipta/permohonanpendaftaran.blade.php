@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 2; @endphp
@include('hakcipta.partials.menu')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box">
      <div class="permohonan-pendaftaran">
        <h2>Permohonan Pendaftaran*</h2>
        <p>File dalam bentuk Word, Tanpa Tandatangan</p>
      </div>

      <div class="hero-buttons-start">
        <div class="button-unduh">
          <a href="{{ route('hakcipta.download.template.permohonan') }}" class="btn-download">
            Download Template Surat Permohonan
          </a>
        </div>

        <div class="button-upload">
          <form id="draftForm"
                action="{{ route('ciptaverif.upload.form', ['verif' => $verif->id]) }}"
                method="POST"
                enctype="multipart/form-data">
            @csrf

            <input id="draftFile"
                   type="file"
                   name="file"
                   required
                   hidden
                   accept=".doc,.docx">

            <button id="uploadButton" type="button">Upload</button>

            <span id="fileName">
              @if(!empty($verif->surat_permohonan))
                {{ basename($verif->surat_permohonan) }}
              @else
                Belum Pilih File
              @endif
            </span>

            @error('file')
              <div class="text-danger" style="margin-top:8px;">{{ $message }}</div>
            @enderror
          </form>
        </div>
      </div>
    </div>

    <div class="next">
      <a id="nextLink"
         href="{{ route('ciptaverif.start', ['verif' => $verif->id]) }}"
         class="btn-selanjutnya">
        Selanjutnya
      </a>
    </div>
  </div>
</section>

@endsection
