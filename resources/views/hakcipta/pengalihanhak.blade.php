@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 4 @endphp
@include('hakcipta.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="pengalihan-hak">
                <h2>Surat Pengalihan Hak Cipta*</h2>
                <p>File dalam bentuk Word, Tanpa Tandatangan</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('hakcipta.pengalihanhak.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input
                        id="draftFile"
                        type="file"
                        name="file"
                        hidden
                        required
                        accept=".doc,.docx"
                        data-allowed="doc,docx"
                        data-max-mb="10"
                        >
                        <button id="uploadButton" type="button">Upload</button>
                        <span id="fileName" class="file-name">
                            @if($cipta->surat_pengalihan)
                                {{ basename($cipta->surat_pengalihan) }}
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
                data-fallback="{{ route('hakcipta.suratpernyataan') }}"
                onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
                &laquo; Sebelumnya
            </button>

            <a class="btn-next" href="{{ route('hakcipta.scanktp')}}">
                Selanjutnya &raquo;
            </a>
        </div>
    </div>
</section>

@endsection