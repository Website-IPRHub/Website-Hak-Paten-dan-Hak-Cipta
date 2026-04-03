@extends('layouts.app')

@section('title', 'Edit Deskripsi Singkat Prototipe')

@vite([
  'resources/css/dashboardpemohon.css',
  'resources/js/app.js',
])

@section('content')
<main class="pd-main">
  <div class="pd-container" style="max-width:900px;">

    <div class="pd-topbar">
      <div>
        <h2 class="pd-title">Edit Deskripsi Singkat Prototipe</h2>
        <div class="pd-sub">{{ $judul ?? '-' }}</div>
      </div>
    </div>

    <section class="pd-card pd-card--full">
      <div class="pd-card-head">
        <div class="pd-card-title">Perbaiki Teks</div>
        <div class="pd-note">Ubah deskripsi singkat sesuai catatan revisi admin.</div>
      </div>

      <form method="POST" action="{{ route('pemohon.paten.update_deskripsi') }}">
        @csrf
        <input type="hidden" name="ref" value="{{ $ref }}">

        <div style="margin-top:16px;">
          <label for="deskripsi_singkat_prototipe" style="display:block; font-weight:700; margin-bottom:8px;">
            Deskripsi Singkat Prototipe
          </label>

          <textarea
            id="deskripsi_singkat_prototipe"
            name="deskripsi_singkat_prototipe"
            rows="10"
            style="width:100%; border:1px solid #d6deea; border-radius:12px; padding:14px; font-size:15px; line-height:1.6;"
          >{{ old('deskripsi_singkat_prototipe', $deskripsi ?? '') }}</textarea>

          @error('deskripsi_singkat_prototipe')
            <div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>
          @enderror
        </div>

        <div style="display:flex; gap:10px; margin-top:18px;">
          <a href="{{ route('pemohon.dashboard') }}" class="pd-mini-btn" style="text-decoration:none;">
            Batal
          </a>

          <button type="submit" class="pd-mini-btn primary">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </section>

  </div>
</main>
@endsection