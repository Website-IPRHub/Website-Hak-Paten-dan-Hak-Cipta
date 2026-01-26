@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 7; @endphp
@include('hakpaten.verifikasidokumen.menuverif')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="gambar-prototipe">
            <h2>Upload Gambar Prototipe (Jika Ada)</h2>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form
                        id="draftForm"
                        method="POST"
                        action="{{ route('patenverif.upload.gambar', ['verif' => $verif->id]) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <input id="draftFile" type="file" name="file" hidden accept=".jpg,.jpeg,.png,.svg">


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
                data-fallback="{{ route('patenverif.scanktp',['verif' => $verif->id]) }}"
                onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
                &laquo; Sebelumnya
            </button>

            <a id="nextLink"
                href="{{ route('patenverif.deskripsi',['verif' => $verif->id]) }}"
                class="btn-selanjutnya {{ empty($verif->scan_ktp) ? 'is-disabled' : '' }}">
                Selanjutnya &raquo;
            </a>

        </div>
    </div>
</section>
@endsection
