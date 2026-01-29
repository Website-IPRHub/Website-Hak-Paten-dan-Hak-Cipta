@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 1; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-isi">
  <div class="section-inner">
    <h1 class="datadiri-title">Data Pemohon <span class="req">*</span></h1>

    <form id="draftForm"class="paten-form" action="{{ route('paten.start') }}" method="POST" novalidate>
      @csrf

      <div class="form-2col">
        {{-- LEFT --}}
        <div class="col-left">

          <div class="field">
            <label class="label">Jumlah inventor <span class="req">*</span></label>
            <input
              type="number"
              class="input"
              id="jumlah_inventor"
              name="jumlah_inventor"
              min="1"
              max="20"
              value="{{ old('jumlah_inventor', 1) }}"
              required
            >
          </div>

          <div class="field">
            <label class="label">Jenis Pengajuan Paten <span class="req">*</span></label>
            <select class="input" name="jenis_paten" required>
              <option value="" selected disabled>-- Jenis Pengajuan Paten --</option>
              <option value="Paten" {{ old('jenis_paten')=='Paten' ? 'selected' : '' }}>Paten</option>
              <option value="Paten Sederhana" {{ old('jenis_paten')=='Paten Sederhana' ? 'selected' : '' }}>Paten Sederhana</option>
            </select>
          </div>

          <div class="field">
            <label class="label">Judul Paten <span class="req">*</span></label>
            <input
              type="text"
              class="input"
              name="judul_paten"
              placeholder="Masukkan judul paten"
              value="{{ old('judul_paten') }}"
              required
            >
          </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-right">
          <div class="field">
            <label class="label">Prototipe <span class="req">*</span></label>
            <select class="input" name="prototipe" required>
              <option value="" selected disabled>-- Prototipe --</option>
              <option value="Sudah" {{ old('prototipe')=='Sudah' ? 'selected' : '' }}>Sudah</option>
              <option value="Belum" {{ old('prototipe')=='Belum' ? 'selected' : '' }}>Belum</option>
            </select>
          </div>

          <div class="field">
            <label class="label">Nilai Perolehan <span class="req">*</span></label>
            <p class="hint">Jumlah biaya yang dibutuhkan untuk menghasilkan invensi</p>
            <input
              type="text"
              class="input"
              name="nilai_perolehan"
              placeholder="Nilai Perolehan"
              value="{{ old('nilai_perolehan') }}"
              required
            >
          </div>

          <div class="field">
            <label class="label">Sumber Dana <span class="req">*</span></label>
            <select class="input" name="sumber_dana" required>
              <option value="" selected disabled>-- Sumber Dana --</option>
              <option value="Universitas Diponegoro" {{ old('sumber_dana')=='Universitas Diponegoro' ? 'selected' : '' }}>Universitas Diponegoro</option>
              <option value="APBN/APBD/Swasta" {{ old('sumber_dana')=='APBN/APBD/Swasta' ? 'selected' : '' }}>APBN/APBD/Swasta</option>
              <option value="Mandiri" {{ old('sumber_dana')=='Mandiri' ? 'selected' : '' }}>Mandiri</option>
            </select>
          </div>
        </div>
      </div>

      <div class="hr"></div>

      <div class="field field-full">
        <div class="field">
            <label class="label">Data Inventor <span class="req">*</span></label>

            <div id="inventor-container"></div>
            <template id="inventor-template">
              <div class="inventor-card">
                <p class="inventor-head">Inventor <span class="inv-no"></span></p>

                <div class="inventor-grid">
                  <div class="inventor-col">
                    <div class="field">
                      <label class="label">Nama Inventor <span class="req">*</span></label>
                      <input type="text" class="input" name="inventor[nama][]" placeholder="Nama lengkap" required>
                    </div>

                    <div class="field">
                      <label class="label">NIP/NIM <span class="req">*</span></label>
                      <input type="text" class="input" name="inventor[nip_nim][]" placeholder="Masukkan NIP/NIM Anda" required>
                    </div>

                    <div class="field">
                      <label class="label">Fakultas <span class="req">*</span></label>
                      <select class="input" name="inventor[fakultas][]" required>
                        <option value="" selected disabled>-- Pilih Fakultas --</option>
                        <option value="Fakultas Teknik">Fakultas Teknik</option>
                        <option value="Fakultas Sains dan Matematika">Fakultas Sains dan Matematika</option>
                        <option value="Fakultas Kesehatan Masyarakat">Fakultas Kesehatan Masyarakat</option>
                        <option value="Fakultas Kedokteran">Fakultas Kedokteran</option>
                        <option value="Fakultas Perikanan dan Ilmu Kelautan">Fakultas Perikanan dan Ilmu Kelautan</option>
                        <option value="Fakultas Peternakan dan Pertanian">Fakultas Peternakan dan Pertanian</option>
                        <option value="Fakultas Psikologi">Fakultas Psikologi</option>
                        <option value="Fakultas Hukum">Fakultas Hukum</option>
                        <option value="Fakultas Ilmu Sosial dan Ilmu Politik">Fakultas Ilmu Sosial dan Ilmu Politik</option>
                        <option value="Fakultas Ilmu Budaya">Fakultas Ilmu Budaya</option>
                        <option value="Fakultas Ekonomi dan Bisnis">Fakultas Ekonomi dan Bisnis</option>
                        <option value="Sekolah Vokasi">Sekolah Vokasi</option>
                        <option value="Sekolah Pasca Sarjana">Sekolah Pasca Sarjana</option>
                      </select>
                    </div>
                  </div>

                  <div class="inventor-col">
                    <div class="field">
                      <label class="label">No. HP <span class="req">*</span></label>
                      <input type="text" class="input" name="inventor[no_hp][]" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="field">
                      <label class="label">Email <span class="req">*</span></label>
                      <input type="email" class="input" name="inventor[email][]" placeholder="nama@email.com" required>
                    </div>

                    <div class="field">
                      <label class="label">Status Inventor <span class="req">*</span></label>
                      <select class="input" name="inventor[status][]" required>
                        <option value="" selected disabled>-- Status Inventor --</option>
                        <option value="Dosen">Dosen</option>
                        <option value="Mahasiswa">Mahasiswa</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>

        <label class="label">Dihasilkan dari Skema Penelitian? <span class="req">*</span></label>
        <img src="/images/Skema%20Penelitian.jpg" class="skema-img" alt="Skema">

        <select class="input input-full" name="skema_penelitian" required>
          <option value="" selected disabled>-- Pilih Skema --</option>
          <option value="Penelitian Dasar (TKT 1 - 3)" {{ old('skema_penelitian')=='Penelitian Dasar (TKT 1 - 3)' ? 'selected' : '' }}>Penelitian Dasar (TKT 1 - 3)</option>
          <option value="Penelitian Terapan (TKT 4 - 6)" {{ old('skema_penelitian')=='Penelitian Terapan (TKT 4 - 6)' ? 'selected' : '' }}>Penelitian Terapan (TKT 4 - 6)</option>
          <option value="Penelitian Pengembangan (TKT 7 - 9)" {{ old('skema_penelitian')=='Penelitian Pengembangan (TKT 7 - 9)' ? 'selected' : '' }}>Penelitian Pengembangan (TKT 7 - 9)</option>
          <option value="Bukan dihasilkan dari Skema Penelitian" {{ old('skema_penelitian')=='Bukan dihasilkan dari Skema Penelitian' ? 'selected' : '' }}>Bukan dihasilkan dari Skema Penelitian</option>
        </select>
      </div>

      {{-- ACTIONS --}}
      <div class="actions-bar">
        <div class="actions-left">
          <a id="nextLink"
            href="javascript:void(0)"
            class="btn-selanjutnya is-disabled"
            data-create-url="{{ route('draftpaten') }}">
            Selanjutnya &raquo;
          </a>
        </div>
      </div>

    </form>
  </div>
</section>

@endsection
