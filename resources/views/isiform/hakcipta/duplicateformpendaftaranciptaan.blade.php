@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')


@php 
  $activeStep = 2; 
@endphp

@include('isiform.hakcipta.duplicatemenuformcipta')

@php
  // Ambil ID referensi
  $ref = request('ref') ?? session('edit_ref_id');
  
  // Ambil data session spesifik ID (hasil jemputan Controller Dashboard tadi)
  $data = $ref 
      ? session("hakcipta.form.$ref", session('hakcipta.form', [])) 
      : session('hakcipta.form', []);

  // Variabel buat script inventor biar gak error
  $prefill = $data; 
@endphp

<script type="application/json" id="prefill-inventor-data">
{!! json_encode($data['inventor'] ?? []) !!}
</script>

<script type="application/json" id="prefill-count">
{!! json_encode($data['jumlah_inventor'] ?? 1) !!}
</script>

<div class="paten-form-page">
  <div class="judul">
    <h2>Formulir Pendaftaran Ciptaan</h2>
    <p>Isi formulir ini untuk mendapatkan dokumen formulir pendaftaran hak cipta</p>
  </div>

  <form class="form" method="POST" action="{{ route('dup.isiformCipta.store') }}">
    @csrf
    <input type="hidden" name="ref" value="{{ $ref }}">

    {{-- GRID UTAMA --}}
    <div class="grid-2">
      @php
    $jumlahInventor = old('jumlah_inventor', $data['jumlah_inventor'] ?? 1);
@endphp

      <div class="field">
          <label class="label">Jumlah pencipta <span class="req">*</span></label>
          <div class="jumlah-inventor-wrap" style="display:flex; gap:10px; align-items:center;">
            <button type="button" id="invMinus" class="btn-minus" aria-label="Kurangi inventor">-</button>

          <input
              type="number"
              class="input"
              id="jumlah_inventor"
              name="jumlah_inventor"
              min="1"
              max="20"
              value="{{ $jumlahInventor }}"
              required
          >
           <button type="button" id="invPlus" class="btn-plus" aria-label="Tambah inventor">+</button>
          </div>
          @error('jumlah_inventor')
              <small class="err">{{ $message }}</small>
          @enderror
      </div>

      <div class="field">
   @php
    $jenisOld = old('jenis_cipta', $data['jenis_cipta'] ?? null);
    $jenisLainnyaOld = old('jenis_cipta_lainnya', $data['jenis_cipta_lainnya'] ?? null);
@endphp

    <label class="label">Jenis Hak Cipta <span class="req">*</span></label>

    <div class="jenis-radio">
        <label class="radio-item">
            <input type="radio" name="jenis_cipta" value="Buku"
                {{ $jenisOld === 'Buku' ? 'checked' : '' }} required>
            Buku
        </label>

        <label class="radio-item">
            <input type="radio" name="jenis_cipta" value="Program Komputer"
                {{ $jenisOld === 'Program Komputer' ? 'checked' : '' }} required>
            Program Komputer
        </label>

        <label class="radio-item">
            <input type="radio" name="jenis_cipta" value="Karya Rekaman Video"
                {{ $jenisOld === 'Karya Rekaman Video' ? 'checked' : '' }} required>
            Karya Rekaman Video
        </label>

        <label class="radio-item">
            <input type="radio" name="jenis_cipta" value="Lainnya"
                {{ $jenisOld === 'Lainnya' ? 'checked' : '' }} required>
            Lainnya
        </label>
    </div>

    <div id="jenis-lainnya-wrap" class="mt-8" style="display:none;">
        <input
            type="text"
            class="input"
            name="jenis_cipta_lainnya"
            value="{{ old('jenis_cipta_lainnya', $data['jenis_cipta_lainnya'] ?? '') }}"
            placeholder="Sebutkan jenis ciptaan lainnya"
        >
        <small class="hint">Isi jika memilih “Lainnya”.</small>
    </div>

      @error('jenis_cipta') <small class="err">{{ $message }}</small> @enderror
      @error('jenis_cipta_lainnya') <small class="err">{{ $message }}</small> @enderror
  </div>

      <div class="field">
        <label class="label">Link Ciptaan <span class="req">*</span></label>
        <input
          type="url"
          class="input"
          name="link_ciptaan"
          placeholder="Contoh: https://drive.google.com/..."
          value="{{ old('link_ciptaan', $data['link_ciptaan'] ?? '') }}"
          required
        >
        @error('link_ciptaan') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field span-2">
        <label class="label">Judul Ciptaan <span class="req">*</span></label>
        <input
          type="text"
          class="input"
          name="judul_ciptaan"
          placeholder="Masukkan judul ciptaan"
          value="{{ old('judul_ciptaan', $data['judul_ciptaan'] ?? '') }}"
          required
        >
        @error('judul_ciptaan') <small class="err">{{ $message }}</small> @enderror
      </div>
      <div class="field span-2">
        <label class="label">Produk Ciptaan Berupa? <span class="req">*</span></label>
        <input
          type="text"
          class="input"
          name="berupa"
          placeholder="Produk ciptaan berupa..."
          value="{{ old('berupa', $data['berupa'] ?? '') }}"
          required
        >
        @error('berupa') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Tanggal Pengisian <span class="req">*</span></label>
        <input
          type="date"
          class="input"
          id="tanggal_pengisian"
          name="tanggal_pengisian"
          value="{{ old('tanggal_pengisian', $data['tanggal_pengisian'] ?? now()->format('Y-m-d')) }}"
        >
        @error('tanggal_pengisian') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field span-2">
        <label class="label">Tempat Pengisian <span class="req">*</span></label>
        <p class="hint">Tempat saat anda mengisi form ini.</p>
        <input
          type="text"
          class="input"
          name="tempat"
          placeholder="Contoh: Semarang"
          value="{{ old('tempat', $data['tempat'] ?? '') }}"
          required
        >
        @error('tempat') <small class="err">{{ $message }}</small> @enderror
      </div>
    </div>

    {{-- DATA PENCIPTA --}}
    <div class="nama mt-16">
      <div class="field">
        <label class="label">Data Pencipta <span class="req">*</span></label>
        <div id="inventor-container"></div>
        @error('inventor') <small class="err">{{ $message }}</small> @enderror
        @error('inventor.*') <small class="err">{{ $message }}</small> @enderror
      </div>
    </div>

    {{-- ULASAN (DI ATAS BUTTON) --}}
    <div class="field mt-16">
      <label class="label">Ulasan Ciptaan <span class="req">*</span></label>
      <p class="hint">Tulis singkat (± 2–3 kalimat).</p>
      <textarea
        class="input input-full"
        name="uraian"
        rows="4"
        maxlength="350"
        placeholder="Masukkan uraian produk ciptaan"
        required
      >{{ old('uraian', $data['uraian'] ?? '') }}</textarea>

      @error('uraian') <small class="err">{{ $message }}</small> @enderror
    </div>

    {{-- ACTIONS BAR --}}
    {{-- ACTIONS BAR --}}
