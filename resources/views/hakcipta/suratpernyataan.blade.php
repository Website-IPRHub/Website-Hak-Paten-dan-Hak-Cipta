@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 3 @endphp
@include('hakcipta.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="surat-pernyataan">
                <h2>Surat Pernyataan*</h2>
                <p>File dalam bentuk Word, Tanpa Tandatangan</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-unduh">
                    <a href="{{ route('hakcipta.download.template.pernyataan') }}" class="btn-download">Download Template Surat Pernyataan</a>
                </div>
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('hakcipta.suratpernyataan.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input id="draftFile" type="file" name="file" hidden required>
                        <button id="uploadButton">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('hakcipta.pengalihanhak') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>

@endsection