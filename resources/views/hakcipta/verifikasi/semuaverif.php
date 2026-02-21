@extends('layouts.app')
@section('title','Upload Berkas Verifikasi Hak Cipta')
@section('body-class','paten-page')

@section('content')

@php $activeStep = 4; @endphp
@include('hakcipta.isiform.menuformcipta')

{{ dd($verif) }}

<div class="upload-halaman" style="background:yellow; padding:50px">
  <div class="upload-pembungkus">

    <div class="upload-head">
      <div>
        <h1 class="upload-judul">Upload Berkas Verifikasi Hak Cipta</h1>
        <p class="upload-sub">Upload satu per satu dan terakhir baru klik <b>Submit Verifikasi</b>.</p>
      </div>
    </div>

    <div class="grid">

      {{-- 1) Formulir Permohonan --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Formulir Permohonan Pendaftaran Ciptaan <span class="req">*</span></p>
            <p class="card-hint">DOC/DOCX/PDF • max 5MB</p>
          </div>
          <span class="status {{ $verif->surat_permohonan ? 'ok' : 'no' }}">
            {{ $verif->surat_permohonan ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('ciptaverif.upload.form', ['verif' => $verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".doc,.docx,.pdf" required>

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                  {{ $verif->surat_permohonan ? basename($verif->surat_permohonan) : 'Belum pilih file' }}
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

      {{-- 2) Surat Pernyataan Hak Cipta --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Surat Pernyataan Hak Cipta <span class="req">*</span></p>
            <p class="card-hint">DOC/DOCX/PDF</p>
          </div>
          <span class="status {{ $verif->surat_pernyataan ? 'ok' : 'no' }}">
            {{ $verif->surat_pernyataan ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('ciptaverif.upload.invensi', ['verif' => $verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".doc,.docx,.pdf" required>

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                  {{ $verif->surat_pernyataan ? basename($verif->surat_pernyataan) : 'Belum pilih file' }}
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

      {{-- 3) Surat Pengalihan Hak --}}
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
          <form class="upload-form" method="POST" action="{{ route('ciptaverif.upload.pengalihan', ['verif' => $verif->id]) }}" enctype="multipart/form-data">
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

      {{-- 4) Scan KTP --}}
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
          <form class="upload-form" method="POST" action="{{ route('ciptaverif.upload.scanktp', ['verif' => $verif->id]) }}" enctype="multipart/form-data">
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

      {{-- 5) Hasil Ciptaan --}}
      <div class="card">
        <div class="card-top">
          <div>
            <p class="card-name">Hasil Ciptaan</p>
            <p class="card-hint">PNG/JPG/JPEG/SVG/PDF</p>
          </div>
          <span class="status {{ $verif->hasil_ciptaan ? 'ok' : 'no' }}">
            {{ $verif->hasil_ciptaan ? 'Sudah' : 'Belum' }}
          </span>
        </div>
        <div class="card-body">
          <form class="upload-form" method="POST" action="{{ route('ciptaverif.upload.hasilciptaan', ['verif' => $verif->id]) }}" enctype="multipart/form-data">
            @csrf
            <input class="upload-input" type="file" name="file" hidden accept=".png,.jpg,.jpeg,.svg,.pdf">

            <div class="drop">
              <div class="file-meta">
                <div class="fn upload-fn">
                  {{ $verif->hasil_ciptaan ? basename($verif->hasil_ciptaan) : 'Belum pilih file' }}
                </div>
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

      {{-- 6) Link Ciptaan --}}
      <div class="card card-wide">
        <div class="card-top">
          <div>
            <p class="card-name">Link CIptaan</p>
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
          data-fallback="{{ route('datadiricipta') }}"
          onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
        >
          &laquo; Sebelumnya
        </button>

      <form id="finalSubmitForm" method="POST" action="{{ route('ciptaverif.submit.final',['verif'=>$verif->id]) }}">
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
