@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 6; @endphp
@include('hakpaten.verifikasidokumen.menuverif')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="scan-ktp">
            <h2>Scan KTP *</h2>
            <p>Seluruh Scan KTP Pencipta dijadikan 1 (Satu) file PDF</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form
                        id="draftForm"
                        method="POST"
                        action="{{ route('patenverif.upload.ktp', ['verif' => $verif->id]) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <input
                        id="draftFile"
                        type="file"
                        name="file"
                        hidden
                        required
                        accept=".pdf"
                        >

                        <button id="uploadButton" type="button" class="btn-upload">
                        Upload
                        </button>

                        <span id="fileName" class="file-name">Belum pilih file</span>

                        {{-- submit beneran (disembunyikan, dipencet via JS setelah pilih file) --}}
                        <button id="submitUpload" type="submit" style="display:none;">Kirim</button>
                    </form>

                </div>
            </div>
        </div>
        <div class="actions-bar">
            <button type="button" class="btn-prev"
                data-fallback="{{ route('patenverif.pengalihanhak',['verif' => $verif->id]) }}"
                onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
                &laquo; Sebelumnya
            </button>

            <a class="btn-next" href="{{ route('patenverif.uploadgambar',['verif' => $verif->id]) }}">
                Selanjutnya &raquo;
            </a>

        </div>
    </div>
</section>
@endsection
