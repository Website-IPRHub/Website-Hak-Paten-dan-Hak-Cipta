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
    </div>

    <div class="judul-right">
      <div class="profile-badge">
        <span>Kelengkapan Pendaftaran: {{ $percent }}%</span>
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
    <ul class="paten-steps">
      <li class="paten-step {{ $activeStep == 1 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta') }}">Data Pemohon</a>
      </li>

      <li class="paten-step {{ $activeStep == 2 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('draftpaten') }}">Draft Paten</a>
      </li>

      <li class="paten-step {{ $activeStep == 3 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('formulirpermohonan') }}">Formulir Permohonan</a>
      </li>

      <li class="paten-step {{ $activeStep == 4 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('kepemilikaninvensi') }}">Kepemilikan Invensi</a>
      </li>

      <li class="paten-step {{ $activeStep == 5 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('pengalihanhak') }}">Pengalihan Hak</a>
      </li>

      <li class="paten-step {{ $activeStep == 6 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('scanktp') }}">Scan KTP</a>
      </li>

      <li class="paten-step {{ $activeStep == 7 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('tandaterima') }}">Tanda Terima</a>
      </li>

      <li class="paten-step {{ $activeStep == 8 ? 'is-active' : 'is-disabled' }}">
        <a href="#">Upload Gambar Prototipe (Jika Ada)</a>
      </li>

      <li class="paten-step {{ $activeStep == 9 ? 'is-active' : 'is-disabled' }}">
        <a href="#">Deskripsi singkat prototipe/produk (Jika Ada)</a>
      </li>
    </ul>
  </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const wrap = document.querySelector('.paten-steps');
  const active = document.querySelector('.paten-steps .paten-step.is-active');

  if (!wrap || !active) return;

  active.scrollIntoView({
    behavior: 'smooth',
    inline: 'center',
    block: 'nearest'
  });

  wrap.addEventListener('wheel', (e) => {
    if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
      e.preventDefault();
      wrap.scrollLeft += e.deltaY;
    }
  }, { passive: false });
});
</script>
