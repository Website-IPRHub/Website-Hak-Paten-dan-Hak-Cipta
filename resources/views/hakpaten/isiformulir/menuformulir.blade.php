@php
  $totalSteps = 4;
  $percent = (int) round(($activeStep / $totalSteps) * 100);

  $verifId = $verif->id ?? null;
@endphp


<section class="section-full section-judul">
  <div class="section-inner judul-inner">

    <div class="judul-left">
      <h1 class="judul-title">
        <i class="bi bi-file-earmark-text-fill"></i> Sistem Pendaftaran Paten
      </h1>
    </div>

    <div class="judul-right">
      <div class="profile-badge">
        <span>Kelengkapan Proses: {{ $percent }}%</span>
        <i class="bi bi-info-circle-fill"></i>
      </div>

      <div class="profile-progress" aria-label="Progress kelengkapan profile">
        <div class="profile-progress__bar" style="--w: {{$percent}}%;"></div>
      </div>
    </div>

  </div>
</section>


<section class="section-full section-steps">
  <div class="section-inner">
    <ul class="menu-steps">
      <li class="step {{ $activeStep == 1 ? 'active' : 'disabled' }}">
        <span class="step-number">1</span>
        <a href="{{ route('hakcipta') }}">Dokumen yang Diperlukan</a>
      </li>

      <li class="step {{ $activeStep == 2 ? 'active' : 'disabled' }}">
        <span class="step-number">2</span>
        <a href="{{ route('draftpaten') }}">Isi Formulir</a>
      </li>

      <li class="step {{ $activeStep == 3 ? 'active' : 'disabled' }}">
        <span class="step-number">3</span>
        <a href="{{ route('patenverif.datadiri') }}">Verifikasi Berkas</a>
      </li>

      <li class="step {{ $activeStep == 4 ? 'active' : 'disabled' }}">
        <span class="step-number">4</span>
        <a href="{{ $verifId ? route('patenverif.all', ['verif' => $verifId]) : 'javascript:void(0)' }}">Upload Berkas</a>
      </li>
    </ul>
  </div>
</section>