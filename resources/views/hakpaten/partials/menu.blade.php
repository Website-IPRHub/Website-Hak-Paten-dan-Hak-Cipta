@php
  $totalSteps = 9;
  $percent = (int) round(($activeStep / $totalSteps) * 100);
@endphp


<section class="section-full section-judul">
  <div class="section-inner judul-inner">

    <div class="judul-left">
      <h1 class="judul-title">
        <i class="bi bi-file-earmark-text-fill"></i> Sistem Pendaftaran Paten
      </h1>
      <p class="judul-desc">
        Sistem menerapkan alur sekuensial, di mana setiap tahapan wajib diselesaikan secara berurutan<br>
        dan tidak tersedia fitur kembali ke tahapan sebelumnya.
      </p>
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
        <a href="{{ route('hakcipta') }}">Data Pemohon</a>
      </li>

      <li class="step {{ $activeStep == 2 ? 'active' : 'disabled' }}">
        <a href="{{ route('draftpaten') }}">Draft Paten</a>
      </li>

      <li class="step {{ $activeStep == 3 ? 'active' : 'disabled' }}">
        <a href="{{ route('formulirpermohonan') }}">Formulir Permohonan</a>
      </li>

      <li class="step {{ $activeStep == 4 ? 'active' : 'disabled' }}">
        <a href="{{ route('kepemilikaninvensi') }}">Kepemilikan Invensi</a>
      </li>

      <li class="step {{ $activeStep == 5 ? 'active' : 'disabled' }}">
        <a href="{{ route('pengalihanhak') }}">Pengalihan Hak</a>
      </li>

      <li class="step {{ $activeStep == 6 ? 'active' : 'disabled' }}">
        <a href="{{ route('scanktp') }}">Scan KTP</a>
      </li>

      <li class="step {{ $activeStep == 7 ? 'active' : 'disabled' }}">
        <a href="{{ route('tandaterima') }}">Tanda Terima</a>
      </li>

      <li class="step {{ $activeStep == 8 ? 'active' : 'disabled' }}">
        <a href="#">Upload Gambar Prototipe (Jika Ada)</a>
      </li>

      <li class="step {{ $activeStep == 9 ? 'active' : 'disabled' }}">
        <a href="#">Deskripsi singkat prototipe/produk (Jika Ada)</a>
      </li>
    </ul>
  </div>
</section>