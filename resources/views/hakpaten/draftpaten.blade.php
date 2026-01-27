@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 2; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box">

      <div class="draft-paten">
        <h2>Draft Paten *</h2>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">

          <form
            id="draftForm"
            action="{{ route('draftpaten.upload') }}"
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

    {{-- ACTIONS BAR --}}
    <div class="actions-bar">
      <button
        type="button"
        class="btn-prev"
        data-fallback="{{ route('hakpaten') }}"
        onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
      >
        &laquo; Sebelumnya
      </button>

      {{-- SUBMIT FORM --}}
      <button
        type="submit"
        form="draftForm"
        class="btn-selanjutnya"
        data-btn-submit
        disabled
      >
        Selanjutnya &raquo;
      </button>
    </div>

  </div>
</section>

@endsection
