@php
  $totalSteps = 4;
  $percent = (int) round(($activeStep / $totalSteps) * 100);
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
        <span>Kelengkapan Formulir: {{ $percent }}%</span>
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
        <a href="{{ route('hakcipta') }}">Draft Paten</a>
      </li>

      <li class="step {{ $activeStep == 2 ? 'active' : 'disabled' }}">
        <a href="{{ route('draftpaten') }}">Formulir Permohonan</a>
      </li>

      <li class="step {{ $activeStep == 3 ? 'active' : 'disabled' }}">
        <a href="{{ route('formulirpermohonan') }}">Kepemilikan Invensi</a>
      </li>

      <li class="step {{ $activeStep == 4 ? 'active' : 'disabled' }}">
        <a href="{{ route('kepemilikaninvensi') }}">Pengalihan Hak</a>
      </li>
    </ul>
  </div>
</section>