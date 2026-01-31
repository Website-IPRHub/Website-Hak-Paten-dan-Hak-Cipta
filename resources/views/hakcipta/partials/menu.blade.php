@php
  $totalSteps = 8; // kamu punya 8 step di menu (1-8)
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
    <ul class="paten-steps">
      <li class="paten-step {{ $activeStep == 1 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta') }}">Data Pemohon</a>
      </li>

      <li class="paten-step {{ $activeStep == 2 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta.permohonanpendaftaran') }}">
          Surat Permohonan Pendaftaran Ciptaan
        </a>
      </li>

      <li class="paten-step {{ $activeStep == 3 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta.suratpernyataan') }}">Surat Pernyataan</a>
      </li>

      <li class="paten-step {{ $activeStep == 4 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta.pengalihanhak') }}">Surat Pengalihan Hak Cipta</a>
      </li>

      <li class="paten-step {{ $activeStep == 5 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta.scanktp') }}">Scan KTP</a>
      </li>

      <li class="paten-step {{ $activeStep == 6 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta.tandaterima') }}">Surat Tanda Terima Berkas</a>
      </li>

      <li class="paten-step {{ $activeStep == 7 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta.hasilciptaan') }}">Hasil Ciptaan</a>
      </li>

      <li class="paten-step {{ $activeStep == 8 ? 'is-active' : 'is-disabled' }}">
        <a href="{{ route('hakcipta.linkciptaan') }}">
          Link Ciptaan untuk Hak Cipta jenis Karya Rekaman Video
        </a>
      </li>
    </ul>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const wrap = document.querySelector('.paten-steps');
  const active = document.querySelector('.paten-steps .paten-step.is-active');

  if (!wrap || !active) return;

  // scroll ke step aktif (tengah-ish)
  active.scrollIntoView({
    behavior: 'smooth',
    inline: 'center',
    block: 'nearest'
  });

  // Optional: kalau user scroll mouse wheel, geser horizontal (enak di desktop)
  wrap.addEventListener('wheel', (e) => {
    if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
      e.preventDefault();
      wrap.scrollLeft += e.deltaY;
    }
  }, { passive: false });
});
</script>
