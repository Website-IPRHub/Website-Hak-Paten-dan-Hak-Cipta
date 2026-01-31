@extends('layouts.app')

@section('title','Sukses')

@section('content')
<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box peralihan-box">
      <h2 class="peralihan-title">Proses Isi Formulir Selesai</h2>

      <div class="peralihan-desc">
        <p>Lanjutkan ke Proses Verifikasi dengan mengirimkan dokumen-dokumen berikut dalam bentuk word: <br>
            1. Formulir Permohonan Pendaftaran Ciptaan<br>
            2. Surat Pernyataan Hak Cipta<br>
            3. Surat Pengalihan Hak Cipta<br>
        </p>
      </div>
        <a href="{{ route('datadiricipta')}}" class="btn-verif">
  Lanjut ke Proses Verifikasi Dokumen
</a>


    </div>
  </div>
</section>
@endsection
