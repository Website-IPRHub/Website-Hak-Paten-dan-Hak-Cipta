@php
  $totalSteps = 7;
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
      <p class="judul-desc">Verifikasi & lengkapi dokumen hak cipta di sini.</p>
    </div>

    <div class="judul-right">
      <div class="profile-badge">
        <span>Kelengkapan Profile: {{ $percent }}%</span>
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
        <a href="{{ route('datadiricipta') }}">Data Pemohon</a>
      </li>

      {{-- 2-8: butuh verifId --}}
      <li class="step-tab {{ $activeStep == 2 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('ciptaverif.formulirpermohonan', ['verif' => $verifId]) : 'javascript:void(0)' }}">Form Permohonan</a>
      </li>

      <li class="step-tab {{ $activeStep == 3 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('ciptaverif.suratpernyataan', ['verif' => $verifId]) : 'javascript:void(0)' }}">Surat Pernyataan</a>
      </li>

      <li class="step-tab {{ $activeStep == 4 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('ciptaverif.suratpengalihan', ['verif' => $verifId]) : 'javascript:void(0)' }}">Pengalihan Hak</a>
      </li>

      <li class="step-tab {{ $activeStep == 5 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('ciptaverif.scanktp', ['verif' => $verifId]) : 'javascript:void(0)' }}">Scan KTP</a>
      </li>

      <li class="step-tab {{ $activeStep == 6 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="{{ $verifId ? route('ciptaverif.hasilciptaan', ['verif' => $verifId]) : 'javascript:void(0)' }}">Hasil Ciptaan</a>
      </li>

      <li class="step-tab {{ $activeStep == 7 ? 'active' : ($verifId ? '' : 'disabled') }}">
        <a href="#">Link Ciptaan</a>
      </li>
    </ul>
  </div>
</section>
