@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 6; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="scan-ktp">
            <h2>Scan KTP *</h2>
            <p>Seluruh Scan KTP Pencipta dijadikan 1 (Satu) file PDF</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('scanktp.uploadScanKTP') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden data-allowed="pdf" data-max-mb="10">
                        <button id="uploadButton" type="button">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>

        <div class="actions-bar">
                    <button
            type="button"
            class="btn-prev"
            data-fallback="{{ route('pengalihanhak') }}"
            onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
          >
            &laquo; Sebelumnya
          </button>
            <a id="nextLink" href="{{ route('tandaterima') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>
@endsection
