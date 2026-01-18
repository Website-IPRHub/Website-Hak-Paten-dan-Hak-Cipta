@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 7; @endphp
@include('hakcipta.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="hasil-ciptaan">
            <h2>Hasil Ciptaan *</h2>
            <p>*Ukuran Maksimal File 10Mb
*Khusus jenis Karya Rekaman Video mengupload screenshoot video (pdf)</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('hakcipta.hasilciptaan.uploadScanKTP') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden>
                        <button id="uploadButton" for="draftFile">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('hakcipta.linkciptaan') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>
@endsection
