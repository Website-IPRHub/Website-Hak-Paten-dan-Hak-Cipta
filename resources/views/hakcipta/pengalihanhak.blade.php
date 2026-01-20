@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 4 @endphp
@include('hakcipta.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="pengalihan-hak">
                <h2>Surat Pengalihan Hak Cipta*</h2>
                <p>File dalam bentuk Word, Tanpa Tandatangan</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-unduh">
                    <a href="{{ route('hakcipta.download.template.pengalihan') }}" class="btn-download">Download Template Surat Pengalihan</a>
                </div>
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('hakcipta.pengalihanhak.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden data-allowed="doc,docx" data-max-mb="10">
                        <button id="uploadButton" type="button">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('hakcipta.tandaterima') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>

@endsection