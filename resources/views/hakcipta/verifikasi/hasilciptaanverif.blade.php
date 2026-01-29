@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 6; @endphp
@include('hakcipta.verifikasi.menuciptaverif')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-isi">
      <div class="kepemilikan-invensi">
        <h2>Hasil Ciptaan *</h2>
        <p>*Ukuran Maksimal File 10Mb<br>
        *Khusus jenis Karya Rekaman Video mengupload screenshoot video (pdf)
        </p>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">
          <form
            id="draftForm"
            action="{{ route('ciptaverif.upload.hasilciptaan', ['verif' => $verif->id]) }}"
            method="POST"
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
              data-allowed="pdf"
              data-max-mb="10"
            >

            <button id="uploadButton" type="button" class="btn-upload">Upload</button>

            <span id="fileName" class="file-name">
                @if($verif->hasil_ciptaan)
                    {{ basename($verif->hasil_ciptaan) }}
                @else
                    Belum pilih file
                @endif
            </span>


            {{-- kalau JS kamu butuh tombol submit tersembunyi --}}
            <button id="submitUpload" type="submit" hidden>Kirim</button>

            {{-- tempat error kalau JS kamu pakai --}}
            <div id="fileError" style="display:none; margin-top:8px; color:#dc2626; font-weight:600;">
              Tipe file tidak sesuai.
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="actions-bar">
      <button type="button" class="btn-prev"
        data-fallback="{{ route('ciptaverif.scanktp', ['verif' => $verif->id]) }}"
        onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)">
        &laquo; Sebelumnya
      </button>

      <a class="btn-next" href="{{ route('ciptaverif.linkciptaan',['verif' => $verif->id])}}">
        Selanjutnya &raquo;
      </a>
    </div>

  </div>
</section>

@endsection
