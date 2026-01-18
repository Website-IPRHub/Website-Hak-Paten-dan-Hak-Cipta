@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 1; @endphp
@include('hakpaten.partials.menu')

<section class="section-full section-content">
  <h1 class="page-title">Data Pemohon *</h1>

  <div class="section-inner">

    <form action="{{ route('paten.start') }}" method="POST">
      @csrf

      <div class="form-2col">
        <div class="col-left">

          <div class="field">
            <label class="label">Jenis Pengajuan Paten <span class="req">*</span></label>
            <select class="input" name="jenis_paten" required>
              <option value="" disabled {{ old('jenis_paten') ? '' : 'selected' }}>-- Jenis Pengajuan Paten --</option>
              <option value="Paten" {{ old('jenis_paten') == 'Paten' ? 'selected' : '' }}>Paten</option>
              <option value="Paten Sederhana" {{ old('jenis_paten') == 'Paten Sederhana' ? 'selected' : '' }}>Paten Sederhana</option>
            </select>
            @error('jenis_paten')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
            <label class="label">Judul Paten <span class="req">*</span></label>
            <input type="text" class="input" name="judul_paten" placeholder="Masukkan judul paten" value="{{ old('judul_paten') }}" required>
            @error('judul_paten')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
            <label class="label">Nama Pencipta <span class="req">*</span></label>
            <p>Jika lebih dari 1, pisahkan dengan titik koma (;). Contoh: Dr. Abhin, S.T., M.T.; Budi Irawan</p>
            <input type="text" class="input" name="nama_pencipta" placeholder="Masukkan nama pencipta" value="{{ old('nama_pencipta') }}" required>
            @error('nama_pencipta')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
            <label class="label">NIP / NIM Seluruh Pencipta <span class="req">*</span></label>
            <p>Jika lebih dari 1, pisahkan dengan titik koma (;). Contoh: 19811223356222; 2025666644</p>
            <input type="text" class="input" name="nip_nim" placeholder="Masukkan NIP/NIM" value="{{ old('nip_nim') }}" required>
            @error('nip_nim')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
            <label class="label">Nomor HP Aktif Seluruh Pencipta <span class="req">*</span></label>
            <p>Jika lebih dari 1, pisahkan dengan titik koma (;). Contoh: 081256789; 08123456</p>
            <input type="text" class="input" name="no_hp" placeholder="Nomor HP Aktif" value="{{ old('no_hp') }}" required>
            @error('no_hp')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

        </div>

        <div class="col-right">

          <div class="field">
            <label class="label">Fakultas <span class="req">*</span></label>
            <select class="input" name="fakultas" required>
              <option value="" disabled {{ old('fakultas') ? '' : 'selected' }}>-- Pilih Fakultas --</option>
              <option value="Fakultas Sains dan Matematika" {{ old('fakultas') == 'fsm' ? 'selected' : '' }}>Fakultas Sains dan Matematika</option>
              <option value="Fakultas Teknik" {{ old('fakultas') == 'ft' ? 'selected' : '' }}>Fakultas Teknik</option>
              <option value="Fakultas Kesehatan Masyarakat" {{ old('fakultas') == 'fkm' ? 'selected' : '' }}>Fakultas Kesehatan Masyarakat</option>
              <option value="Fakultas Kedokteran" {{ old('fakultas') == 'fk' ? 'selected' : '' }}>Fakultas Kedokteran</option>
              <option value="Fakultas Perikanan dan Ilmu Kelautan" {{ old('fakultas') == 'fpik' ? 'selected' : '' }}>Fakultas Perikanan dan Ilmu Kelautan</option>
              <option value="Fakultas Peternakan dan Pertanian" {{ old('fakultas') == 'fpp' ? 'selected' : '' }}>Fakultas Peternakan dan Pertanian</option>
              <option value="Fakultas Psikologi" {{ old('fakultas') == 'fpsi' ? 'selected' : '' }}>Fakultas Psikologi</option>
              <option value="Fakultas Hukum" {{ old('fakultas') == 'fh' ? 'selected' : '' }}>Fakultas Hukum</option>
              <option value="Fakultas Ilmu Sosial dan Ilmu Politik" {{ old('fakultas') == 'fisip' ? 'selected' : '' }}>Fakultas Ilmu Sosial dan Ilmu Politik</option>
              <option value="Fakultas Ilmu Budaya" {{ old('fakultas') == 'fib' ? 'selected' : '' }}>Fakultas Ilmu Budaya</option>
              <option value="Fakultas Ekonomi dan Bisnis" {{ old('fakultas') == 'feb' ? 'selected' : '' }}>Fakultas Ekonomi dan Bisnis</option>
              <option value="Sekolah Vokasi" {{ old('fakultas') == 'sv' ? 'selected' : '' }}>Sekolah Vokasi</option>
              <option value="Sekolah Pasca Sarjana" {{ old('fakultas') == 'pasca' ? 'selected' : '' }}>Sekolah Pasca Sarjana</option>
            </select>
            @error('fakultas')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
  <label class="label">Email Seluruh Pencipta <span class="req">*</span></label>
  <p>Jika lebih dari 1, pisahkan dengan titik koma (;). Contoh: amin@gmail.com; budi@gmail.com</p>

          <input
            type="text"
            class="input"
            name="email"
            placeholder="Email (pisahkan dengan ;)"
            value="{{ old('email') }}"
            required
          >

          @error('email')
            <small style="color:red">{{ $message }}</small>
          @enderror
        </div>


          <div class="field">
            <label class="label">Prototipe <span class="req">*</span></label>
            <select class="input" name="prototipe" required>
              <option value="" disabled {{ old('prototipe') ? '' : 'selected' }}>-- Prototipe --</option>
              <option value="Sudah" {{ old('prototipe') == 'Sudah' ? 'selected' : '' }}>Sudah</option>
              <option value="Belum" {{ old('prototipe') == 'Belum' ? 'selected' : '' }}>Belum</option>
            </select>
            @error('prototipe')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
            <label class="label">Nilai Perolehan <span class="req">*</span></label>
            <p>Jumlah biaya yang dibutuhkan untuk menghasilkan invensi</p>
            <input type="text" class="input" name="nilai_perolehan" placeholder="Nilai Perolehan" value="{{ old('nilai_perolehan') }}" required>
            @error('nilai_perolehan')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

          <div class="field">
            <label class="label">Sumber Dana <span class="req">*</span></label>
            <select class="input" name="sumber_dana" required>
              <option value="" disabled {{ old('sumber_dana') ? '' : 'selected' }}>-- Sumber Dana --</option>
              <option value="Universitas Diponegoro" {{ old('sumber_dana') == 'Undip' ? 'selected' : '' }}>Universitas Diponegoro</option>
              <option value="APBN/APBD/Swasta" {{ old('sumber_dana') == 'APBN/APBD/Swasta' ? 'selected' : '' }}>APBN/APBD/Swasta</option>
              <option value="Mandiri" {{ old('sumber_dana') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
            </select>
            @error('sumber_dana')
              <small style="color:red">{{ $message }}</small>
            @enderror
          </div>

        </div>
      </div>

      <div class="field field-full">
        <label class="label">Dihasilkan dari Skema Penelitian? <span class="req">*</span></label>
        <img src="{{ asset('images/Skema Penelitian.jpg') }}" class="skema-img" alt="Skema">

        <select class="input input-full" name="skema_penelitian" required>
          <option value="" disabled {{ old('skema_penelitian') ? '' : 'selected' }}>-- Pilih Skema --</option>
          <option value="Penelitian Dasar (TKT 1 - 3)" {{ old('skema_penelitian') == 'Penelitian Dasar (TKT 1 - 3)' ? 'selected' : '' }}>Penelitian Dasar (TKT 1 - 3)</option>
          <option value="Penelitian Terapan (TKT 4 - 6)" {{ old('skema_penelitian') == 'Penelitian Terapan (TKT 4 - 6)' ? 'selected' : '' }}>Penelitian Terapan (TKT 4 - 6)</option>
          <option value="Penelitian Pengembangan (TKT 7 - 9)" {{ old('skema_penelitian') == 'Penelitian Pengembangan (TKT 7 - 9)' ? 'selected' : '' }}>Penelitian Pengembangan (TKT 7 - 9)</option>
          <option value="Bukan dihasilkan dari Skema Penelitian" {{ old('skema_penelitian') == 'Bukan dihasilkan dari Skema Penelitian' ? 'selected' : '' }}>Bukan dihasilkan dari Skema Penelitian</option>
        </select>

        @error('skema_penelitian')
          <small style="color:red">{{ $message }}</small>
        @enderror
      </div>

      <div class="next">
        {{-- button submit: bakal nembak controller start() dan set session paten_id --}}
        <button type="submit" class="btn-selanjutnya">
          Selanjutnya
        </button>
      </div>

    </form>

  </div>
</section>

@endsection
