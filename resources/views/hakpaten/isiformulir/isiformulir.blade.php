@extends('layouts.app')

@section('title','Test Header')

@section('content')

@php $activeStep = 2; @endphp
@include('hakpaten.isiformulir.menuformulir')

<div class="paten-form-page">
  <div class="judul">
    <h2>Formulir Pendaftaran Paten</h2>
    <p>Isi formulir ini untuk mendapatkan dokumen form paten</p>
  </div>

  <form class="form" method="POST" action="{{ route('isiform.store') }}">
    @csrf

    {{-- ===============================
    1) JENIS PATEN + PCT
    =============================== --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Jenis Pengajuan Paten <span class="req">*</span></label>
          <select class="input" name="jenis_paten" required>
            <option value="" disabled {{ old('jenis_paten') ? '' : 'selected' }}>-- Jenis Pengajuan Paten --</option>
            <option value="Paten" {{ old('jenis_paten')=='Paten'?'selected':'' }}>Paten</option>
            <option value="Paten Sederhana" {{ old('jenis_paten')=='Paten Sederhana'?'selected':'' }}>Paten Sederhana</option>
          </select>
          @error('jenis_paten') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="col">
        <div class="field">
          <label class="label">Apakah menggunakan Nomor PCT? <span class="req">*</span></label>
          <select class="input" id="is_pct" name="is_pct" required>
            <option value="" disabled {{ old('is_pct') ? '' : 'selected' }}>-- Apakah menggunakan Nomor PCT --</option>
            <option value="Ya" {{ old('is_pct')=='Ya'?'selected':'' }}>Ya</option>
            <option value="Tidak" {{ old('is_pct')=='Tidak'?'selected':'' }}>Tidak</option>
          </select>
          @error('is_pct') <small style="color:red">{{ $message }}</small> @enderror
        </div>

        <div class="field" id="pct-followup" @if(old('is_pct')!=='Ya') style="display:none;" @endif>
          <label class="label">Nomor Permohonan Paten Internasional (PCT) <span class="req">*</span></label>
          <input type="text" class="input" name="nomor_permohonan"
                 placeholder="Masukkan nomor permohonan paten internasional"
                 value="{{ old('nomor_permohonan') }}"
                 @if(old('is_pct')==='Ya') required @endif>
          @error('nomor_permohonan') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    {{-- ===============================
    2) JUDUL + PECAHAN
    =============================== --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Judul Invensi <span class="req">*</span></label>
          <input type="text" class="input" name="judul_invensi" placeholder="Masukkan judul draft paten"
                 value="{{ old('judul_invensi') }}" required>
          @error('judul_invensi') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="col">
        <div class="field">
          <label class="label">Apakah merupakan pecahan paten? <span class="req">*</span></label>
          <select class="input" id="is_pecahan" name="is_pecahan" required>
            <option value="" disabled {{ old('is_pecahan') ? '' : 'selected' }}>-- Apakah merupakan pecahan paten --</option>
            <option value="Ya" {{ old('is_pecahan')=='Ya'?'selected':'' }}>Ya</option>
            <option value="Tidak" {{ old('is_pecahan')=='Tidak'?'selected':'' }}>Tidak</option>
          </select>
          @error('is_pecahan') <small style="color:red">{{ $message }}</small> @enderror
        </div>

        <div class="field" id="pecahan-followup" @if(old('is_pecahan')!=='Ya') style="display:none;" @endif>
          <label class="label">Nomor Permohonan Paten Induk <span class="req">*</span></label>
          <input type="text" class="input" name="pecahan_paten"
                 placeholder="Masukkan nomor permohonan paten induk"
                 value="{{ old('pecahan_paten') }}"
                 @if(old('is_pecahan')==='Ya') required @endif>
          @error('pecahan_paten') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    {{-- ===============================
    3) KONSULTAN PATEN
    =============================== --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Apakah Melalui Konsultan Paten? <span class="req">*</span></label>
          <select class="input" id="konsultanpaten" name="konsultanpaten" required>
            <option value="" disabled {{ old('konsultanpaten') ? '' : 'selected' }}>-- Apakah Melalui Konsultan Paten --</option>
            <option value="Melalui" {{ old('konsultanpaten')=='Melalui'?'selected':'' }}>Melalui</option>
            <option value="Tidak Melalui" {{ old('konsultanpaten')=='Tidak Melalui'?'selected':'' }}>Tidak Melalui</option>
          </select>
          @error('konsultanpaten') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    <div class="field" id="konsultan-followup" @if(old('konsultanpaten')!=='Melalui') style="display:none;" @endif>
      <div class="row-2">
        <div class="col">
          <div class="field">
            <label class="label">Nama Badan Hukum <span class="req">*</span></label>
            <input type="text" class="input" id="nama_badan_hukum" name="nama_badan_hukum"
                   value="{{ old('nama_badan_hukum') }}" placeholder="Masukkan nama badan hukum"
                   @if(old('konsultanpaten')==='Melalui') required @endif>
            @error('nama_badan_hukum') <small style="color:red">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label class="label">Nama Konsultan Paten <span class="req">*</span></label>
            <input type="text" class="input" id="nama_konsultan_paten" name="nama_konsultan_paten"
                   value="{{ old('nama_konsultan_paten') }}" placeholder="Masukkan nama konsultan paten"
                   @if(old('konsultanpaten')==='Melalui') required @endif>
            @error('nama_konsultan_paten') <small style="color:red">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label class="label">Nomor Konsultan Paten <span class="req">*</span></label>
            <input type="text" class="input" id="nomor_konsultan_paten" name="nomor_konsultan_paten"
                   value="{{ old('nomor_konsultan_paten') }}" placeholder="Masukkan nomor konsultan paten"
                   @if(old('konsultanpaten')==='Melalui') required @endif>
            @error('nomor_konsultan_paten') <small style="color:red">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="col">
          <div class="field">
            <label class="label">Alamat Badan Hukum <span class="req">*</span></label>
            <input type="text" class="input" id="alamat_badan_hukum" name="alamat_badan_hukum"
                   value="{{ old('alamat_badan_hukum') }}" placeholder="Masukkan alamat badan hukum"
                   @if(old('konsultanpaten')==='Melalui') required @endif>
            @error('alamat_badan_hukum') <small style="color:red">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label class="label">Alamat Konsultan Paten <span class="req">*</span></label>
            <input type="text" class="input" id="alamat_konsultan_paten" name="alamat_konsultan_paten"
                   value="{{ old('alamat_konsultan_paten') }}" placeholder="Masukkan alamat konsultan paten"
                   @if(old('konsultanpaten')==='Melalui') required @endif>
            @error('alamat_konsultan_paten') <small style="color:red">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label class="label">Telepon/Fax <span class="req">*</span></label>
            <input type="text" class="input" id="telepon_fax" name="telepon_fax"
                   value="{{ old('telepon_fax') }}" placeholder="Masukkan telepon/fax"
                   @if(old('konsultanpaten')==='Melalui') required @endif>
            @error('telepon_fax') <small style="color:red">{{ $message }}</small> @enderror
          </div>
        </div>
      </div>
    </div>

    <div class="col">
        <div class="field">
          <label class="label">Lampiran 1 (satu) rangkap</label>

          <label>
            <input type="checkbox" name="lampiran[]" value="surat_kuasa"
              {{ in_array('surat_kuasa', old('lampiran', [])) ? 'checked' : '' }}>
            surat kuasa
          </label>

          <label>
            <input type="checkbox" checked disabled>
            surat pengalihan hak atas penemuan
          </label>
          <input type="hidden" name="lampiran[]" value="pengalihan">

          <label>
            <input type="checkbox" checked disabled>
            bukti pemilikan hak atas penemuan
          </label>
          <input type="hidden" name="lampiran[]" value="bukti_pemilikan">

          <label>
            <input type="checkbox" name="lampiran[]" value="do_eo"
              {{ in_array('do_eo', old('lampiran', [])) ? 'checked' : '' }}>
            bukti penunjukan negara tujuan (DO/EO)
          </label>

          <label>
            <input type="checkbox" name="lampiran[]" value="dok_prioritas"
              {{ in_array('dok_prioritas', old('lampiran', [])) ? 'checked' : '' }}>
            dokumen prioritas dan terjemahannya
          </label>

          <label>
            <input type="checkbox" name="lampiran[]" value="dok_pct"
              {{ in_array('dok_pct', old('lampiran', [])) ? 'checked' : '' }}>
            dokumen permohonan paten internasional/PCT
          </label>

          <label>
            <input type="checkbox" name="lampiran[]" value="jasad_renik"
              {{ in_array('jasad_renik', old('lampiran', [])) ? 'checked' : '' }}>
            sertifikat penyimpanan jasad renik dan terjemahannya
          </label>

          <label>
            <input type="checkbox" checked disabled>
            dokumen lain (sebutkan)
          </label>
          <input type="hidden" name="lampiran[]" value="dok_lain">

          <textarea class="input" name="lampiran_lainnya"
             placeholder="Tulis per baris ya. Contoh:
Fotocopy KTP Pemohon
Fotocopy KTP Para Inventor
">{{ old('lampiran_lainnya') }}</textarea>

          @error('lampiran') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>

    {{-- ===============================
    4) HAK PRIORITAS
    =============================== --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Permohonan paten ini diajukan dengan/tidak dengan Hak prioritas? <span class="req">*</span></label>
          <select class="input" id="hak_prioritas" name="hak_prioritas" required>
            <option value="" disabled {{ old('hak_prioritas') ? '' : 'selected' }}>-- Hak Prioritas --</option>
            <option value="Ya" {{ old('hak_prioritas')=='Ya'?'selected':'' }}>Ya</option>
            <option value="Tidak" {{ old('hak_prioritas')=='Tidak'?'selected':'' }}>Tidak</option>
          </select>
          @error('hak_prioritas') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    <div class="field" id="hak-prioritas-followup" @if(old('hak_prioritas') !== 'Ya') style="display:none;" @endif>
      <div class="row-2">
        <div class="col">
          <div class="field">
            <label class="label">Negara <span class="req">*</span></label>
            <input type="text" class="input" id="negara" name="negara"
                   value="{{ old('negara') }}" placeholder="Masukkan negara"
                   @if(old('hak_prioritas') === 'Ya') required @endif>
            @error('negara') <small style="color:red">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="col">
          <div class="field">
            <label class="label">Nomor Prioritas <span class="req">*</span></label>
            <input type="text" class="input" id="nomor_prioritas" name="nomor_prioritas"
                   value="{{ old('nomor_prioritas') }}" placeholder="Masukkan nomor prioritas"
                   @if(old('hak_prioritas') === 'Ya') required @endif>
            @error('nomor_prioritas') <small style="color:red">{{ $message }}</small> @enderror
          </div>
        </div>
      </div>

      <div class="field">
        <label class="label">Tanggal Penerimaan Permohonan <span class="req">*</span></label>
        <p>Format: Tanggal/Bulan/Tahun<br>Contoh: 13/05/2026</p>
        <input type="text" class="input" id="tgl_penerimaan" name="tgl_penerimaan"
               value="{{ old('tgl_penerimaan') }}" placeholder="Masukkan Tanggal Penerimaan Permohonan"
               @if(old('hak_prioritas') === 'Ya') required @endif>
        @error('tgl_penerimaan') <small style="color:red">{{ $message }}</small> @enderror
      </div>
    </div>


    {{-- ===============================
    5) URAIAN + TANGGAL
    =============================== --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Uraian (jumlah halaman) <span class="req">*</span></label>
          <input type="number" class="input" name="uraian_halaman" min="1"
                 value="{{ old('uraian_halaman') }}" placeholder="Masukkan jumlah halaman" required>
          @error('uraian_halaman') <small style="color:red">{{ $message }}</small> @enderror
        </div>

        <div class="field">
          <label class="label">Abstrak (jumlah buah) <span class="req">*</span></label>
          <input type="number" class="input" name="abstrak_buah" min="1"
                 value="{{ old('abstrak_buah') }}" placeholder="Masukkan jumlah buah" required>
          @error('abstrak_buah') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="col">
        <div class="field">
          <label class="label">Klaim (jumlah buah) <span class="req">*</span></label>
          <input type="number" class="input" name="klaim_buah" min="1"
                 value="{{ old('klaim_buah') }}" placeholder="Masukkan jumlah buah" required>
          @error('klaim_buah') <small style="color:red">{{ $message }}</small> @enderror
        </div>

        <div class="field">
          <label class="label">Gambar (jumlah buah) <span class="req">*</span></label>
          <input type="number" class="input" name="gambar_buah" min="1"
                 value="{{ old('gambar_buah') }}" placeholder="Masukkan jumlah buah" required>
          @error('gambar_buah') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    <div class="col">
      <div class="field">
        <label class="label">Gambar yang menyertai abstrak (dari–sampai) <span class="req">*</span></label>
        <div class="row-2 inner-row">
          <div class="col">
            <input type="number" class="input" name="gambar_dari" placeholder="Dari (contoh: 1)"
                   value="{{ old('gambar_dari') }}" required>
            @error('gambar_dari') <small style="color:red">{{ $message }}</small> @enderror
          </div>
          <div class="col">
            <input type="number" class="input" name="gambar_sampai" placeholder="Sampai (contoh: 3)"
                   value="{{ old('gambar_sampai') }}" required>
            @error('gambar_sampai') <small style="color:red">{{ $message }}</small> @enderror
          </div>
        </div>
      </div>
    </div>

    <div class="field">
      <label class="label">Tanggal Pengisian</label>
      <input type="date" class="input" name="tanggal_pengisian"
             value="{{ old('tanggal_pengisian', now()->format('Y-m-d')) }}">
      @error('tanggal_pengisian') <small style="color:red">{{ $message }}</small> @enderror
    </div>

    {{-- ===============================
    6) JUMLAH INVENTOR (PALING BAWAH)
    =============================== --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Jumlah inventor <span class="req">*</span></label>

          <div class="jumlah-inventor-wrap" style="display:flex; gap:10px; align-items:center;">
            <button type="button" id="invMinus" class="btn-minus" aria-label="Kurangi inventor">-</button>

            <input
              id="jumlah_inventor"
              name="jumlah_inventor"
              class="input"
              value="{{ old('jumlah_inventor', 1) }}"
              readonly
              style="text-align:center; width:90px;"
              required
            >

            <button type="button" id="invPlus" class="btn-plus" aria-label="Tambah inventor">+</button>
          </div>

          @error('jumlah_inventor') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    {{-- ===============================
    7) DATA INVENTOR (PALING BAWAH)
    =============================== --}}
    <div class="hr"></div>

    <div class="field field-full">
      <div class="field">
        <label class="label">Data Inventor <span class="req">*</span></label>

        <div id="inventor-container-verif"></div>

        @error('inventor') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.*') <small style="color:red">{{ $message }}</small> @enderror

        {{-- TEMPLATE INVENTOR 1 (WAJIB DOSEN) --}}
        <template id="inventor-template-first-verif">
          <div class="inventor-card">
            <p class="inventor-head">Inventor <span class="inv-no"></span></p>
            <p>Catatan: Data Inventor 1 HARUS diisi dengan data Dosen</p>

            <div class="inventor-grid">
              <div class="inventor-col">
                <div class="field">
                  <label class="label">Nama Lengkap Inventor <span class="req">*</span></label>
                  <input type="text" class="input" name="inventor[nama][]" placeholder="Nama lengkap" required>
                </div>

                <div class="field">
                  <label class="label">Kewarganegaraan <span class="req">*</span></label>
                  <input type="text" class="input" name="inventor[kewarganegaraan][]" placeholder="Contoh: Indonesia" required>
                </div>

                <div class="field">
                  <label class="label">NIP/NIM <span class="req">*</span></label>
                  <input type="text" class="input nip-input" name="inventor[nip_nim][]" placeholder="Masukkan NIP/NIM Anda" required>
                  <small class="nip-warning">NIP/NIM harus terdiri dari 14 atau 18 digit angka</small>
                </div>

                <div class="field">
                  <label class="label">Alamat Lengkap (sesuai KTP) <span class="req">*</span></label>
                  <textarea class="input" name="inventor[alamat][]" rows="3" placeholder="Alamat lengkap" required></textarea>
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

                <div class="field nidn-field">
                  <label class="label">NIDN <span class="req">*</span></label>
                  <input type="text" class="input nidn-input" name="inventor[nidn][]" placeholder="NIDN" required>
                  <small class="nidn-warning">NIDN harus 8 karakter</small>
                </div>

                <div class="field">
                  <label class="label">Status Inventor <span class="req">*</span></label>
                  <input type="text" class="input" value="Dosen" disabled>
                  <input type="hidden" name="inventor[status][]" value="Dosen">
                </div>

                <div class="field">
                  <label class="label">Kode Pos <span class="req">*</span></label>
                  <input type="text" class="input" name="inventor[kode_pos][]" placeholder="Contoh: XXXXX" required>
                </div>

                <div class="field">
                  <label class="label">Pekerjaan <span class="req">*</span></label>
                  <input type="text" class="input" name="inventor[pekerjaan][]" placeholder="Contoh: Pegawai" required>
                </div>
              </div>
            </div>
          </div>
        </template>

        {{-- TEMPLATE INVENTOR 2+ (STATUS PILIH) --}}
        <template id="inventor-template-verif">
          <div class="inventor-card">
            <p class="inventor-head">Inventor <span class="inv-no"></span></p>

            <div class="inventor-grid">
              <div class="inventor-col">
                <div class="field">
                  <label class="label">Nama Lengkap Inventor <span class="req">*</span></label>
                  <input type="text" class="input" name="inventor[nama][]" placeholder="Nama lengkap" required>
                </div>

                <div class="field">
                  <label class="label">Kewarganegaraan <span class="req">*</span></label>
                  <input type="text" class="input" name="inventor[kewarganegaraan][]" placeholder="Contoh: Indonesia" required>
                </div>

                <div class="field">
                  <label class="label">NIP/NIM <span class="req">*</span></label>
                  <input type="text" class="input nip-input" name="inventor[nip_nim][]" placeholder="Masukkan NIP/NIM Anda" required>
                  <small class="nip-warning">NIP/NIM harus terdiri dari 14 atau 18 digit angka</small>
                </div>

                <div class="field">
                  <label class="label">Alamat Lengkap (sesuai KTP) <span class="req">*</span></label>
                  <textarea class="input" name="inventor[alamat][]" rows="3" placeholder="Alamat lengkap" required></textarea>
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

                <div class="field nidn-field" style="display:none;">
                  <label class="label">NIDN <span class="req">*</span></label>
                  <input type="text" class="input nidn-input" name="inventor[nidn][]" placeholder="Masukkan NIDN">
                  <small class="nidn-warning">NIDN harus 8 karakter</small>
                </div>

                <div class="field">
                  <label class="label">Status Inventor <span class="req">*</span></label>
                  <select class="input status-select" name="inventor[status][]" required>
                    <option value="" selected disabled>-- Status Inventor --</option>
                    <option value="Dosen">Dosen</option>
                    <option value="Mahasiswa">Mahasiswa</option>
                  </select>
                </div>

                <div class="field">
                  <label class="label">Kode Pos <span class="req">*</span></label>
                  <input type="text" class="input" name="inventor[kode_pos][]" placeholder="Contoh: XXXXX" required>
                </div>

                <div class="field">
                  <label class="label">Pekerjaan <span class="req">*</span></label>
                  <input type="text" class="input" name="inventor[pekerjaan][]" placeholder="Contoh: Pegawai" required>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    {{-- ACTIONS --}}
    <div class="actions-bar">
      <div class="actions-left">
        <button
          type="button"
          class="btn-prev"
          data-fallback="{{ route('hakpaten.draftpatenisiformulir') }}"
          onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
        >
          &laquo; Sebelumnya
        </button>

        <a class="btn-next" href="{{ route('hakpaten.invensiformulir') }}">
          Selanjutnya &raquo;
        </a>
      </div>

      <div class="actions-download">
        <select id="doc_type" class="input" style="width:220px;">
          <option value="" selected disabled>-- Pilih Dokumen --</option>
          <option value="{{ route('isiform.store') }}">Form Paten</option>
          <option value="{{ route('pengalihanhak.store') }}">Surat Pengalihan Hak</option>
          <option value="{{ route('invensi.store') }}">Kepemilikan Invensi</option>
        </select>

        <select name="download_format" class="input" style="width:160px;">
          <option value="pdf">PDF</option>
          <option value="docx">DOCX</option>
        </select>

        <button type="submit" class="unduh" id="btnDownload">
          ⬇ Download
        </button>
      </div>

    </div>

  </form>

  <script type="application/json" id="old-inventor-data">
    {!! json_encode(old('inventor', [])) !!}
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const jumlahInput = document.getElementById("jumlah_inventor");
      const btnMinus = document.getElementById("invMinus");
      const btnPlus  = document.getElementById("invPlus");

      const container = document.getElementById("inventor-container-verif");
      const tplFirst  = document.getElementById("inventor-template-first-verif");
      const tplOther  = document.getElementById("inventor-template-verif");

      const oldEl = document.getElementById("old-inventor-data");
      let oldInventor = {};
      try { oldInventor = JSON.parse(oldEl?.textContent || "{}"); } catch(e) { oldInventor = {}; }

      const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

      const getCount = () => {
        const n = parseInt(jumlahInput?.value || "1", 10);
        return clamp(isNaN(n) ? 1 : n, 1, 20);
      };

      const snapshotCurrent = () => {
        const snap = {};
        if (!container) return snap;

        container.querySelectorAll("[name^='inventor[']").forEach((el) => {
          const name = el.getAttribute("name");
          if (!snap[name]) snap[name] = [];
          snap[name].push(el.value);
        });

        return snap;
      };

      const fillFromOld = (root, idx) => {
        const keys = [
          "nama","kewarganegaraan","nip_nim","alamat","fakultas",
          "no_hp","email","nidn","status","kode_pos","pekerjaan"
        ];

        keys.forEach((k) => {
          const el = root.querySelector(`[name="inventor[${k}][]"]`);
          if (!el) return;

          const arr = oldInventor?.[k];
          const val = Array.isArray(arr) ? (arr[idx] ?? "") : "";
          if (val) el.value = val;
        });
      };

      const applyStatusLogic = (card, isFirst) => {
        const statusSelect = card.querySelector(".status-select");
        const nidnField = card.querySelector(".nidn-field");
        const nidnInput = card.querySelector('[name="inventor[nidn][]"]');

        if (isFirst) {
          if (nidnField) nidnField.style.display = "";
          if (nidnInput) nidnInput.setAttribute("required", "required");
          return;
        }

        const update = () => {
          const isDosen = statusSelect?.value === "Dosen";
          if (nidnField) nidnField.style.display = isDosen ? "" : "none";
          if (nidnInput) {
            if (isDosen) nidnInput.setAttribute("required", "required");
            else {
              nidnInput.removeAttribute("required");
              nidnInput.value = "";
            }
          }
        };

        if (statusSelect) {
          statusSelect.addEventListener("change", update);
          update();
        }
      };

      function renderInventors(count) {
        if (!container || !tplFirst || !tplOther) return;

        const snap = snapshotCurrent();
        container.innerHTML = "";

        for (let i = 0; i < count; i++) {
          const isFirst = (i === 0);
          const tpl = isFirst ? tplFirst : tplOther;

          const node = tpl.content.cloneNode(true);
          const card = node.querySelector(".inventor-card");

          const no = node.querySelector(".inv-no");
          if (no) no.textContent = (i + 1);

          node.querySelectorAll("[name^='inventor[']").forEach((el) => {
            const name = el.getAttribute("name");
            const arr = snap[name] || [];
            const v = arr[i] ?? "";
            if (v) el.value = v;
          });

          fillFromOld(node, i);

          if (card) applyStatusLogic(card, isFirst);

          container.appendChild(node);
        }
      }

      const setCount = (n) => {
        const v = clamp(n, 1, 20);
        if (jumlahInput) jumlahInput.value = v;
        renderInventors(v);
      };

      // init
      setCount(getCount());

      if (btnMinus) btnMinus.addEventListener("click", () => setCount(getCount() - 1));
      if (btnPlus)  btnPlus.addEventListener("click", () => setCount(getCount() + 1));

      // Konsultan show/hide + required toggle
      const konsultan = document.getElementById("konsultanpaten");
      const follow = document.getElementById("konsultan-followup");
      const konsultanReqIds = [
        "nama_badan_hukum","nama_konsultan_paten","nomor_konsultan_paten",
        "alamat_badan_hukum","alamat_konsultan_paten","telepon_fax"
      ];

      const setKonsultanRequired = (on) => {
        konsultanReqIds.forEach(id => {
          const el = document.getElementById(id);
          if (!el) return;
          if (on) el.setAttribute("required", "required");
          else el.removeAttribute("required");
        });
      };

      const updateKonsultanUI = () => {
        const isMelalui = konsultan?.value === "Melalui";
        if (follow) follow.style.display = isMelalui ? "" : "none";
        setKonsultanRequired(!!isMelalui);
      };

      if (konsultan) {
        konsultan.addEventListener("change", updateKonsultanUI);
        updateKonsultanUI();
      }

      // Hak prioritas show/hide + required toggle
      const hak = document.getElementById("hak_prioritas");
      const hakFollow = document.getElementById("hak-prioritas-followup");
      const hakReqIds = ["negara","nomor_prioritas","tgl_penerimaan"];

      const setHakRequired = (on) => {
        hakReqIds.forEach(id => {
          const el = document.getElementById(id);
          if (!el) return;
          if (on) el.setAttribute("required", "required");
          else el.removeAttribute("required");
        });
      };

      const updateHakUI = () => {
        const isYa = hak?.value === "Ya";
        if (hakFollow) hakFollow.style.display = isYa ? "" : "none";
        setHakRequired(!!isYa);
      };

      if (hak) {
        hak.addEventListener("change", updateHakUI);
        updateHakUI();
      }

      // FIX: jumlah inventor dihitung dari inventor[nama][]
      document.querySelector("form").addEventListener("submit", () => {
        // hitung dari jumlah kartu inventor yang dirender
        const cards = document.querySelectorAll("#inventor-container-verif .inventor-card");
        document.getElementById("jumlah_inventor").value = cards.length || 1;
      });

    });
  </script>
</div>
@endsection
