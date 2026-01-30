@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 6 @endphp
@include('hakcipta.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="tanda-terima">
                <h2>Tanda Terima</h2>
                <p>*file dalam bentuk PDF
*berlaku mulai hari Senin, 1 Juli 2024
*surat tanda terima dapat didownload di <a href="biks.undip.ac.id/download">biks.undip.ac.id/download</a></p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('hakcipta.tandaterima.uploadTandaTerima') }}" method="POST" enctype="multipart/form-data">
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
                            @if($cipta->tanda_terima)
                                {{ basename($cipta->tanda_terima) }}
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
                data-fallback="{{ route('hakcipta.scanktp',) }}"
                onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
                &laquo; Sebelumnya
            </button>

            <a class="btn-next" href="{{ route('hakcipta.hasilciptaan')}}">
                Selanjutnya &raquo;
            </a>
        </div>
    </div>
</section>

@endsection