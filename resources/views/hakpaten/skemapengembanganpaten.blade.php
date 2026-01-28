@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box">

      <div class="skema-pengembangan">
        <h2>Surat Pernyataan TKT 7–9 *</h2>
        <p>File dalam bentuk Word</p>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">
            <form id="draftForm"
                action="{{ route('hakpaten.skema.upload', ['paten' => $paten->id]) }}"
                method="POST"
                enctype="multipart/form-data"
                data-upload-form
            >
                @csrf

                <input
                type="file"
                name="file"
                hidden
                required
                data-allowed="doc,docx"
                data-max-mb="10"
                >

                <button type="button" class="btn-upload" data-btn-pick>
                    Pilih File
                </button>

                <span class="file-name" data-file-name>
                    Belum pilih file
                </span>
            </form>
        </div>
      </div>
    </div>

    <div class="actions-bar">
       <button type="button" class="btn-prev" onclick="history.back()">
           &laquo; Sebelumnya
        </button>
        <button
            type="submit"
            class="btn-selanjutnya"
            form="draftForm"
            data-btn-submit
            disabled
            >
            Selanjutnya &raquo;
        </button>

    </div>

  </div>
</section>

@endsection
