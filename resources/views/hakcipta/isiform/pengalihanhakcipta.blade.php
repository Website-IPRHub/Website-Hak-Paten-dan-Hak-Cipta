@extends('layouts.app')

@section('title','Hak Cipta')

@section('content')

@php $activeStep = 3; @endphp
@include('hakcipta.isiform.menuformcipta')

<div class="paten-form-page">
  <div class="judul">
    <h2>Formulir Pendaftaran Ciptaan</h2>
    <p>Isi formulir ini untuk mendapatkan dokumen surat pengalihan hak cipta</p>
  </div>

    <form class="form" method="POST" action="{{ route('pengalihanhak.store') }}">
    @csrf

    {{-- ATAS: 2 KOLOM (kiri jenis, kanan judul/tanggal/jumlah) --}}
    <div class="form-grid-2col">
        {{-- KIRI --}}
        <div class="left-col">
        <div class="field">
            <label class="label">Jenis Hak Cipta <span class="req">*</span></label>

            @php
            $jenisOld = old('jenis_hak_cipta');
            $jenisLainnyaOld = old('jenis_hak_cipta_lainnya');
            @endphp

            <div class="jenis-radio">
            <label class="radio-item">
                <input type="radio" name="jenis_hak_cipta" value="Buku" {{ $jenisOld === 'Buku' ? 'checked' : '' }} required>
                Buku
            </label><br>

            <label class="radio-item">
                <input type="radio" name="jenis_hak_cipta" value="Program Komputer" {{ $jenisOld === 'Program Komputer' ? 'checked' : '' }}>
                Program Komputer
            </label><br>

            <label class="radio-item">
                <input type="radio" name="jenis_hak_cipta" value="Karya Rekaman Video" {{ $jenisOld === 'Karya Rekaman Video' ? 'checked' : '' }}>
                Karya Rekaman Video
            </label><br>

            <label class="radio-item">
                <input type="radio" name="jenis_hak_cipta" value="Lainnya" {{ $jenisOld === 'Lainnya' ? 'checked' : '' }}>
                Lainnya
            </label>
            </div>

            <div id="jenis-lainnya-wrap" style="display:none;">
            <input type="text" class="input" name="jenis_hak_cipta_lainnya"
                    placeholder="Tulis jenis hak cipta lainnya…" value="{{ $jenisLainnyaOld }}">
            <small style="color:#6b7280;">Isi jika anda memilih “Lainnya”.</small>
            </div>

            @error('jenis_hak_cipta') <small style="color:red">{{ $message }}</small> @enderror
            @error('jenis_hak_cipta_lainnya') <small style="color:red">{{ $message }}</small> @enderror
        </div>
        </div>

        {{-- KANAN --}}
        <div class="right-col">
        <div class="field">
            <label class="label">Judul Ciptaan <span class="req">*</span></label>
            <input type="text" class="input" name="judul_hak_cipta"
                placeholder="Masukkan judul ciptaan..." value="{{ old('judul_hak_cipta') }}" required>
            @error('judul_hak_cipta') <small class="err">{{ $message }}</small> @enderror
        </div>

        <div class="field">
            <label class="label">Tanggal Pengisian <span class="req">*</span></label>
            <input type="date" class="input" id="tanggal_pengisian" name="tanggal_pengisian"
                value="{{ old('tanggal_pengisian', now()->format('Y-m-d')) }}" required>
            @error('tanggal_pengisian') <small style="color:red">{{ $message }}</small> @enderror
        </div>

        <div class="field">
            <label class="label">Jumlah inventor <span class="req">*</span></label>
            <input type="number" class="input" id="jumlah_inventor" name="jumlah_inventor"
                min="1" max="20" value="{{ old('jumlah_inventor', 1) }}" required>
            @error('jumlah_inventor') <small style="color:red">{{ $message }}</small> @enderror
        </div>
        </div>
    </div>

    {{-- BAWAH: FULL WIDTH (di bawah 2 sisi) --}}
    <div class="field full-width" style="margin-top:16px;">
        <label class="label">Data Pencipta <span class="req">*</span></label>
        <div id="inventor-container"></div>
        @error('inventor') <small style="color:red">{{ $message }}</small> @enderror
        @error('inventor.*') <small style="color:red">{{ $message }}</small> @enderror
    </div>

    {{-- TEMPLATE PENCIPTA --}}
    <template id="inventor-template">
        <div class="inventor-card">
        <p class="inventor-head">Pencipta <span class="inv-no"></span></p>
        <div class="grid-2">
            <div class="field">
            <label class="label">Nama Lengkap <span class="req">*</span></label>
            <input type="text" class="input" name="inventor[nama][]" placeholder="Nama lengkap" required>
            </div>

            <div class="field">
            <label class="label">No. HP <span class="req">*</span></label>
            <input type="text" class="input" name="inventor[no_hp][]" placeholder="Contoh: 08xxxxxxxxxx" required>
            </div>

            <div class="field">
            <label class="label">NIK <span class="req">*</span></label>
            <input type="text" class="input" name="inventor[NIK][]" placeholder="Masukkan NIK Anda" required>
            </div>

            <div class="field">
            <label class="label">Email <span class="req">*</span></label>
            <input type="email" class="input" name="inventor[email][]" placeholder="nama@email.com" required>
            </div>

            <div class="field span-2">
            <label class="label">Alamat Lengkap (sesuai KTP) <span class="req">*</span></label>
            <textarea class="input" name="inventor[alamat][]" rows="3" placeholder="Alamat lengkap" required></textarea>
            </div>

            <div class="field">
            <label class="label">Kode Pos</label>
            <input type="text" class="input" name="inventor[kode_pos][]" placeholder="Contoh: XXXXX" required>
            </div>
        </div>
        </div>
    </template>
    {{-- ACTIONS BAR --}}
    <div class="actions-bar">
        <div class="actions-left">
        <button
            type="button"
            class="btn-prev"
            data-fallback="{{ route('hakcipta.isiform.suratpernyataan') }}"
            onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
        >
            &laquo; Sebelumnya
        </button>

        <a class="btn-next" href="{{ route('hakcipta.isiform.peralihanverifcipta') }}">
            Selanjutnya &raquo;
        </a>
        </div>

        <div class="actions-right" style="display:flex; gap:10px;">
        <button class="unduh" type="submit" name="action" value="download">
            Unduh
        </button>
        </div>
    </div>
    </form>

</div>
@endsection
