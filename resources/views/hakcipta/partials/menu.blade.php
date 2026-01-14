@php
  $totalSteps = 9;
  $percent = (int) round(($activeStep / $totalSteps) * 100);
@endphp


<section class="section-full section-judul">
  <div class="section-inner judul-inner">
    <h1 class="judul-title">
      <i class="bi bi-file-earmark-text-fill"></i> Sistem Pendaftaran Hak Cipta
    </h1>

    <div class="profile-badge">
      <span>Kelengkapan Profile: {{ $percent }}%</span>
      <i class="bi bi-info-circle-fill"></i>
    </div>
    <div class="profile-progress" aria-label="Progress kelengkapan profile">
      <div class="profile-progress__bar" style="--w: {{$percent}}%;"></div>
    </div>

  </div>
</section>

<section class="section-full section-steps">
  <div class="section-inner">
    <ul class="menu-steps">
      <li class="step {{ $activeStep == 1 ? 'active' : 'disabled' }}">
        <a href="{{ route('hakpaten') }}">Data Pemohon</a>
      </li>

      <li class="step {{ $activeStep == 2 ? 'active' : 'disabled' }}">
        <a href="{{ route('draftpaten') }}">Surat Permohonan Pendaftaran Ciptaan</a>
      </li>

      <li class="step {{ $activeStep == 3 ? 'active' : 'disabled' }}">
        <a href="{{ route('formulirpermohonan') }}">Surat Pernyataan</a>
      </li>

      <li class="step {{ $activeStep == 4 ? 'active' : 'disabled' }}">
        <a href="{{ route('kepemilikaninvensi') }}">Surat Pengalihan Hak Cipta</a>
      </li>

      <li class="step {{ $activeStep == 5 ? 'active' : 'disabled' }}">
        <a href="{{ route('pengalihanhak') }}">Surat Tanda Terima Berkas</a>
      </li>

      <li class="step {{ $activeStep == 6 ? 'active' : 'disabled' }}">
        <a href="{{ route('scanktp') }}">Scan KTP</a>
      </li>

      <li class="step {{ $activeStep == 7 ? 'active' : 'disabled' }}">
        <a href="{{ route('tandaterima') }}">Hasil Ciptaan
</a>
      </li>

      <li class="step {{ $activeStep == 8 ? 'active' : 'disabled' }}">
        <a href="#">Link Ciptaan untuk Hak Cipta jenis Karya Rekaman Video</a>
      </li>
    </ul>
  </div>
</section>

