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

    {{-- 1) Jenis paten + Nomor PCT --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Jenis Pengajuan Paten <span class="req">*</span></label>
          <select class="input" name="jenis_paten" required>
            <option value="" disabled {{ old('jenis_paten') ? '' : 'selected' }}>-- Jenis Pengajuan Paten --</option>
            <option value="Paten" {{ old('jenis_paten') == 'Paten' ? 'selected' : '' }}>Paten</option>
            <option value="Paten Sederhana" {{ old('jenis_paten') == 'Paten Sederhana' ? 'selected' : '' }}>Paten Sederhana</option>
          </select>
          @error('jenis_paten') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="col">
        <div class="field">
          <label class="label">Nomor Permohonan Paten Internasional (PCT) <span class="req">*</span></label>
          <input type="text" class="input" name="nomor_permohonan"
                placeholder="Masukkan nomor permohonan paten internasional"
                value="{{ old('nomor_permohonan') }}" required>
          @error('nomor_permohonan') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    {{-- 2) Judul invensi + Pecahan paten --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Judul Invensi <span class="req">*</span></label>
          <input type="text" class="input" name="judul_invensi"
                placeholder="Masukkan judul draft paten"
                value="{{ old('judul_invensi') }}" required>
          @error('judul_invensi') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="col">
        <div class="field">
          <label class="label">Permohonan Paten ini merupakan pecahan dari permohonan paten nomor <span class="req">*</span></label>
          <input type="text" class="input" name="pecahan_paten"
                placeholder="Masukkan nomor permohonan paten"
                value="{{ old('pecahan_paten') }}" required>
          @error('pecahan_paten') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    {{-- 3) Konsultan + Jumlah inventor (tombol +/-) --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Apakah Melalui Konsultan Paten? <span class="req">*</span></label>
          <select class="input" id="konsultanpaten" name="konsultanpaten" required>
            <option value="" disabled {{ old('konsultanpaten') ? '' : 'selected' }}>-- Pilih --</option>
            <option value="Melalui" {{ old('konsultanpaten') == 'Melalui' ? 'selected' : '' }}>Melalui</option>
            <option value="Tidak Melalui" {{ old('konsultanpaten') == 'Tidak Melalui' ? 'selected' : '' }}>Tidak Melalui</option>
          </select>
          @error('konsultanpaten') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>

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

    {{-- 4) Follow-up Konsultan --}}
    <div class="field" id="konsultan-followup" @if(old('konsultanpaten') !== 'Melalui') style="display:none;" @endif>
      <div class="row-2">
        <div class="col">
          <div class="field">
            <label class="label">Nama Badan Hukum <span class="req">*</span></label>
            <input type="text" class="input" id="nama_badan_hukum" name="nama_badan_hukum"
                  value="{{ old('nama_badan_hukum') }}" placeholder="Masukkan nama badan hukum"
                  @if(old('konsultanpaten') === 'Melalui') required @endif>
            @error('nama_badan_hukum') <small style="color:red">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label class="label">Nama Konsultan Paten <span class="req">*</span></label>
            <input type="text" class="input" id="nama_konsultan_paten" name="nama_konsultan_paten"
                  value="{{ old('nama_konsultan_paten') }}" placeholder="Masukkan nama konsultan paten"
                  @if(old('konsultanpaten') === 'Melalui') required @endif>
            @error('nama_konsultan_paten') <small style="color:red">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label class="label">Nomor Konsultan Paten <span class="req">*</span></label>
            <input type="text" class="input" id="nomor_konsultan_paten" name="nomor_konsultan_paten"
                  value="{{ old('nomor_konsultan_paten') }}" placeholder="Masukkan nomor konsultan paten"
                  @if(old('konsultanpaten') === 'Melalui') required @endif>
            @error('nomor_konsultan_paten') <small style="color:red">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="col">
          <div class="field">
            <label class="label">Alamat Badan Hukum <span class="req">*</span></label>
            <input type="text" class="input" id="alamat_badan_hukum" name="alamat_badan_hukum"
                  value="{{ old('alamat_badan_hukum') }}" placeholder="Masukkan alamat badan hukum"
                  @if(old('konsultanpaten') === 'Melalui') required @endif>
            @error('alamat_badan_hukum') <small style="color:red">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label class="label">Alamat Konsultan Paten <span class="req">*</span></label>
            <input type="text" class="input" id="alamat_konsultan_paten" name="alamat_konsultan_paten"
                  value="{{ old('alamat_konsultan_paten') }}" placeholder="Masukkan alamat konsultan paten"
                  @if(old('konsultanpaten') === 'Melalui') required @endif>
            @error('alamat_konsultan_paten') <small style="color:red">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label class="label">Telepon/Fax <span class="req">*</span></label>
            <input type="text" class="input" id="telepon_fax" name="telepon_fax"
                  value="{{ old('telepon_fax') }}" placeholder="Masukkan telepon/fax"
                  @if(old('konsultanpaten') === 'Melalui') required @endif>
            @error('telepon_fax') <small style="color:red">{{ $message }}</small> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- 5) Inventor + Lampiran --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Nama dan kewarganegaraan para inventor <span class="req">*</span></label>

          <div id="inventor-container"></div>

          @error('inventor') <small style="color:red">{{ $message }}</small> @enderror
          @error('inventor.*') <small style="color:red">{{ $message }}</small> @enderror
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
            placeholder="Jika pilih dokumen lain, tulis di sini...">{{ old('lampiran_lainnya') }}</textarea>

          @error('lampiran') <small style="color:red">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    <template id="inventor-template">
      <div class="inventor-row">
        <label class="label inventor-label">Inventor</label>
        <input type="text" class="input inventor-input" name="inventor[]" placeholder="Nama Kewarganegaraan" required>
      </div>
    </template>

    {{-- 6) Hak prioritas + Gambar abstrak --}}
    <div class="row-2">
      <div class="col">
        <div class="field">
          <label class="label">Permohonan paten ini diajukan dengan/tidak dengan Hak prioritas <span class="req">*</span></label>
          <select class="input" id="hak_prioritas" name="hak_prioritas" required>
            <option value="" disabled {{ old('hak_prioritas') ? '' : 'selected' }}>-- Pilih --</option>
            <option value="Ya" {{ old('hak_prioritas') == 'Ya' ? 'selected' : '' }}>Ya</option>
            <option value="Tidak" {{ old('hak_prioritas') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
          </select>
          @error('hak_prioritas') <small style="color:red">{{ $message }}</small> @enderror
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
        <p>Format: Tanggal/Bulan/Tahun<br>
        Contoh: 13/05/2026
        </p>
        <input type="text" class="input" id="tgl_penerimaan" name="tgl_penerimaan"
              value="{{ old('tgl_penerimaan') }}" placeholder="Masukkan Tanggal Penerimaan Permohonan"
              @if(old('hak_prioritas') === 'Ya') required @endif>
        @error('tgl_penerimaan') <small style="color:red">{{ $message }}</small> @enderror
      </div>
    </div>

    {{-- 7) Uraian/Abstrak/Klaim/Gambar --}}
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

    <div class="actions-bar">
      <div class="actions-left">
        <button
          type="button"
          class="btn-prev"
          data-fallback="{{ route('hakpaten.draftpatenisiformulir') }}"
          onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
        >&laquo; Sebelumnya</button>

        <a class="btn-next" href="{{ route('hakpaten.invensiformulir') }}">
          Selanjutnya &raquo;
        </a>
      </div>

      <div class="actions-right" style="display:flex; gap:10px; align-items:center;">
        <select name="download_format" class="input" style="width:160px;">
          <option value="pdf" {{ old('download_format','pdf')=='pdf' ? 'selected' : '' }}>PDF</option>
          <option value="docx" {{ old('download_format')=='docx' ? 'selected' : '' }}>DOCX</option>
        </select>

        <button class="unduh" type="submit" name="action" value="download">Unduh</button>
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

        const container = document.getElementById("inventor-container");
        const tpl = document.getElementById("inventor-template");
        const oldInventorEl = document.getElementById("old-inventor-data");

        let oldInventor = [];
        try { oldInventor = JSON.parse(oldInventorEl?.textContent || "[]"); } catch(e) { oldInventor = []; }

        const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

        const renderInventors = (count) => {
          if (!container || !tpl) return;

          // simpan nilai yang sudah diketik sebelum render ulang
          const existing = Array.from(container.querySelectorAll('input[name="inventor[]"]'))
            .map(i => i.value);

          container.innerHTML = "";

          for (let i = 0; i < count; i++) {
            const node = tpl.content.cloneNode(true);
            const input = node.querySelector('input[name="inventor[]"]');

            // prioritas isi: existing (kalau user sudah ketik) -> oldInventor (kalau dari server) -> kosong
            const v = (existing[i] ?? oldInventor[i] ?? "");
            if (input) input.value = v;

            container.appendChild(node);
          }
        };

        const getCount = () => {
          const n = parseInt(jumlahInput?.value || "1", 10);
          return clamp(isNaN(n) ? 1 : n, 1, 20);
        };

        const setCount = (n) => {
          const v = clamp(n, 1, 20);
          if (jumlahInput) jumlahInput.value = v;
          renderInventors(v);
        };

        // init
        setCount(getCount());

        // tombol +/- (tidak submit form)
        if (btnMinus) btnMinus.addEventListener("click", () => setCount(getCount() - 1));
        if (btnPlus)  btnPlus.addEventListener("click", () => setCount(getCount() + 1));

        // Konsultan followup show/hide + required toggle
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

        // Hak prioritas followup show/hide + required toggle
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

        document.querySelector("form").addEventListener("submit", () => {
          const inputs = document.querySelectorAll('input[name="inventor[]"]');
          document.getElementById("jumlah_inventor").value = inputs.length;
        });
      });
    </script>
</div>
@endsection