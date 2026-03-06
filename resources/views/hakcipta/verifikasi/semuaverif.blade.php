@extends('layouts.app')
@section('title','Upload Berkas Verifikasi Paten')
@section('body-class','paten-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/paten-upload.css') }}">
@endpush

@section('content')

@php $activeStep = 4; @endphp
@include('hakpaten.isiformulir.menuformulir')

@if(session('submit_error'))
<script>
document.addEventListener('DOMContentLoaded', function () {

    let berkasList = `{!! implode('</li><li>', (array) session('submit_error')) !!}`;

    Swal.fire({
        icon: 'error',
        title: 'Berkas Belum Lengkap',
        html: `
            <div style="text-align:left">
                <small style="color:#6c757d;">
                    Silakan upload berkas di bawah ini terlebih dahulu:
                </small>
                <ul style="margin-top:10px;padding-left:18px;">
                    <li>${berkasList}</li>
                </ul>
            </div>
        `,
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#d33'
    });

});
</script>
@endif

<div class="upload-page">
  <div class="upload-wrap">

    <div class="upload-head">
      <div>
        <h1 class="upload-title">Upload Berkas Verifikasi Paten</h1>
        <p class="upload-sub">Upload satu per satu dan terakhir baru klik <b>Submit Verifikasi</b>.</p>
      </div>
    </div>

    <div class="grid">

      {{-- 1) Draft --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Formulir Permohonan Pendaftaran Ciptaan <span class="req">*</span></p>
            <p class="card-hint">DOC/DOCX/PDF • max 10MB</p>
          </div>
          <span class="status {{ $verif->draft_paten ? 'ok' : 'no' }}">
            {{ $verif->draft_paten ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('patenverif.upload.draft',['verif'=>$verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".doc,.docx,.pdf" required>

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                    @if($verif->draft_paten)
                        {{ basename($verif->draft_paten) }}
                    @else
                        Belum pilih file
                    @endif
                  </div>

                  @if($verif->draft_paten)
                      <div style="margin-top:6px;">
                          <a href="{{ Storage::url($verif->draft_paten) }}" target="_blank" class="lihat-file-link">
                              Lihat File
                          </a>
                      </div>
                  @endif
                <div class="ft">Klik Upload → pilih file → otomatis kirim</div>
              </div>
              <div class="btns">
                <button type="button" class="btn-soft upload-pick">Upload</button>
                <button type="submit" class="upload-submit" hidden>Kirim</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- 2) Form Permohonan --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Form Permohonan <span class="req">*</span></p>
            <p class="card-hint">DOC/DOCX/PDF • tanpa tanda tangan</p>
          </div>
          <span class="status {{ $verif->form_permohonan ? 'ok' : 'no' }}">
            {{ $verif->form_permohonan ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('patenverif.upload.form',['verif'=>$verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".doc,.docx,.pdf" required>

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                  @if($verif->form_permohonan)
                      {{ basename($verif->form_permohonan) }}
                  @else
                      Belum pilih file
                  @endif
                </div>

                @if($verif->form_permohonan)
                    <div style="margin-top:6px;">
                        <a href="{{ Storage::url($verif->form_permohonan) }}" target="_blank" class="lihat-file-link">
                            Lihat File
                        </a>
                    </div>
                @endif
                <div class="ft">Klik Upload → pilih file → otomatis kirim</div>
              </div>
              <div class="btns">
                <button type="button" class="btn-soft upload-pick">Upload</button>
                <button type="submit" class="upload-submit" hidden>Kirim</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- 3) Surat Kepemilikan Invensi --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Surat Pernyataan Hak Cipta <span class="req">*</span></p>
            <p class="card-hint">DOC/DOCX/PDF • max 10MB</p>
          </div>
          <span class="status {{ $verif->surat_kepemilikan ? 'ok' : 'no' }}">
            {{ $verif->surat_kepemilikan ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('patenverif.upload.invensi',['verif'=>$verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".doc,.docx,.pdf" required>

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                    @if($verif->surat_pernyataan)
                        {{ basename($verif->surat_pernyataan) }}
                    @else
                        Belum pilih file
                    @endif
                  </div>

                  @if($verif->surat_pernyataan)
                      <div style="margin-top:6px;">
                          <a href="{{ Storage::url($verif->surat_pernyataan) }}" target="_blank" class="lihat-file-link">
                              Lihat File
                          </a>
                      </div>
                  @endif
                <div class="ft">Klik Upload → pilih file → otomatis kirim</div>
              </div>
              <div class="btns">
                <button type="button" class="btn-soft upload-pick">Upload</button>
                <button type="submit" class="upload-submit" hidden>Kirim</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- 4) Surat Pengalihan Hak --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Surat Pengalihan Hak <span class="req">*</span></p>
            <p class="card-hint">DOC/DOCX/PDF • max 10MB</p>
          </div>
          <span class="status {{ $verif->surat_pengalihan ? 'ok' : 'no' }}">
            {{ $verif->surat_pengalihan ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('patenverif.upload.pengalihan',['verif'=>$verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".doc,.docx,.pdf" required>

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                    @if($verif->surat_pengalihan)
                        {{ basename($verif->surat_pengalihan) }}
                    @else
                        Belum pilih file
                    @endif
                  </div>

                  @if($verif->surat_pengalihan)
                      <div style="margin-top:6px;">
                          <a href="{{ Storage::url($verif->surat_pengalihan) }}" target="_blank" class="lihat-file-link">
                              Lihat File
                          </a>
                      </div>
                  @endif
                <div class="ft">Klik Upload → pilih file → otomatis kirim</div>
              </div>
              <div class="btns">
                <button type="button" class="btn-soft upload-pick">Upload</button>
                <button type="submit" class="upload-submit" hidden>Kirim</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- 5) Scan KTP --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Scan KTP <span class="req">*</span></p>
            <p class="card-hint">PDF • max 10MB</p>
          </div>
          <span class="status {{ $verif->scan_ktp ? 'ok' : 'no' }}">
            {{ $verif->scan_ktp ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('patenverif.upload.ktp',['verif'=>$verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".pdf" required>

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                    @if($verif->scan_ktp)
                        {{ basename($verif->scan_ktp) }}
                    @else
                        Belum pilih file
                    @endif
                  </div>

                  @if($verif->scan_ktp)
                      <div style="margin-top:6px;">
                          <a href="{{ Storage::url($verif->scan_ktp) }}" target="_blank" class="lihat-file-link">
                              Lihat File
                          </a>
                      </div>
                  @endif
                <div class="ft">Klik Upload → pilih file → otomatis kirim</div>
              </div>
              <div class="btns">
                <button type="button" class="btn-soft upload-pick">Upload</button>
                <button type="submit" class="upload-submit" hidden>Kirim</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- 6) Gambar Prototipe --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Hasil Ciptaan <span class="req">*</span></p>
            <p class="card-hint">PNG/JPG/JPEG/SVG/PDF • max 10MB</p>
          </div>
          <span class="status {{ $verif->gambar_prototipe ? 'ok' : 'no' }}">
            {{ $verif->gambar_prototipe ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('patenverif.upload.gambar',['verif'=>$verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".png,.jpg,.jpeg,.svg,.pdf">

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                    @if($verif->hasil_ciptaan)
                        {{ basename($verif->hasil_ciptaan) }}
                    @else
                        Belum pilih file
                    @endif
                  </div>

                  @if($verif->hasil_ciptaan)
                      <div style="margin-top:6px;">
                          <a href="{{ Storage::url($verif->hasil_ciptaan) }}" target="_blank" class="lihat-file-link">
                              Lihat File
                          </a>
                      </div>
                  @endif
                <div class="ft">Khusus jenis Karya Rekaman Video mengupload screenshoot video (pdf)<br>Klik Upload → pilih file → otomatis kirim</div>
              </div>
              <div class="btns">
                <button type="button" class="btn-soft upload-pick">Upload</button>
                <button type="submit" class="upload-submit" hidden>Kirim</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- 7) Deskripsi --}}
      <div class="card card-wide">
        <div class="card-top">
          <div>
            <p class="card-name">Link Ciptaan</p>
            <p class="card-hint">Link Ciptaan untuk Hak Cipta jenis Karya Rekaman Video.</p>
          </div>
          <span class="status {{ $verif->link_ciptaan ? 'ok' : 'no' }}">
            {{ $verif->link_ciptaan ? 'Terisi' : 'Kosong' }}
          </span>
        </div>
        <div class="card-body">
          <textarea
            class="textarea"
            name="link_ciptaan"
            maxlength="255"
            form="finalSubmitForm"
            placeholder="Tulis link hasil ciptaan..."
          >{{ old('link_ciptaan', $verif->link_ciptaan) }}</textarea>
        </div>
      </div>

    </div>

    <div class="footer-actions">
      <button
          type="button"
          class="btn-prev"
          onclick="window.location.href='{{ route('datadiricipta') }}'"
      >
          &laquo; Sebelumnya
      </button>

      <form id="finalSubmitForm" method="POST" action="{{ route('patenverif.submit.final',['verif'=>$verif->id]) }}">
        @csrf
        <button type="submit" class="btn-final">
          Submit Verifikasi
        </button>
      </form>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.upload-form').forEach(form => {
    const input = form.querySelector('.upload-input');
    const pick  = form.querySelector('.upload-pick');
    const fn    = form.querySelector('.upload-fn');
    const submit= form.querySelector('.upload-submit');

    if (!input || !pick) return;

    pick.addEventListener('click', () => input.click());

    input.addEventListener('change', () => {
      const file = input.files && input.files[0];
      if (!file) return;

      if (fn) fn.textContent = file.name;

      if (submit) submit.click();
      else form.submit();
    });
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const key = 'hakcipta_link_ciptaan_{{ $verif->id }}';
  const textarea = document.getElementById('link_ciptaan_input');
  const finalForm = document.getElementById('finalSubmitForm');

  if (!textarea) return;

  // kalau DB kosong, isi dari sessionStorage
  if (!textarea.value) {
    const saved = sessionStorage.getItem(key);
    if (saved) textarea.value = saved;
  }

  // simpan tiap user ngetik
  textarea.addEventListener('input', () => {
    sessionStorage.setItem(key, textarea.value);
  });

  // kalau submit final berhasil, hapus cache browser
  if (finalForm) {
    finalForm.addEventListener('submit', () => {
      sessionStorage.removeItem(key);
    });
  }
});
</script>

@endsection