@extends('layouts.app')

@section('title','Sukses')

@section('content')

@php $activeStep = 1; @endphp
@include('isiform.paten.duplicatemenuformulir')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box peralihan-box">
      <h2 class="peralihan-title">Proses Isi Formulir dan Verifikasi Dokumen</h2>

      <div class="peralihan-desc">
        <p>Lanjutkan ke Proses Verifikasi dengan mengirimkan dokumen-dokumen berikut dalam bentuk word: <br>
            1. Draft Paten (sudah diisi)<br>
            2. Formulir Permohonan<br>
            3. Surat Pernyataan Kepemilikan Invensi oleh Inventor<br>
            4. Surat Pengalihan<br>
            5. Scan KTP<br>
            6. Gambar Prototipe<br>
            7. Surat Pernyataan (Jika memilih skema penelitian pengembangan (TKT 7-9))
        </p>
        <h1>Catatan: Mohon rapikan kembali dokumen sebelum dikirimkan ke proses verifikasi.</h1>
      </div>
      <a href="{{ route('patenverif.datadiri') }}" class="btn-verif">Mulai Proses</a>
    </div>
  </div>
</section>
@endsection
