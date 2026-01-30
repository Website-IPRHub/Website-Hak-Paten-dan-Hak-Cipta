@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 8; @endphp
@include('hakcipta.partials.menu')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box">

      <div class="link-ciptaan">
        <h2>Link Ciptaan</h2>
      </div>

      @if ($errors->any())
        <div style="margin:10px 0; padding:10px; border:1px solid #fca5a5; background:#fee2e2; border-radius:10px;">
          <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif


      <form action="{{ route('hakcipta.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="cipta_id" value="{{ session('cipta_id') }}">
        <input type="url" name="link" class="input input-link" placeholder="Masukkan link ciptaan">

        <div class="btn-center">
          <button type="submit" class="btn-selanjutnya-submit">Submit</button>
          <button
            type="button"
            class="btn-prev-desk"
            data-fallback="{{ route('hakcipta.hasilciptaan') }}"
            onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
          >
            &laquo; Sebelumnya
          </button>
        </div>
      </form>

    </div>
  </div>
</section>

@endsection
