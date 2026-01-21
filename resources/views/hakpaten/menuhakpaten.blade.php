@extends('layouts.app')

@section('title','Test Header')

@section('content')

<div class="hero-layout">
  <div class="bg">
    <img src="{{ asset('images/logoUNDIP.png') }}" class="img-undip" alt="undip">
    <img src="{{ asset('images/Logo Dirinovki 2026.jpg') }}" class="hero-img2" alt="dirinovki">
  </div>

  <div class="right">
    <div class="menu-hak-paten">
        <h2>Alur Pendaftaran Paten</h2>
        <div class="flow">
            <div class="step">
            <span class="dot"></span>
            <p>Isi Formulir</p>
        </div>

        <div class="step">
            <span class="dot"></span>
            <p>Verifikasi Berkas</p>
        </div>

        <div class="step">
            <span class="dot"></span>
            <p>Pendaftaran Paten</p>
        </div>
    </div>
   </div>

    <div class="button-daftar-ptn">
      <h3>Siap Daftarkan Paten Anda?</h3>
      <a href="#" class="btn-dftr">Daftar Paten</a>
    </div>
  </div>
</div>

@endsection