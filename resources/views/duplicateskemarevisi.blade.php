@extends('layouts.app')

@section('title','Revisi Skema Pengembangan')

@section('content')
<div class="judul">
  <h2>Skema Penelitian Pengembangan (TKT 7 - 9)</h2>
  <p>Catatan: Isi form ini untuk menghasilkan surat pernyataan TKT 7-9.<br>
      Form ini diisi oleh Inventor 1 (Dosen)</p>
<p>Upload surat pernyataan skema pengembangan HARUS sudah dilengkapi dengan tanda tangan dan bermaterai</p>
</div>

@if(session('success'))
  <div class="alert-success">
    {{ session('success') }}
  </div>
@endif

{{-- FORM DOWNLOAD (wajib membungkus semua input) --}}
<form id="downloadForm" class="form" method="POST"
      action="{{ route('patenverif.skema.download', ['verif' => $verif->id]) }}">
  @csrf

  <div class="grid-2">
    {{-- KIRI (3 field) --}}
    <div class="col">
      <div class="field">
        <label class="label">Nama Lengkap <span class="req">*</span></label>
        <input class="input" name="nama_lengkap" placeholder="Masukkan nama lengkap Dosen (Inventor 1)"
               value="{{ old('nama_lengkap', $draft['nama_lengkap'] ?? '') }}" required>
        @error('nama_lengkap') <small class="error">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Program Studi <span class="req">*</span></label>
        <input class="input" name="program_studi" placeholder="Masukkan program studi"
               value="{{ old('program_studi', $draft['program_studi'] ?? '') }}" required>
        @error('program_studi') <small class="error">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Judul Paten <span class="req">*</span></label>
        <input class="input" name="judul_paten" placeholder="Masukkan judul paten"
               value="{{ old('judul_paten', $draft['judul_paten'] ?? ($verif->judul_paten ?? '')) }}" required>
        @error('judul_paten') <small class="error">{{ $message }}</small> @enderror
      </div>
    </div>

    {{-- KANAN (3 field) --}}
    <div class="col">
      <div class="field">
        <label class="label">NIDN/NIP <span class="req">*</span></label>
        <input
    type="text"
    class="input nidn-nip-input"
    name="nidn_nip"
    placeholder="Masukkan NIDN/NIP Anda"
    value="{{ old('nidn_nip', $draft['nidn_nip'] ?? '') }}"
    required
>
                      <small class="nip-warning">
                        NIDN/NIP harus terdiri dari 8 atau 18 digit angka
                      </small>
        @error('nidn_nip') <small class="error">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        @php
          $fakultasValue = old('fakultas', $draft['fakultas'] ?? '');
        @endphp
        <label class="label">Fakultas <span class="req">*</span></label>
        <select class="input" name="fakultas" required>
          <option value="" disabled @selected(!$fakultasValue)>-- Pilih Fakultas --</option>
          <option value="Fakultas Teknik" @selected($fakultasValue=='Fakultas Teknik')>Fakultas Teknik</option>
          <option value="Fakultas Sains dan Matematika" @selected($fakultasValue=='Fakultas Sains dan Matematika')>Fakultas Sains dan Matematika</option>
          <option value="Fakultas Kesehatan Masyarakat" @selected($fakultasValue=='Fakultas Kesehatan Masyarakat')>Fakultas Kesehatan Masyarakat</option>
          <option value="Fakultas Kedokteran" @selected($fakultasValue=='Fakultas Kedokteran')>Fakultas Kedokteran</option>
          <option value="Fakultas Perikanan dan Ilmu Kelautan" @selected($fakultasValue=='Fakultas Perikanan dan Ilmu Kelautan')>Fakultas Perikanan dan Ilmu Kelautan</option>
          <option value="Fakultas Peternakan dan Pertanian" @selected($fakultasValue=='Fakultas Peternakan dan Pertanian')>Fakultas Peternakan dan Pertanian</option>
          <option value="Fakultas Psikologi" @selected($fakultasValue=='Fakultas Psikologi')>Fakultas Psikologi</option>
          <option value="Fakultas Hukum" @selected($fakultasValue=='Fakultas Hukum')>Fakultas Hukum</option>
          <option value="Fakultas Ilmu Sosial dan Ilmu Politik" @selected($fakultasValue=='Fakultas Ilmu Sosial dan Ilmu Politik')>Fakultas Ilmu Sosial dan Ilmu Politik</option>
          <option value="Fakultas Ilmu Budaya" @selected($fakultasValue=='Fakultas Ilmu Budaya')>Fakultas Ilmu Budaya</option>
          <option value="Fakultas Ekonomi dan Bisnis" @selected($fakultasValue=='Fakultas Ekonomi dan Bisnis')>Fakultas Ekonomi dan Bisnis</option>
          <option value="Sekolah Vokasi" @selected($fakultasValue=='Sekolah Vokasi')>Sekolah Vokasi</option>
          <option value="Sekolah Pasca Sarjana" @selected($fakultasValue=='Sekolah Pasca Sarjana')>Sekolah Pasca Sarjana</option>
        </select>
        @error('fakultas') <small class="error">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label class="label">Tanggal Pengisian <span class="req">*</span></label>
        <input type="date" class="input" id="tanggal_pengisian" name="tanggal_pengisian"
               value="{{ old('tanggal_pengisian', $draft['tanggal_pengisian'] ?? now()->format('Y-m-d')) }}" required>
        @error('tanggal_pengisian') <small class="error">{{ $message }}</small> @enderror
      </div>
    </div>
  </div>
