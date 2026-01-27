@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 5; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="pengalihan-hak">
                <h2>Surat Pernyataan Pengalihan Hak *</h2>
                <p>File dalam bentuk Word, Tanpa Tandatangan</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('pengalihanhak.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden data-allowed="doc,docx" data-max-mb="10">
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
            data-fallback="{{ route('kepemilikaninvensi') }}"
            onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
          >
            &laquo; Sebelumnya
          </button>
            <a id="nextLink" href="{{ route('scanktp') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>

@endsection