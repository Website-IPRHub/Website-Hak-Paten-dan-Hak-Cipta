@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 4; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="kepemilikan-invensi">
                <h2>Surat Pernyataan Kepemilikan Invensi *</h2>
                <p>File dalam bentuk Word, Tanpa Tandatangan</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-unduh">
                    <a href="{{ route('download.template.surat_invensi')}}" class="btn-template-surat-pernyataan-kepemilikan-invensi">Unduh Surat Pernyataan Kepemilikan Invensi</a>
                </div>
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('kepemilikaninvensi.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden data-allowed="doc,docx" data-max-mb="10">
                        <button id="uploadButton" type="button">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('pengalihanhak') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>

@endsection