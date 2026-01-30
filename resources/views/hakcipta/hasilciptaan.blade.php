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
                    <form id="draftForm" action="{{ route('hakcipta.hasilciptaan.uploadHasilCiptaan') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input
                        id="draftFile"
                        type="file"
                        name="file"
                        hidden
                        required
                        accept=".pdf"
                        data-allowed="pdf"
                        data-max-mb="10"
                        >
                        <button id="uploadButton" type="button">Upload</button>
                        <span id="fileName" class="file-name">
                            @if($cipta->hasil_ciptaan)
                                {{ basename($cipta->hasil_ciptaan) }}
                            @else
                                Belum pilih file
                            @endif
                        </span>

                        <button id="submitUpload" type="submit" hidden>Kirim</button>

                        <div id="fileError" style="display:none; margin-top:8px; color:#dc2626; font-weight:600;">
                            Tipe file tidak sesuai.
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="actions-bar">
            <button type="button" class="btn-prev"
                data-fallback="{{ route('hakcipta.tandaterima') }}"
                onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
                &laquo; Sebelumnya
            </button>

            <a class="btn-next" href="{{ route('hakcipta.linkciptaan')}}">
                Selanjutnya &raquo;
            </a>
        </div>
    </div>
</section>
@endsection
