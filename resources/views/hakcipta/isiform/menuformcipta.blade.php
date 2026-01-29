@php
  $totalSteps = 3;
  $percent = (int) round(($activeStep / $totalSteps) * 100);
@endphp


<section class="section-full section-judul">
  <div class="section-inner judul-inner">

    <div class="judul-left">
      <h1 class="judul-title">
        <i class="bi bi-file-earmark-text-fill"></i> Sistem Pendaftaran Hak Cipta
      </h1>
    </div>

    <div class="judul-right">
      <div class="profile-badge">
        <span>Kelengkapan Profile: {{ $percent }}%</span>
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
  <a href="{{ route('hakcipta.isiform.formpendaftaran') }}">Formulir Permohonan Pendaftaran Ciptaan</a>
</li>

<li class="step {{ $activeStep == 2 ? 'active' : 'disabled' }}">
  <a href="{{ route('hakcipta.isiform.suratpernyataan') }}">Surat Pernyataan</a>
</li>

<li class="step {{ $activeStep == 3 ? 'active' : 'disabled' }}">
  <a href="{{ route('hakcipta.isiform.pengalihanhak') }}">Pengalihan Hak</a>
</li>

    </ul>
  </div>
</section>