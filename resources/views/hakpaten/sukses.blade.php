@extends('layouts.app')

@section('title','Pengajuan Berhasil')

@section('content')
<section class="section-full section-content success-page">
  <div class="section-inner">
    <div class="submit-result">

      <h2>Pengajuan Paten Berhasil</h2>

      @if(session('success'))
        <p class="success-flash">{{ session('success') }}</p>
      @else
        <p class="desc">
          Terima kasih. Data pendaftaran paten Anda telah berhasil dikirim.
          Silakan simpan kode pengajuan berikut untuk keperluan tracking.
        </p>
      @endif

      <div class="submit-actions">
        <a href="{{ url('/header') }}" class="btn-primary">Kembali ke Beranda</a>
      </div>

    </div>
  </div>
</section>
@endsection
