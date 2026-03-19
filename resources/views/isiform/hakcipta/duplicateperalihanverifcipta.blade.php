@extends('layouts.app')

@section('title','Sukses')

@section('content')

@php $activeStep = 1; @endphp
@include('hakcipta.isiform.menuformcipta')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box peralihan-box">
      <h2 class="peralihan-title">Proses Isi Formulir dan Verifikasi Dokumen</h2>

      <div class="peralihan-desc">
        <p>Lanjutkan ke Proses Verifikasi dengan mengirimkan dokumen-dokumen berikut dalam bentuk word: <br>
            1. Formulir Permohonan Pendaftaran Ciptaan<br>
            2. Surat Pernyataan Hak Cipta<br>
            3. Surat Pengalihan Hak Cipta<br>
            4. Scan KTP<br>
            5. Hasil Ciptaan<br>
        </p>
        <h1>Catatan: Mohon rapikan kembali dokumen sebelum dikirimkan ke proses verifikasi.</h1>
      </div>
        <a href="{{ route('isiform.hakcipta.duplicateformpendaftaran')}}" class="btn-verif">
  Mulai Proses 
</a>


    </div>
  </div>
</section>
@endsection
