@extends('layouts.app')

@section('title','Sukses')

@section('content')
<section class="section-full section-content success-page">
  <div class="section-inner">
    <div class="submit-result">

      <h2>Pendaftaran Hak Cipta Berhasil</h2>

      <p class="desc">
        Terima kasih. Data pendaftaran hak cipta Anda telah berhasil dikirim.
      </p>

      <div class="submit-actions">
        <a href="{{ url('/header') }}" class="btn-primary">Kembali ke Beranda</a>
      </div>

    </div>
  </div>
</section>

@endsection
