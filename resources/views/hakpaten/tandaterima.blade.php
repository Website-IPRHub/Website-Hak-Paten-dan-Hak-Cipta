@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 7; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
  <div class="section-inner">

    {{-- CONTENT --}}
    <div class="content-box">

      <div class="tanda-terima">
        <h2>Surat Tanda Terima Berkas *</h2>
        <p>
          * file dalam bentuk PDF<br>
          * berlaku mulai hari Senin, 1 Juli 2024<br>
          * surat tanda terima dapat didownload di
          <a href="https://biks.undip.ac.id/download" target="_blank">
            biks.undip.ac.id/download
          </a>
        </p>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">

          <form
            id="draftForm"
            action="{{ route('tandaterima.uploadFormSuratTandaTerimaBerkas') }}"
            method="POST"
            enctype="multipart/form-data"
            data-upload-form
          >
            @csrf

            <input
              type="file"
              name="file"
              hidden
              required
              accept=".pdf"
              data-allowed="pdf"
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

      {{-- SEBELUMNYA --}}
      <button
        type="button"
        class="btn-prev"
        data-fallback="{{ route('scanktp') }}"
        onclick="(history.length > 1)
          ? history.back()
          : (window.location.href=this.dataset.fallback)"
      >
        &laquo; Sebelumnya
      </button>

      {{-- SELANJUTNYA --}}
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
