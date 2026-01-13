@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 8; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="gambar-prototipe">
            <h2>Upload Gambar Prototipe (Jika Ada)</h2>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('uploadgambarprototipe.uploadPrototipe') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" hidden>
                        <button id="uploadButton" for="draftFile">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('deskripsiproduk') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>
@endsection
