@extends('layouts.app')

@section('title','Sukses')

@section('content')
<section class="section-full section-content success-page">
  <div class="section-inner">
    <div class="submit-result">

      <h2>Pendaftaran Hak Cipta Berhasil</h2>

      <p class="desc">
        Terima kasih. Data pendaftaran hak cipta Anda telah berhasil dikirim.
          Silakan simpan kode pengajuan berikut untuk keperluan tracking. dikirim.
      </p>

      <div class="submit-actions">
        <a href="{{ url('/header') }}" class="btn-primary">Kembali ke Beranda</a>
        <a href="{{ url('/pemohon/login') }}" class="btn-secondary">Login</a>
      </div>

    </div>
  </div>
</section>

@endsection
