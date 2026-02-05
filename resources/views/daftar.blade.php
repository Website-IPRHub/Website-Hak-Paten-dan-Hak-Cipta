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
        <h2>Alur Pendaftaran</h2>
        <div class="flow">
            <div class="step">
            <span class="dot"></span>
            <p>Datang Langsung ke Gedung <br>Perpustakaan Universitas Diponegoro (UNDIP)<br>Lantai 5 Innovation Hub</p>
        </div>
    </div>
   </div>

   
   <div class="cta-row">
      <div class="panduan-hki">
        <h2>
          <a href="https://drive.google.com/file/d/1eLTA7Uw_9ykRL43qV6G7wLcHZA2nlVdK/view" target="_blank">
            Panduan Kekayaan Intelektual
          </a>
        </h2>
      </div>

      <div class="button-daftar-ptn">
        <h3>Siap Daftarkan Karya Anda?</h3>
        <a href="#" class="btn-dftr">Daftar Karya</a>
      </div>
    </div>

  </div>
</div>

@endsection