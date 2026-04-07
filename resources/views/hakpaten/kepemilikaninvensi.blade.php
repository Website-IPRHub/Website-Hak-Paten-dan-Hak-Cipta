@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 4; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
  <div class="section-inner">

    {{-- CONTENT --}}
    <div class="content-box">

      <div class="kepemilikan-invensi">
        <h2>Surat Pernyataan Kepemilikan Invensi *</h2>
        <p>File dalam bentuk Word, Tanpa Tandatangan</p>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">

          <form
            id="draftForm"
            action="{{ route('kepemilikaninvensi.upload') }}"
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
              accept=".doc,.docx"
              data-allowed="doc,docx"
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

      {{--  SEBELUMNYA --}}
      <button
        type="button"
        class="btn-prev"
        data-fallback="{{ route('formulirpermohonan') }}"
        onclick="(history.length > 1)
          ? history.back()
          : (window.location.href=this.dataset.fallback)"
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