</form>

{{-- ACTIONS BAR --}}
<div class="skm-actions">
  <div class="skm-actions__left">
    {{-- ✅ 1. Tipe ganti jadi 'button' (biar gak lari ke route next otomatis) --}}
    {{-- ✅ 2. HAPUS atribut form="downloadForm" --}}
    {{-- ✅ 3. Kasih ID 'btnSaveSkema' biar script lo nangkep --}}
    {{-- ✅ 4. Kasih dataset biar JS tau harus nembak URL mana --}}
    <button type="button" 
            id="btnSaveSkema" 
            class="skm-btn skm-btn--prev"
            data-save-url="{{ route('dup.skema.download', ['verif' => $verif->id]) }}"
            data-next-url="{{ route('pemohon.dashboard') }}">
        Simpan Perubahan
    </button>
  </div>

  <div class="actions-right">
    {{-- tombol unduh submit ke form download --}}
    
    <div class="actions-right2" style="display:flex; gap:10px; align-items:center;">
        <select form="downloadForm" name="download_format" class="input" style="width:160px;">
          @php
  $downloadFormat = old('download_format', $draft['download_format'] ?? 'pdf');
@endphp

<option value="pdf"  {{ $downloadFormat == 'pdf' ? 'selected' : '' }}>PDF</option>
<option value="docx" {{ $downloadFormat == 'docx' ? 'selected' : '' }}>DOCX</option>
        </select>

        <button form="downloadForm" class="unduh" type="submit" name="action" value="download">
          Unduh
        </button>

      </div>

  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const draftFile = document.getElementById('draftFile');
    const fileName = document.getElementById('fileName');
    const draftForm = document.getElementById('draftForm');
    const downloadForm = document.getElementById('downloadForm');
    const uploadButtonLabel = document.getElementById('uploadButtonLabel');
    const btnSaveSkema = document.getElementById('btnSaveSkema');

    // --- LOGIC 1: AUTO UPLOAD SAAT PILIH FILE ---
    if (draftFile && draftForm && downloadForm) {
        draftFile.addEventListener('change', () => {
            const file = draftFile.files[0];
            if (!file) return;

            if (fileName) fileName.textContent = file.name;
            if (uploadButtonLabel) {
                uploadButtonLabel.style.pointerEvents = 'none';
                uploadButtonLabel.textContent = 'Mengupload...';
            }

            // Copy data dari form input ke form upload biar session ke-update
            const fields = ['nama_lengkap','program_studi','judul_paten','nidn_nip','fakultas','tanggal_pengisian','download_format'];
            fields.forEach(name => {
                const val = downloadForm.querySelector(`[name="${name}"]`).value;
                let hidden = draftForm.querySelector(`input[name="${name}"]`);
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = name;
                    draftForm.appendChild(hidden);
                }
                hidden.value = val;
            });
            draftForm.submit();
        });
    }

    // --- LOGIC 2: SWEETALERT SAAT KLIK SIMPAN ---
    if (btnSaveSkema) {
        btnSaveSkema.addEventListener('click', async (e) => {
            e.preventDefault();
            
            if (!downloadForm.checkValidity()) {
                downloadForm.reportValidity();
                return;
            }

            const saveUrl = btnSaveSkema.dataset.saveUrl;
            const nextUrl = btnSaveSkema.dataset.nextUrl;
            const fd = new FormData(downloadForm);
            fd.set('action', 'save');

            try {
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
                    const result = await Swal.fire({
                        title: 'Data Disimpan Sementara',
                        html: `
                            <p>Data perubahan Anda sudah tersimpan di sistem.</p>
                            <hr style="margin:15px 0;">
                            <p><b>Penting:</b> Pastikan Anda sudah mendownload ulang dokumen TKT terbaru.</p>
                            <ul style="text-align:left; margin-top:10px; list-style:none; font-size:16px;">
                                <li>• Surat Pernyataan TKT 7-9</li>
                            </ul>
                        `,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Sudah Download',
                        cancelButtonText: 'Belum, Download Dulu',
                        confirmButtonColor: '#2F5C9E',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    });

                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Kembali ke Dashboard...',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => { window.location.href = nextUrl; }, 1500);
                    } else {
                        Swal.fire({
                            title: 'Silakan Download',
                            text: 'Gunakan tombol Unduh di bawah untuk mendapatkan file terbaru.',
                            icon: 'info'
                        });
                    }
                } else {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan data.', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
            }
        });
    }
});
</script>
@endsection
