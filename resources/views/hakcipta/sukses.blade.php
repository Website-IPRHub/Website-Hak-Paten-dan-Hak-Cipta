@extends('layouts.app')

@section('title','Sukses')

@section('content')
<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box success-box">
      <h2 class="success-title">Submit Berhasil</h2>

      <p class="success-desc">Data pendaftaran cipta sudah terkirim.</p>

      @if(session('success'))
        <p class="success-flash">{{ session('success') }}</p>
      @endif

      <a href="{{ url('/header') }}" class="btn-selanjutnya">Kembali ke Landing Page</a>
    </div>
  </div>
</section>
@endsection
