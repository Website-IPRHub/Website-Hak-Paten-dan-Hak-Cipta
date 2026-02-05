{{-- resources/views/admin/cipta/detail.blade.php --}}

@php
  $tab = 'cipta';
  $name = $name ?? 'Admin';
  $notifCount = $notifCount ?? 0;

  // arah lonceng (silakan ganti kalau kamu punya route khusus)
  $notifUrl = route('admin.dashboard', ['tab' => 'status', 'sub' => 'revisi']);

  $docLabels = [
    'surat_permohonan' => 'Surat Permohonan',
    'surat_pernyataan' => 'Surat Pernyataan',
    'surat_pengalihan' => 'Surat Pengalihan',
    'tanda_terima'     => 'Tanda Terima',
    'scan_ktp'         => 'Scan KTP',
    'hasil_ciptaan'    => 'Hasil Ciptaan',
  ];
  $docKeys = array_keys($docLabels);

  // pencipta/inventor (kalau sudah dinormalisasi di model/controller)
  $inventors = $row->inventors_arr ?? [];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Detail Hak Cipta</title>

  {{-- biar tampilannya sama --}}
  @vite(['resources/css/admin.css', 'resources/js/app.js'])

  {{-- asset detail cipta --}}
  @vite([
    'resources/css/ciptadetail.css',
    'resources/js/admin/lihatdetail.js'
  ])
</head>

<body class="admin-page cipta-detail-page">

<header class="admin-header">
  <div class="brand">
    <img src="{{ asset('images/logo.jpg') }}?v={{ filemtime(public_path('images/logo.jpg')) }}" alt="Logo">
  </div>

  <div class="header-actions">
    <a href="{{ $notifUrl }}" class="notif-icon-btn" title="Notif Revisi">
      <img src="{{ asset('images/notif.png') }}" alt="Notif" class="notif-ic">
      @if($notifCount > 0)
        <span class="notif-badge">{{ $notifCount }}</span>
      @endif
    </a>

    <div class="user-dd" id="userDD">
      <button type="button" class="user-icon" id="userBtn" aria-haspopup="true" aria-expanded="false">
        <img src="{{ asset('images/user.png') }}" alt="User">
      </button>

      <div class="user-menu" id="userMenu" hidden>
        <div class="user-menu-head">
          <div class="user-menu-name">{{ $name }}</div>
          <div class="user-menu-sub">Admin</div>
        </div>
        <div class="user-menu-actions">
          <button type="button" class="user-menu-item" id="openChangePass">Ubah Password</button>
        </div>
      </div>
    </div>

    <button type="button" class="logout-btn" id="openLogoutModal" aria-label="Logout">
      <img src="{{ asset('images/logout.png') }}" alt="Logout">
    </button>
  </div>
</header>

<section class="dash-hero">
  <div class="dash-hero-overlay"></div>
  <h1 class="dash-hero-title">Halo, {{ $name }}!</h1>
</section>

