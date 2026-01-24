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
                <div class="button-unduh">
                    <a href="{{ route('download.template.draftpaten')}}" class="btn-template-draft-paten">Unduh Template Draft Paten</a>
                </div>
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('draftpaten.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden data-allowed="doc,docx" data-max-mb="10">
                        <button id="uploadButton" type="button">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('formulirpermohonan') }}" class="btn-selanjutnya is-disabled">Selanjutnya &raquo;</a>
        </div>
    </div>
</section>
@endsection
