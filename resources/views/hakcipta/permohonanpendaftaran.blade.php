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
        <div class="button-upload">
          <form id="draftForm"
                action="{{ route('hakcipta.permohonanpendaftaran.uploadPendaftaran') }}"
                method="POST"
                enctype="multipart/form-data">
            @csrf

            <input id="draftFile"
                   type="file"
                   name="file"
                   required
                   hidden
                   accept=".doc,.docx"
                   data-allowed="doc,docx">

            <button id="uploadButton" type="button" class="btn-upload">
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

            <div id="fileError" style="display:none; margin-top:8px; color:#dc2626; font-weight:600;">
              Tipe file tidak sesuai.
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="actions-bar">
      <button type="button" class="btn-prev"
        data-fallback="{{ route('hakciptapendaftaran') }}"
        onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
        &laquo; Sebelumnya
      </button>

      <a class="btn-next" href="{{ route('hakcipta.suratpernyataan')}}">
        Selanjutnya &raquo;
      </a>
    </div>
  </div>
</section>

@endsection
