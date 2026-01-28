@extends('layouts.app')

@section('title', 'Dashboard Pemohon')

@vite(['resources/css/dashboardpemohon.css'])

@section('content')
<main class="pd-main">
  <div class="pd-container">

    <div class="pd-topbar">
      <div>
        <h2 class="pd-title">Pemantauan Status</h2>
        <div class="pd-sub">Pantau Pengajuan Kekayaan Intelektual di Sini.</div>
      </div>

      <div class="pd-actions">
        <button type="button" class="pd-user-btn" id="openAccount">
          <span class="pd-avatar">{{ strtoupper(substr($pemohon['username'], 0, 2)) }}</span>
          <span class="pd-user-text">
            <span class="pd-user-name">{{ $pemohon['username'] }}</span>
            <span class="pd-user-role">{{ $pemohon['kategori'] }}</span>
          </span>
          <span class="pd-caret">▾</span>
        </button>

        <form method="POST" action="{{ route('pemohon.logout') }}">
          @csrf
          <button class="pd-logout" type="submit">Logout</button>
        </form>
      </div>
    </div>

    <section class="pd-card pd-card--full">
      <div class="pd-card-head">
        <div class="pd-card-title">Pemantauan Status</div>
        <div class="pd-note">Status pengajuan saat ini</div>
      </div>

      <div class="pd-tracker" data-active="{{ $activeStatus }}">
        @foreach($steps as $s)
          <div class="pd-step" data-step="{{ $s['key'] }}">
            <div class="pd-dot"></div>

            <div class="pd-step-body">
              <div class="pd-step-title">{{ $s['label'] }}</div>
              <div class="pd-step-sub">Terakhir diperbarui: {{ $s['updated_at'] }}</div>

              {{-- =========================
                   REVISI (TETAP - TIDAK DIUBAH)
                   ========================= --}}
              @if(($s['key'] === 'revisi') && (($status ?? '') === 'revisi'))
                <button type="button" id="btnRevisi" class="pd-mini-btn" style="margin-top:10px;">
                  Detail Revisi
                </button>

                <div id="boxRevisi" class="pd-revisi-box" style="display:none; margin-top:10px;">
                  <div class="pd-revisi-title">Dokumen yang Perlu Direvisi</div>

                  <table class="pd-revisi-table">
                    <thead>
                      <tr>
                        <th>Dokumen</th>
                        <th>Catatan</th>
                        <th>File Resivi</th>
                        <th>Status</th>
                        <th>Upload Revisi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($revisiDocs ?? [] as $d)
                        @php
                          $labels = [
                            'draft_paten'=>'Draft Paten',
                            'form_permohonan'=>'Form Permohonan',
                            'surat_kepemilikan'=>'Surat Kepemilikan',
                            'surat_pengalihan'=>'Surat Pengalihan',
                            'scan_ktp'=>'Scan KTP',
                            'tanda_terima'=>'Tanda Terima',
                            'gambar_prototipe'=>'Gambar Prototipe',
                            'surat_permohonan'=>'Surat Permohonan',
                            'surat_pernyataan'=>'Surat Pernyataan',
                            'hasil_ciptaan'=>'Hasil Ciptaan',
                          ];
                          $docLabel = $labels[$d->doc_key] ?? $d->doc_key;

                          // cek apakah admin sudah "request" di revisions utk doc ini
                          $req = ($revRowsByDoc[$d->doc_key][0] ?? null);
                          $pemohonUploaded = $req && !empty($req->pemohon_file_path);
                        @endphp

                        <tr>
                          <td>{{ $docLabel }}</td>
                          <td>{{ $d->note ?? '-' }}</td>

                          <td>
                            @if(!empty($d->admin_attachment_path))
                              <a href="{{ asset('storage/'.$d->admin_attachment_path) }}" target="_blank">Download</a>
                            @else
                              -
                            @endif
                          </td>

                          <td>
                            @if($pemohonUploaded)
                              <span class="pd-pill done">Sudah upload</span>
                            @else
                              <span class="pd-pill todo">Belum upload</span>
                            @endif
                          </td>

                          <td>
                            @if(!$req)
                              <span class="pd-muted">Menunggu admin klik “Kirim Permintaan Revisi”.</span>
                            @else
                              <form method="POST"
                                    action="{{ route('pemohon.uploadRevisi', ['id' => $req->id]) }}"
                                    enctype="multipart/form-data"
                                    style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                @csrf
                                <input type="file" name="file" required style="max-width:200px;">
                                <button type="submit" class="pd-mini-btn">Upload</button>
                              </form>
                            @endif
                          </td>
                        </tr>
                      @empty
                        <tr><td colspan="5">Belum ada dokumen revisi.</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              @endif

              {{-- =========================
                   APPROVE (INI YANG KAMU MAU)
                   - tampil SELALU di step APPROVE
                   - disabled kalau belum approve
                   ========================= --}}
              @if($s['key'] === 'approve')
                @php
                  $isApprove = ($status === 'approve');
                  $tt = $sv->tanda_terima_pdf ?? null;
                @endphp

                <div class="pd-approve-actions" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                  {{-- TANDA TERIMA --}}
                  @if($isApprove && $tt)
                    <a class="pd-mini-btn primary"
                      target="_blank"
                      href="{{ asset('storage/'.$tt) }}">
                      Download Tanda Terima
                    </a>
                  @else
                    <button class="pd-mini-btn" disabled>
                      Download Tanda Terima
                    </button>
                  @endif

                  {{-- PENDAFTARAN --}}
                  @if($isApprove)
                    <button class="pd-mini-btn outline"
                            onclick="alert('Menu pendaftaran akan segera tersedia')">
                      Pendaftaran
                    </button>
                  @else
                    <button class="pd-mini-btn outline" disabled>
                      Pendaftaran
                    </button>
                  @endif
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      <div class="pd-legend">
        <div class="pd-leg"><span class="pd-leg-dot done"></span> Selesai</div>
        <div class="pd-leg"><span class="pd-leg-dot run"></span> Sedang Berlangsung</div>
        <div class="pd-leg"><span class="pd-leg-dot todo"></span> Belum Diproses</div>
      </div>
    </section>

  </div>
