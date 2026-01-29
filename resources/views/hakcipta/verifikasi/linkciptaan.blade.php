@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 7; @endphp
@include('hakcipta.verifikasi.menuciptaverif')

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box">

      <div class="kepemilikan-invensi">
        <h2>Link Ciptaan</h2>
        <p>*Link Ciptaan untuk Hak Cipta jenis Karya Rekaman Video</p>
      </div>

      {{-- ERROR --}}
      @if ($errors->any())
        <div style="margin:10px 0; padding:10px; border:1px solid #fca5a5; background:#fee2e2; border-radius:10px;">
          <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form
  action="{{ route('ciptaverif.submit.final', $verif->id) }}"
  method="POST"
>
  @csrf

  <input
    type="url"
    name="link_ciptaan"
    class="input-link-ciptaan"
    placeholder="https://contoh.com/karya-cipta"
    value="{{ old('link_ciptaan', $verif->link_ciptaan) }}"
  >

  <div class="btn-center">
    <button type="submit" class="btn-selanjutnya-submit">
      Simpan & Kirim
    </button>
  </div>
</form>

    </div>
  </div>
</section>

@endsection
