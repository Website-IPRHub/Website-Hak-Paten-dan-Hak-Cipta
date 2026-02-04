@extends('layouts.app')

@section('title','Landing Page')

@section('content')

<div class="content-y">
  <div class="hero">
  <img src="{{ asset('images/bg2.jpg') }}" class="hero-img" alt="gedung">

  <div class="hero-text">
    <h1>Selamat Datang</h1>
    <h2>di KI Hub Universitas Diponegoro</h2>
  </div>
</div>

  <div class="hero-daftar">
    <h3>Ayo daftarkan KI Anda</h3>
    <div class="hero-buttons-center icon-menu">
      <div class="hero-buttons-center icon-menu">
  <a href="{{ route('menuhakpaten') }}" class="icon-btn paten">
    <span class="icon"><i class="fa-solid fa-lightbulb"></i></span>
    <span class="label">Daftar Paten</span>
  </a>

  <a href="{{ route('menucipta') }}" class="icon-btn cipta">
    <span class="icon"><i class="fa-regular fa-copyright"></i></span>
    <span class="label">Daftar Hak Cipta</span>
  </a>

  <a href="{{ route('menucipta') }}" class="icon-btn industri">
    <span class="icon"><i class="fa-solid fa-pen-ruler"></i></span>
    <span class="label">Daftar Desain Industri</span>
  </a>

  <a href="{{ route('menucipta') }}" class="icon-btn dtlst">
    <span class="icon"><i class="fa-solid fa-microchip"></i></span>
    <span class="label">Daftar Desain Tata Letak<br/>Sirkuit Terpadu</span>
  </a>

  <a href="{{ route('menucipta') }}" class="icon-btn rahasia">
    <span class="icon"><i class="fa-solid fa-user-secret"></i></span>
    <span class="label">Daftar Rahasia Dagang</span>
  </a>
</div>

    <!-- CARD PUTIH DI TENGAH GAMBAR -->
  <div class="hero-card">
    <h1>Tentang KI Hub</h1>
    <p>
      Bagian ini adalah abstrak yang digunakan sebagai sarana untuk menyampaikan benang merah dari isi laporan PKL jd ini adalah pppppp dsakajdijaeiorioeqiro kjHADsioqheroqoeur kwhdwqhruhqur akawhruhqweroieqjoijqeio IWIHRIOQHOIRHOIQHER IWEQROIEIQRIOEQHIOHIOEJ FIPWEJOIEHWTJWEOIJWTEEW EAOIFHWEIOTH9Een oiehoiehrreoiwqrj iehoiwtho8eeoi
    </p>
  </div>

  <div class="hero-search">
    <form action="{{ route('tracking') }}" method="GET" class="search-form">
      <input type="text" name="q" class="search-input" placeholder="Masukkan No Pengajuan (contoh: EC00XXXXXXXXX)" value="{{ request('q') }}" required>
      <button type="submit" class="search-btn">Cek Status Verifikasi</button>
    </form>
  </div>
  </div>
  <div class="container-langkah">
    <div class="container-langkah-langkah">
        <h3>Langkah-Langkah Pendaftaran Paten / Hak Cipta</h3>
        <p>1. Lengkapi persyaratan pendaftaran Paten atau Hak Cipta sesuai yang tertera pada menu.</p>
        <p>2. Print/cetak draft Paten/Hak Cipta yang telah diisi (tanpa meterai dan ttd), kemudian diajukan ke Bagian Inovasi, Gedung UPT Perpustakaan Lt 5 untuk diverifikasi kelengkapannya (mohon membawa laptop untuk koreksi di tempat).</p>
        <p>3. Draft berkas Paten/Hak Cipta yang telah diverifikasi, mohon dilengkapi ttd semua inventor/pencipta dan meterai dan dikumpulkan ke Bagian Inovasi, Gedung UPT Perpustakaan lt 5, Pada jam kerja berikut Senin – Kamis 09.00 – 12.00 & 13.00 – 15.00 WIB dan Jumat 09.00 – 11.30 & 13.30 – 15.30 WIB (*Jam Pelayanan dapat berubah sewaktu waktu).</p>
        <p>4. Jika telah mendapat nomor urut, mohon mengisi kelengkapan data dan upload softfile pengajuan (tanpa meterai dan tanpa ttd) pada menu yang tersedia diatas. </p>
        <p>5. Surat Catatan Ciptaan Kekayaan Intelektual akan dikirimkan melalui email masing – masing pencipta.</p>
    </div>
  </div>
  <div class="container-catatan-awal">
    <img src="{{ asset('images/lampu.svg') }}" class="lampu-img" alt="lampu">
    <div class="container-catatan">
        <h3>Catatan: </h3>
        <p>1. Semua dokumen yang dibutuhkan dapat diunduh dan diisi sesuai dengan data yang benar pada menu yang tersedia di atas.</p>
        <p>2. Nama Inventor/Pencipta DOSEN disesuaikan dengan gelar di E-Duk.</p>
        <p>3. Nama Inventor/Pencipta MAHASISWA disesuaikan dengan SIAP.</p>
        <p>4. Kolom NIP/NIM pada semua Inventor WAJIB diisi.</p>
        <p>5. Dibuat Rangkap 1 bermaterai.</p>
        <p>6. Di Print dengan Kertas A4 80 gram.</p>
        <p>7. Data dan Tanda Tangan pada Surat Pernyataan dan Surat Pengalihan Hak dalam 1 halaman.</p>
        <p>8. Informasi lebih lanjut dapat menghubungi melalui Call Center 0811-3848-555 pada hari-jam kerja 07.30-16.00 WIB.</p>
    </div>
  </div>
</div>
@endsection

