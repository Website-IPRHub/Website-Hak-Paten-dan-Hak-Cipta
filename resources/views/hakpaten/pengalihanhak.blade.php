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
                <div class="button-unduh">
                    <a href="{{ route('download.template.pengalihan_hak')}}" class="btn-template-surat-pernyataan-pengalihan-hak">Unduh Surat Pernyataan Pengalihan Hak</a>
                </div>
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('pengalihanhak.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden>
                        <button id="uploadButton" for="draftFile">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('scanktp') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>

@endsection