<div class="dash-layout">
  <aside class="dash-sidebar">
    <a class="side-link {{ $tab==='stats' ? 'active' : '' }}"
       href="{{ route('admin.dashboard', ['tab'=>'stats']) }}">
      <img class="side-ic-img" src="{{ asset('images/statistik.png') }}" alt="">
      Statistik Analisis
    </a>

    <a class="side-link {{ $tab==='cipta' ? 'active' : '' }}"
       href="{{ route('admin.dashboard', ['tab'=>'cipta']) }}">
      <img class="side-ic-img" src="{{ asset('images/dokumen.png') }}" alt="">
      Data Hak Cipta
    </a>

    <a class="side-link {{ $tab==='paten' ? 'active' : '' }}"
       href="{{ route('admin.dashboard', ['tab'=>'paten']) }}">
      <img class="side-ic-img" src="{{ asset('images/dokumen.png') }}" alt="">
      Data Paten
    </a>
  </aside>

  <main class="dash-content">

    <div class="cipta-wrap" data-cipta-detail>
      <div class="page-head page-head-left">
        <a href="{{ route('admin.dashboard',['tab'=>'cipta']) }}" class="btn-ghost back-btn">
          ← Kembali
        </a>
      </div>

      <div class="card card-paten">
        <div class="paten-head">
          <h2 class="paten-title">Detail Hak Cipta</h2>
          <div class="paten-sub">
            {{ $row->no_pendaftaran ?? '-' }} • {{ $row->judul_cipta ?? '-' }}
          </div>
        </div>

        <div class="paten-grid">
          <div class="paten-col">
            <div class="paten-info">
              <div class="paten-row">
                <div class="p-label">No Pendaftaran</div><div class="p-colon">:</div>
                <div class="p-value">{{ $row->no_pendaftaran ?? '-' }}</div>
              </div>

              <div class="paten-row">
                <div class="p-label">Jenis</div><div class="p-colon">:</div>
                <div class="p-value">
                  @php
                    $jenis = $row->jenis_cipta ?? '-';
                    if (strtolower($jenis) === 'lainnya') $jenis = $row->jenis_lainnya ?? 'Lainnya';
                  @endphp
                  {{ $jenis }}
                </div>
              </div>

              <div class="paten-row">
                <div class="p-label">Nilai Perolehan</div><div class="p-colon">:</div>
                <div class="p-value">{{ $row->nilai_perolehan ?? '-' }}</div>
              </div>

              <div class="paten-row">
                <div class="p-label">Skema Penelitian</div><div class="p-colon">:</div>
                <div class="p-value">{{ $row->skema_penelitian ?? '-' }}</div>
              </div>
            </div>
          </div>

          <div class="paten-col">
            <div class="paten-info">
              <div class="paten-row">
                <div class="p-label">Judul</div><div class="p-colon">:</div>
                <div class="p-value">{{ $row->judul_cipta ?? '-' }}</div>
              </div>

              <div class="paten-row">
                <div class="p-label">Sumber Dana</div><div class="p-colon">:</div>
                <div class="p-value">{{ $row->sumber_dana ?? '-' }}</div>
              </div>

              <div class="paten-row">
                <div class="p-label">Status Pengajuan</div><div class="p-colon">:</div>
                <div class="p-value">
                  <span class="status-badge s-{{ strtolower($row->status ?? 'pending') }}">
                    {{ strtoupper($row->status ?? '-') }}
                  </span>
                </div>
              </div>

              <div class="paten-row">
                <div class="p-label">Link Ciptaan</div><div class="p-colon">:</div>
                <div class="p-value">
                  @if(!empty($row->link_ciptaan))
                    <a class="doc-link" href="{{ $row->link_ciptaan }}" target="_blank">{{ $row->link_ciptaan }}</a>
                  @else
                    <span class="muted">-</span>
                  @endif
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      {{-- ACCORDION --}}
      <div class="acc-wrap">

        {{-- DOKUMEN --}}
        <div class="acc-card" data-acc-card="docs">
          <button type="button" class="acc-head" data-acc-toggle="docs" aria-expanded="false">
            <div class="acc-left">
              <div class="acc-title">Detail Dokumen</div>
              <div class="acc-sub">Klik untuk melihat & verifikasi dokumen</div>
            </div>
            <span class="acc-caret" aria-hidden="true">˅</span>
          </button>

          <div class="acc-body" data-acc-body="docs" hidden>
            <div class="docs-list">
              @foreach($docKeys as $k)
                @php
                  $filePath  = $row->$k ?? null;
                  $doc       = $row->docs[$k] ?? null;
                  $statusDoc = optional($doc)->status ?? 'pending';
                  $note      = optional($doc)->note;
                @endphp

                <div class="doc-item" data-doc-wrap>
                  <div class="doc-top">
                    <div>
                      <div class="doc-name">{{ $docLabels[$k] }}</div>
                      @if($filePath)
                        <a class="doc-link" href="{{ asset('storage/'.$filePath) }}" target="_blank">
                          {{ basename($filePath) }}
                        </a>
                      @else
                        <div class="muted">-</div>
                      @endif
                    </div>

                    <span class="badge badge-{{ $statusDoc }}" data-doc-badge data-doc-key="{{ $k }}">
                      {{ strtoupper($statusDoc) }}
                    </span>
                  </div>

                  <div class="doc-actions">
                    <form class="js-doc-form" method="POST"
                          action="{{ route('admin.verifikasi_dokumen.set',['type'=>'cipta','id'=>$row->id]) }}">
                      @csrf
                      <input type="hidden" name="doc_key" value="{{ $k }}">
                      <input type="hidden" name="action" value="ok">
                      <button class="btn-mini" type="submit">OK</button>
                    </form>

                    <div class="rev-dd" data-rev>
                      <button type="button" class="btn-mini rev-btn" data-rev-btn>Revisi</button>

                      <div class="rev-pop" data-rev-pop hidden>
                        <form class="js-doc-form" method="POST" enctype="multipart/form-data"
                              action="{{ route('admin.verifikasi_dokumen.set',['type'=>'cipta','id'=>$row->id]) }}">
                          @csrf
                          <input type="hidden" name="doc_key" value="{{ $k }}">
                          <input type="hidden" name="action" value="revisi">

                          <textarea name="note" rows="3" class="input" placeholder="Catatan revisi (wajib)">{{ $note }}</textarea>

                          <div style="margin-top:6px;">
                            <label style="font-size:12px;">Upload file revisi admin (opsional)</label>
                            <input type="file" name="admin_attachment">
                          </div>

                          <button type="submit" class="btn-mini" style="margin-top:6px;">Simpan Revisi</button>
                        </form>

                        @if(!empty(optional($doc)->admin_attachment_path))
                          <div style="margin-top:6px; font-size:12px;">
                            Lampiran admin:
                            <a href="{{ asset('storage/'.optional($doc)->admin_attachment_path) }}" target="_blank">
                              {{ basename(optional($doc)->admin_attachment_path) }}
                            </a>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="docs-footer-actions">
              <form class="js-send-revisi-form js-send-revisi-all"
                    method="POST"
                    action="{{ route('admin.verifikasi_dokumen.sendRevisi',['type'=>'cipta','id'=>$row->id]) }}">
                @csrf
                <button type="submit" class="btn-mini btn-revisi">Simpan & Kirim ke Pemohon</button>
                <div class="inline-msg" data-inline-msg style="margin-top:6px;font-size:12px;"></div>
              </form>
            </div>
          </div>
        </div>

        {{-- PENCIPTA --}}
        <div class="acc-card" data-acc-card="inv">
          <button type="button" class="acc-head" data-acc-toggle="inv" aria-expanded="false">
            <div class="acc-left">
              <div class="acc-title">Detail Pencipta</div>
              <div class="acc-sub">Klik untuk melihat data pencipta</div>
            </div>
            <span class="acc-caret" aria-hidden="true">˅</span>
          </button>

          <div class="acc-body" data-acc-body="inv" hidden>
            @if(empty($inventors))
              <div class="muted">Tidak ada data pencipta.</div>
            @else
              <div class="inv-grid">
                @foreach($inventors as $idx => $inv)
                  <div class="inv-card">
                    <div class="inv-name">
                      {{ $idx+1 }}. {{ $inv['nama'] ?? '-' }}
                      <span class="muted">({{ $inv['status'] ?? '-' }})</span>
                    </div>

                    <div class="inv-info">
                      <div class="inv-row"><div class="inv-k">NIP/NIM</div><div class="inv-colon">:</div><div class="inv-v">{{ $inv['nip_nim'] ?? '-' }}</div></div>
                      <div class="inv-row"><div class="inv-k">Fakultas</div><div class="inv-colon">:</div><div class="inv-v">{{ $inv['fakultas'] ?? '-' }}</div></div>
                      <div class="inv-row"><div class="inv-k">Email</div><div class="inv-colon">:</div><div class="inv-v">{{ $inv['email'] ?? '-' }}</div></div>
                      <div class="inv-row"><div class="inv-k">No HP</div><div class="inv-colon">:</div><div class="inv-v">{{ $inv['no_hp'] ?? '-' }}</div></div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>

      </div>
    </div>

    {{-- MODAL LOGOUT --}}
    <div class="modal-backdrop" id="logoutBackdrop" hidden></div>
    <div class="modal" id="logoutModal" hidden role="dialog" aria-modal="true" aria-labelledby="logoutTitle">
      <div class="modal-card">
        <h3 id="logoutTitle" class="modal-title">Konfirmasi Logout</h3>
        <p class="modal-text">Kamu yakin mau logout?</p>

        <div class="modal-actions">
          <button type="button" class="btn-ghost" id="cancelLogout">Batal</button>

          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn-danger">Ya, Logout</button>
          </form>
        </div>
      </div>
    </div>

    {{-- MODAL UBAH PASSWORD --}}
    <div class="modal-backdrop" id="passBackdrop" hidden></div>
    <div class="modal" id="passModal" hidden role="dialog" aria-modal="true" aria-labelledby="passTitle">
      <div class="modal-card">
        <h3 id="passTitle" class="modal-title">Ubah Password</h3>

        <form method="POST" action="{{ route('admin.password.update') }}">
          @csrf

          <label style="display:block; font-size:12px; margin-top:10px;">Password Lama</label>
          <input class="input" type="password" name="old_password" required>

          <label style="display:block; font-size:12px; margin-top:10px;">Password Baru</label>
          <input class="input" type="password" name="new_password" minlength="6" required>

          <label style="display:block; font-size:12px; margin-top:10px;">Konfirmasi Password Baru</label>
          <input class="input" type="password" name="new_password_confirmation" minlength="6" required>

          <div class="modal-actions" style="margin-top:14px;">
            <button type="button" class="btn-ghost" id="cancelPass">Batal</button>
            <button type="submit" class="btn-danger">Simpan</button>
          </div>
        </form>
      </div>
    </div>

  </main>
</div>

</body>
</html>
