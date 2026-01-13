@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 3; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="formulir-permohonan">
                <h2>Formulir Permohonan *</h2>
                <p>File dalam bentuk Word, Tanpa Tandatangan</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-unduh">
                    <a href="{{ route('download.template.Form Daftar Paten (2025)')}}" class="btn-template-formulir-permohonan">Unduh Form Daftar Paten</a>
                </div>
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('formulirpermohonan.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" hidden>
                        <button id="uploadButton" for="draftFile">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('kepemilikaninvensi') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>

@endsection