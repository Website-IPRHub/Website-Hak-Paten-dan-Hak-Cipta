@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 5 @endphp
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
                <div class="button-unduh">
                    <a href="{{ route('hakcipta.download.template.tandaterima') }}" class="btn-download">Download Surat Tanda Terima Berkas (PDF)</a>
                </div>
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('hakcipta.tandaterima.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden>
                        <button id="uploadButton" for="draftFile">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('hakcipta.scanktp') }}" class="btn-selanjutnya is-disabled">Selanjutnya</a>
        </div>
    </div>
</section>

@endsection