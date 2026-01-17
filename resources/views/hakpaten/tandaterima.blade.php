@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 7; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="tanda-terima">
                <h2>Surat Tanda Terima Berkas *</h2>
                <p>*file dalam bentuk PDF<br>
*berlaku mulai hari Senin, 1 Juli 2024<br>
*surat tanda terima dapat didownload di <a href="biks.undip.ac.id/download">biks.undip.ac.id/download</a></p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-unduh">
                    <a href="{{ route('download.template.Surat Tanda Terima Berkas')}}" class="btn-template-surat-tanda-terima-berkas-hki">Unduh Surat Tanda Terima Berkas</a>
                </div>
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('tandaterima.uploadFormSuratTandaTerimaBerkas') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden>
                        <button id="uploadButton" for="draftFile">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('uploadgambarprototipe') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>

@endsection