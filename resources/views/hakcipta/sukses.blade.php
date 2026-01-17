@extends('layouts.app')

@section('title','Sukses')

@section('content')
<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box success-box">
      <h2 class="success-title">Submit Berhasil</h2>

      <div class="success-desc">
        <p>Data pendaftaran cipta sudah terkirim.</p>
        <p>No Pendaftaran anda:</p>
        <h3>{{ session('no_pendaftaran') }}</h3>
      </div>

      @if(session('success'))
        <p class="success-flash">{{ session('success') }}</p>
      @endif

      <br><a href="{{ url('/header') }}" class="btn-selanjutnya">Kembali ke Landing Page</a>
    </div>
  </div>
</section>
@endsection
