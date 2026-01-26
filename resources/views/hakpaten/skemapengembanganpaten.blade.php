@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="skema-pengembangan">
                <h2>Surat Pernyataan TKT 7-9 *</h2>
                <p>File dalam bentuk Word</p>
            </div>
            <div class="hero-buttons-start">
                <div class="button-upload">
                    <form id="draftForm" action="{{ route('hakpaten.skema.upload', ['paten' => $paten->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input id="draftFile" type="file" name="file" required hidden data-allowed="doc,docx" data-max-mb="10">
                        <button id="uploadButton" type="button">Upload</button>
                        <span id="fileName">Belum Pilih File</span>
                    </form>
                </div>
            </div>
        </div>
        <div class="next">
          <a id="nextLink"
            href="{{ route('draftpaten', ['paten' => $paten->id]) }}"
            class="btn-selanjutnya {{ $paten->skema_tkt_template_path ? '' : 'is-disabled' }}">
            Selanjutnya
          </a>
        </div>
    </div>
</section>

@endsection