</main>

{{-- ✅ MODAL AKUN (klik user -> muncul tengah) --}}
<div class="pa-backdrop" id="paBackdrop" hidden></div>

<div class="pa-modal" id="paModal" hidden role="dialog" aria-modal="true" aria-labelledby="paTitle">
  <div class="pa-card">
    <div class="pa-head">
      <div>
        <div class="pa-title" id="paTitle">Akun Pemohon</div>
        <div class="pa-subtitle">Ringkasan data pemohon.</div>
      </div>
      <button type="button" class="pa-close" id="closeAccount" aria-label="Tutup">✕</button>
    </div>

    <div class="pa-body">
      <div class="pa-chip">{{ $pemohon['kategori'] }}</div>

      <div class="pa-kv">
        <div>
          <div class="pa-label">Username / Kode Unik</div>
          <div class="pa-value">{{ $pemohon['username'] }}</div>
        </div>

        <div>
          <div class="pa-label">Fakultas</div>
          <div class="pa-value">{{ $pemohon['fakultas'] }}</div>
        </div>

        <div>
          <div class="pa-label">Kategori</div>
          <div class="pa-value">{{ $pemohon['kategori'] }}</div>
        </div>

        <div>
          <div class="pa-label">Jenis</div>
          <div class="pa-value">{{ $pemohon['jenis'] }}</div>
        </div>

        <div class="pa-wide">
          <div class="pa-label">Judul</div>
          <div class="pa-value">{{ $pemohon['judul'] }}</div>
        </div>
      </div>
    </div>

    <div class="pa-actions">
      <button type="button" class="pa-btn ghost" id="okAccount">Tutup</button>
    </div>
  </div>
</div>
@endsection
