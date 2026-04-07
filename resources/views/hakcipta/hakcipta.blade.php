@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 1; @endphp
@include('hakcipta.partials.menu')

<section class="section-full section-content">
  <div class="section-inner">
    <h1 class="page-title">Data Pemohon <span class="req">*</span></h1>

    {{-- Error summary --}}
    @if($errors->any())
      <div class="alert-error" style="background:#fee2e2;padding:12px;border-radius:8px;margin-bottom:14px;">
        <b>Validasi gagal:</b>
        <ul style="margin:8px 0 0 18px;">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="draftForm" action="{{ route('ciptaverif.start') }}" method="POST" novalidate>
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
            @error('jumlah_inventor')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

        @php
        $jenisOld        = old('jenis_cipta');
        $jenisLainnyaOld = old('jenis_cipta_lainnya');
        @endphp

        <div class="field">
        <label class="label">Jenis Hak Cipta <span class="req">*</span></label>

        <div class="jenis-radio">
            <label class="radio-item">
            <input type="radio" name="jenis_cipta" value="Buku"
                {{ $jenisOld === 'Buku' ? 'checked' : '' }} required>
            Buku
            </label>
            <br>

            <label class="radio-item">
            <input type="radio" name="jenis_cipta" value="Program Komputer"
                {{ $jenisOld === 'Program Komputer' ? 'checked' : '' }} required>
            Program Komputer
            </label>
            <br>

            <label class="radio-item">
            <input type="radio" name="jenis_cipta" value="Karya Rekaman Video"
                {{ $jenisOld === 'Karya Rekaman Video' ? 'checked' : '' }} required>
            Karya Rekaman Video
            </label>
            <br>

            <label class="radio-item">
            <input type="radio" name="jenis_cipta" value="Lainnya"
                {{ $jenisOld === 'Lainnya' ? 'checked' : '' }} required>
            Lainnya
            </label>
        </div>

        
        <div id="jenis-lainnya-wrap" style="display:none;">
            <input
                type="text"
                name="jenis_cipta_lainnya"
                value="{{ old('jenis_cipta_lainnya') }}"
            >
        <small style="color:#6b7280;">Isi jika anda memilih “Lainnya”.</small>
        </div>


        @error('jenis_cipta')
            <small style="color:red">{{ $message }}</small>
        @enderror
        @error('jenis_cipta_lainnya')
            <small style="color:red">{{ $message }}</small>
        @enderror
        </div>

        </div>

        {{-- RIGHT --}}
        <div class="col-right">
            <div class="field">
            <label class="label">Judul Cipta <span class="req">*</span></label>
            <input
              type="text"
              class="input"
              name="judul_cipta"
              placeholder="Masukkan judul cipta"
              value="{{ old('judul_cipta') }}"
              required
            >
            @error('judul_cipta')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
            <label class="label">Nilai Perolehan <span class="req">*</span></label>
            <p class="hint">Jumlah biaya yang dibutuhkan untuk menghasilkan ciptaan</p>
            <input
              type="text"
              class="input"
              name="nilai_perolehan"
              placeholder="Nilai Perolehan"
              value="{{ old('nilai_perolehan') }}"
              required
            >
            @error('nilai_perolehan')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
            <label class="label">Sumber Dana <span class="req">*</span></label>
            <select class="input" name="sumber_dana" required>
              <option value="" disabled {{ old('sumber_dana') ? '' : 'selected' }}>-- Sumber Dana --</option>
              <option value="Universitas Diponegoro" {{ old('sumber_dana')=='Universitas Diponegoro' ? 'selected' : '' }}>
                Universitas Diponegoro
              </option>
              <option value="APBN/APBD/Swasta" {{ old('sumber_dana')=='APBN/APBD/Swasta' ? 'selected' : '' }}>
                APBN/APBD/Swasta
              </option>
              <option value="Mandiri" {{ old('sumber_dana')=='Mandiri' ? 'selected' : '' }}>
                Mandiri
              </option>
            </select>
            @error('sumber_dana')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>
        </div>
      </div>

      <div class="hr"></div>

      {{-- DATA INVENTOR --}}
      <div class="field field-full">
        <label class="label">Data Inventor <span class="req">*</span></label>

        <div id="inventor-container"></div>

        <template id="inventor-template">
          <div class="inventor-card">
            <p class="inventor-head">
              Inventor <span class="inv-no"></span>
            </p>

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
                  <label class="label">Status <span class="req">*</span></label>
                  <select class="input" name="inventor[status][]" required>
                    <option value="" selected disabled>-- Pilih Status --</option>
                    <option value="Dosen">Dosen</option>
                    <option value="Mahasiswa">Mahasiswa</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </template>

        {{-- errors inventor --}}
        @error('inventor') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.nama') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.nip_nim') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.fakultas') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.no_hp') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.email') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.status') <small style="color:red">{{ $message }}</small> @enderror
      </div>

      {{-- SKEMA (PALING BAWAH) --}}
      <div class="field field-full">
        <label class="label">Dihasilkan dari Skema Penelitian? <span class="req">*</span></label>
        <img src="/images/Skema%20Penelitian.jpg" class="skema-img" alt="Skema">

        <select class="input input-full" name="skema_penelitian" required>
          <option value="" selected disabled>-- Pilih Skema --</option>
          <option value="Penelitian Dasar (TKT 1 - 3)" {{ old('skema_penelitian')=='Penelitian Dasar (TKT 1 - 3)' ? 'selected' : '' }}>
            Penelitian Dasar (TKT 1 - 3)
          </option>
          <option value="Penelitian Terapan (TKT 4 - 6)" {{ old('skema_penelitian')=='Penelitian Terapan (TKT 4 - 6)' ? 'selected' : '' }}>
            Penelitian Terapan (TKT 4 - 6)
          </option>
          <option value="Penelitian Pengembangan (TKT 7 - 9)" {{ old('skema_penelitian')=='Penelitian Pengembangan (TKT 7 - 9)' ? 'selected' : '' }}>
            Penelitian Pengembangan (TKT 7 - 9)
          </option>
          <option value="Bukan dihasilkan dari Skema Penelitian" {{ old('skema_penelitian')=='Bukan dihasilkan dari Skema Penelitian' ? 'selected' : '' }}>
            Bukan dihasilkan dari Skema Penelitian
          </option>
        </select>

        @error('skema_penelitian')
          <small style="color:red">{{ $message }}</small>
        @enderror
      </div>

      {{-- ACTIONS --}}
      <div class="actions-bar">
        <div class="actions-left">
          <button
            type="button"
            class="btn-prev"
            data-fallback="#"
            onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
          >
            &laquo; Sebelumnya
          </button>

            <button type="submit" class="btn-next">
                Selanjutnya »
            </button>
        </div>
      </div>

    </form>
  </div>
</section>

@endsection
