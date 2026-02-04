@php
  $totalSteps = 8;
  $activeStep = $activeStep ?? 1;

  // $verif cuma ada di step 2-8
  $verifId = $verif->id ?? null;

  $percent = (int) round(($activeStep / $totalSteps) * 100);
@endphp

<section class="section-full section-judul">
  <div class="section-inner judul-inner">
    <div class="judul-left">
      <h1 class="judul-title">
        <i class="bi bi-file-earmark-text-fill"></i> Sistem Verifikasi Paten
      </h1>
      <p class="judul-desc">Verifikasi & lengkapi dokumen paten di sini.</p>
    </div>

    <div class="judul-right">
      <div class="profile-badge">
        <span>Kelengkapan Verifikasi Dokumen: {{ $percent }}%</span>
        <i class="bi bi-info-circle-fill"></i>
      </div>

      <div class="profile-progress" aria-label="Progress kelengkapan profile">
        <div class="profile-progress__bar" style="--w: {{ $percent }}%;"></div>
      </div>
    </div>
  </div>
</section>

<section class="section-full section-steps">
  <div class="section-inner">
    <ul class="menu-steps">

      {{-- 1: Data diri (ga butuh verifId) --}}
      <li class="step-tab {{ $activeStep == 1 ? 'active' : '' }}">
        <a href="{{ route('patenverif.datadiri') }}">Data Pemohon</a>
      </li>

      {{-- 2-8: butuh verifId --}}
      <li class="step-tab {{ $activeStep == 2 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('patenverif.draft', ['verif' => $verifId]) : 'javascript:void(0)' }}">Draft Paten</a>
      </li>

      <li class="step-tab {{ $activeStep == 3 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('patenverif.formpermohonan', ['verif' => $verifId]) : 'javascript:void(0)' }}">Form Permohonan</a>
      </li>

      <li class="step-tab {{ $activeStep == 4 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('patenverif.invensi', ['verif' => $verifId]) : 'javascript:void(0)' }}">Invensi</a>
      </li>

      <li class="step-tab {{ $activeStep == 5 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('patenverif.pengalihanhak', ['verif' => $verifId]) : 'javascript:void(0)' }}">Pengalihan Hak</a>
      </li>

      <li class="step-tab {{ $activeStep == 6 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('patenverif.scanktp', ['verif' => $verifId]) : 'javascript:void(0)' }}">Scan KTP</a>
      </li>

      <li class="step-tab {{ $activeStep == 7 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('patenverif.uploadgambar', ['verif' => $verifId]) : 'javascript:void(0)' }}">Upload Gambar</a>
      </li>

      <li class="step-tab {{ $activeStep == 8 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('patenverif.deskripsi', ['verif' => $verifId]) : 'javascript:void(0)' }}">Deskripsi Produk</a>
      </li>

    </ul>
  </div>
</section>
