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
            <p class="card-name">Draft Paten <span class="req">*</span></p>
            <p class="card-hint">DOC/DOCX/PDF • max 5MB</p>
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
                  {{ $verif->draft_paten ? basename($verif->draft_paten) : 'Belum pilih file' }}
                </div>
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
                  {{ $verif->form_permohonan ? basename($verif->form_permohonan) : 'Belum pilih file' }}
                </div>
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
            <p class="card-name">Surat Kepemilikan Invensi <span class="req">*</span></p>
            <p class="card-hint">DOC/DOCX/PDF</p>
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
                  {{ $verif->surat_kepemilikan ? basename($verif->surat_kepemilikan) : 'Belum pilih file' }}
                </div>
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
            <p class="card-hint">DOC/DOCX/PDF</p>
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
                  {{ $verif->surat_pengalihan ? basename($verif->surat_pengalihan) : 'Belum pilih file' }}
                </div>
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
                  {{ $verif->scan_ktp ? basename($verif->scan_ktp) : 'Belum pilih file' }}
                </div>
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
            <p class="card-name">Gambar Prototipe</p>
            <p class="card-hint">PNG/JPG/JPEG/SVG/PDF</p>
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
                  {{ $verif->gambar_prototipe ? basename($verif->gambar_prototipe) : 'Belum pilih file' }}
                </div>
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

      {{-- 7) Deskripsi --}}
      <div class="card card-wide">
        <div class="card-top">
          <div>
            <p class="card-name">Deskripsi Singkat Prototipe</p>
            <p class="card-hint">Maks 255 karakter (opsional). Akan ikut tersimpan saat Submit Final.</p>
          </div>
          <span class="status {{ $verif->deskripsi_singkat_prototipe ? 'ok' : 'no' }}">
            {{ $verif->deskripsi_singkat_prototipe ? 'Terisi' : 'Kosong' }}
          </span>
        </div>
        <div class="card-body">
          <textarea
            class="textarea"
            name="deskripsi"
            maxlength="255"
            form="finalSubmitForm"
            placeholder="Tulis deskripsi singkat..."
          >{{ old('deskripsi', $verif->deskripsi_singkat_prototipe) }}</textarea>
        </div>
      </div>

    </div>

    <div class="footer-actions">
      <button
          type="button"
          class="btn-prev"
          onclick="window.location.href='{{ route('patenverif.datadiri', $verif->id) }}'"
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

@endsection
