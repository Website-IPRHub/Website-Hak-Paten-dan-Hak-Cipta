@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 1; @endphp
@include('hakpaten.isiformulir.menuformulir')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box peralihan-box">
      <h2 class="peralihan-title">Dokumen Persyaratan Isi Formulir dan Verifikasi Berkas</h2>

      <div class="peralihan-desc">
        <p>Mohon lengkapi dokumen-dokumen berikut sebelum Anda melanjutkan ke proses isi formulir dan verifikasi berkas. <br>
            1. Draft Paten (<a href="{{ route('download.template.draftpaten') }}"
            class="btn-template-draft-paten" 
            style="display:inline-block; text-decoration:none; color:#0000EE;">⬇ Unduh Template Draft Paten di sini </a>)<br>
            2. Formulir Permohonan<br>
            3. Surat Pernyataan Kepemilikan Invensi oleh Inventor<br>
            4. Surat Pengalihan<br>
            5. Scan KTP<br>
            6. Gambar Prototipe<br>
            7. Surat Pernyataan (Jika memilih skema penelitian pengembangan (TKT 7-9))
        </p>
        <h1>Catatan: Mohon untuk merapikan kembali dokumen sebelum dikirimkan ke proses verifikasi, serta mempersiapkan materai khusus bagi yang memilih skema penelitian pengembangan.</h1>
  </div>
   <a href="{{ route('hakpaten.isiformulir')}}" class="btn-verif">Mulai Proses </a>
</section>
@endsection
