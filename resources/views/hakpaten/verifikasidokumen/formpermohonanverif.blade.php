@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 3; @endphp
@include('hakpaten.verifikasidokumen.menuverif')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-isi">
            <div class="formulir-permohonan">
                <h2>Formulir Permohonan *</h2>
                <p>File dalam bentuk Word, Tanpa Tandatangan</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form
                        id="draftForm"
                        method="POST"
                        action="{{ route('patenverif.upload.form', ['verif' => $verif->id]) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <input
                        id="draftFile"
                        type="file"
                        name="file"
                        hidden
                        required
                        accept=".doc,.docx"
                        >

                        <button id="uploadButton" type="button" class="btn-upload">
                        Upload
                        </button>

                        <span id="fileName" class="file-name">
                            Belum pilih file
                        </span>

                        <button id="submitUpload" type="submit" hidden>Kirim</button>
                </div>
            </div>
        </div>
        <div class="actions-bar">
            <button type="button" class="btn-prev"
                data-fallback="{{ route('patenverif.draft',['verif' => $verif->id]) }}"
                onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
                &laquo; Sebelumnya
            </button>

            <a id="nextLink" href="{{ route('patenverif.invensi',['verif' => $verif->id]) }}" class="btn-selanjutnya is-disabled">
                Selanjutnya &raquo;
            </a>
        </div>
    </div>
</section>

@endsection