<div class="actions-bar">
  <div class="actions-left">
    {{-- ✅ KASIH ID "nextLinkIsiform" BIAR SWEETALERT JALAN --}}
    <button
      type="button" 
      id="nextLinkIsiform"
      class="btn-next"
      data-save-url="{{ route('dup.isiformCipta.store') }}"
      data-next-url="{{ route('pemohon.dashboard') }}"
    >
      Simpan Perubahan
    </button>
  </div>
       <script>
document.addEventListener('DOMContentLoaded', () => {
  const nextBtn = document.getElementById('nextLinkIsiform');
  if (!nextBtn) return;

  nextBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    const form = nextBtn.closest('form');
    if (!form) return;

    // 1. Validasi Input (Wajib isi yang ada tanda *)
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    // --- PROSES SIMPAN KE SESSION ---
    const saveUrl = nextBtn.dataset.saveUrl;
    const nextUrl = nextBtn.dataset.nextUrl;
    const fd = new FormData(form);
    
    // Kita kirim action 'save' agar Controller menjalankan session()->put()
    fd.set('action', 'save'); 

    try {
      // Tampilkan loading sebentar
      Swal.fire({
        title: 'Memproses Data...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });

      const res = await fetch(saveUrl, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
      });

      if (res.ok) {
        // 2. SETELAH DATA MASUK SESSION, BARU TANYA DOWNLOAD
        const result = await Swal.fire({
          title: 'Data Disimpan Sementara',
          html: `
            <p>Data perubahan Anda sudah tersimpan di sistem (Sesi).</p>
            <hr style="margin:15px 0;">
            <p><b>Penting:</b> Pastikan Anda mendownload ulang dokumen dengan data terbaru sebelum melanjutkan ke tahap verifikasi:</p>
            <ul style="text-align:left; margin-top:10px; list-style:none; font-size:15px;">
              <li>• Form Paten</li>
              <li>• Surat Pengalihan Hak</li>
              <li>• Kepemilikan Invensi</li>
            </ul>
          `,
          icon: 'success',
          showCancelButton: true,
          confirmButtonText: 'Sudah Download Semua',
          cancelButtonText: 'Belum, Mau Download Dulu',
          confirmButtonColor: '#2F5C9E',
          cancelButtonColor: '#6c757d',
          reverseButtons: true
        });

       if (result.isConfirmed) {
            // Jika klik "Sudah Download", arahkan ke Dashboard Pemohon
            const nextUrl = nextBtn.dataset.nextUrl; // Ini akan mengambil route('pemohon.dashboard')
            
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data disimpan. Kembali ke Dashboard...',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.href = nextUrl;
            }, 1500);
        } else {
            // Jika klik "Belum", biarkan tetap di halaman ini agar bisa download dulu
            Swal.fire({
                title: 'Silakan Download',
                text: 'Gunakan tombol download di bawah sebelum kembali ke dashboard.',
                icon: 'info'
            });
        }

      } else {
        Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan ke session.', 'error');
      }
    } catch (err) {
      console.error(err);
      Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
    }
  });
});
</script>


      <div class="actions-download">
        <select id="doc_type" class="input" style="width:220px;">
          <option value="" selected disabled>-- Pilih Dokumen --</option>
         <option value="{{ route('dup.isiformCipta.store') }}">Formulir Permohonan Pendaftaran Ciptaan</option>
        <option value="{{ route('dup.pernyataanCipta.store') }}">Surat Pernyataan</option>
        <option value="{{ route('dup.pengalihanhakCipta.store') }}">Surat Pengalihan Hak Cipta</option>
        </select>

        <select name="download_format" class="input" style="width:160px;">
          <option value="pdf">PDF</option>
          <option value="docx">DOCX</option>
        </select>

        <button type="submit" class="unduh" id="btnDownload">
          ⬇ Download
        </button>
      </div>

      <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('doc_type');
            const form = document.querySelector('form');

            select.addEventListener('change', function () {
                form.action = this.value;
            });
        });
        </script>
    </div>

  </form>

  {{-- TEMPLATE PENCIPTA --}}
  <template id="inventor-template">
    <div class="inventor-card">
      
      <p class="inventor-head">
        Pencipta <span class="inv-no"></span>
      </p>

      <div class="grid-2">

        {{-- Nama --}}
        <div class="field">
          <label class="label">Nama Pencipta <span class="req">*</span></label>
          <input type="text"
                class="input"
                name="inventor[nama][]"
                placeholder="Nama lengkap"
                required>
        </div>

        {{-- NIK --}}
        <div class="field">
          <label class="label">NIK <span class="req">*</span></label>
          <input type="text"
                class="input"
                name="inventor[NIK][]"
                placeholder="Masukkan NIK Anda"
                required>
        </div>

        {{-- NIP/NIM --}}
        <div class="field">
          <label class="label">NIP/NIM <span class="req">*</span></label>
          <input type="text"
                class="input nip-input"
                name="inventor[nip_nim][]"
                placeholder="Masukkan NIP/NIM Anda"
                required>
          <small class="nip-warning">
            NIP/NIM harus terdiri dari 14 atau 18 digit angka
          </small>
        </div>

        {{-- Fakultas --}}
        <div class="field">
          <label class="label">Fakultas <span class="req">*</span></label>
          <select class="input"
                  name="inventor[fakultas][]"
                  required>
            <option value="" disabled selected>-- Pilih Fakultas --</option>
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

        {{-- NIDN (khusus dosen) --}}
        <div class="field nidn-wrap" style="display:none;">
          <label class="label">NIDN <span class="req">*</span></label>
          <input type="text"
                class="input nidn-input"
                name="inventor[nidn][]"
                placeholder="8 digit NIDN">
          <small class="nidn-warning">
            NIDN harus 8 digit angka
          </small>
        </div>

        {{-- Status --}}
        <div class="field">
          <label class="label">Status <span class="req">*</span></label>
          <select class="input status-select"
                  name="inventor[status][]"
                  required>
            <option value="" disabled selected>-- Pilih Status --</option>
            <option value="Dosen">Dosen</option>
            <option value="Mahasiswa">Mahasiswa</option>
          </select>
        </div>

        {{-- No HP --}}
        <div class="field">
          <label class="label">No. HP <span class="req">*</span></label>
          <input type="text"
                class="input"
                name="inventor[no_hp][]"
                placeholder="Contoh: 08xxxxxxxxxx"
                required>
        </div>

        {{-- Telp Rumah --}}
        <div class="field">
          <label class="label">Telp Rumah</label>
          <input type="text"
                class="input"
                name="inventor[tlp_rumah][]"
                placeholder="Contoh: 021-1234567">
        </div>

        {{-- Email --}}
        <div class="field">
          <label class="label">Email <span class="req">*</span></label>
          <input type="email"
                class="input"
                name="inventor[email][]"
                placeholder="nama@email.com"
                required>
        </div>

       <div class="field span-2">
  <label class="label">Alamat Lengkap (sesuai KTP) <span class="req">*</span></label>
  {{-- ✅ Pastikan name="inventor[alamat][]" (huruf kecil) sesuai keys di JS --}}
  <textarea class="input" name="inventor[alamat][]" rows="3" placeholder="Alamat lengkap" required></textarea>
</div>

<div class="field">
  <label class="label">Kode Pos <span class="req">*</span></label>
  {{-- ✅ Pastikan name="inventor[kode_pos][]" --}}
  <input type="text" class="input" name="inventor[kode_pos][]" placeholder="Contoh: 50275" required>
</div>
      </div>
    </div>
  </template>

 <script type="application/json" id="old-inventor-data">
    {!! json_encode(old('inventor', $data['inventor'] ?? [])) !!}
</script>

  
</div>
@endsection
