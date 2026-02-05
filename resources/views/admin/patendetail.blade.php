{{-- resources/views/admin/paten/detail.blade.php --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
  $tab = 'paten';
  $name = $name ?? 'Admin';
  $notifCount = $notifCount ?? 0;
  $notifUrl = route('admin.dashboard', ['tab' => 'status', 'sub' => 'revisi']);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Detail Paten</title>

  @vite(['resources/css/admin.css', 'resources/js/app.js'])

  @vite([
    'resources/css/patendetail.css',
    'resources/js/admin/lihatdetail.js'
  ])

  <style>
    .doc-dot{
      display:inline-block;width:8px;height:8px;border-radius:999px;
      margin-right:8px;vertical-align:middle;
      background:#e74c3c;
      box-shadow:0 0 0 3px rgba(231,76,60,.12);
    }
    .doc-dot.green{
      background:#22c55e;
      box-shadow:0 0 0 3px rgba(34,197,94,.14);
    }

    .rev-admin-box{
      background:#f7f9fc;border:1px solid #e6edf7;border-radius:12px;
      padding:12px 14px;margin-top:10px;
    }
    .rev-admin-box .ttl{font-weight:700;margin-bottom:6px}
    .rev-admin-box .meta{font-size:12px;color:#6b7280;margin-top:6px}

    .incoming-table{margin-top:10px}
    .incoming-row > div{font-size:13px}

    .truncate-1{
      max-width: 420px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    @media (max-width: 900px){
      .truncate-1{max-width: 220px;}
    }

    /* tombol approve */
    .btn-approve-right{
      background:#2563eb;
      color:#fff;
      border:none;
      padding:12px 18px;
      border-radius:12px;
      font-weight:800;
      font-size:14px;
      cursor:pointer;
      min-width:180px;
      margin-left:10px;
    }
    .btn-approve-right:disabled,
    .btn-send-right:disabled{
      opacity:.55;
      cursor:not-allowed;
    }

    .docs-footer-actions{
      display:flex;
      justify-content:flex-end;
      gap:10px;
      margin-top:14px;
      align-items:center;
      flex-wrap:wrap;
    }
  </style>
</head>

<body class="admin-page paten-detail-page">

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

    @php
      $docLabels = [
        'draft_paten'       => 'Draft Paten',
        'form_permohonan'   => 'Form Permohonan',
        'surat_kepemilikan' => 'Surat Kepemilikan',
        'surat_pengalihan'  => 'Surat Pengalihan',
        'scan_ktp'          => 'Scan KTP',
        'tanda_terima'      => 'Tanda Terima',
        'gambar_prototipe'  => 'Gambar Prototipe',
      ];
      $docKeys = array_keys($docLabels);

      $inventors = $row->inventors_arr ?? [];
      $incomingByDoc = $incomingByDoc ?? collect();

      /**
       * RULE tombol:
       * - canSend: admin boleh klik "Simpan & Kirim" kalau minimal ada 1 dokumen statusnya ok/revisi (kamu bisa ubah jadi "semua dokumen harus diproses" kalau mau)
       * - canApprove: boleh approve kalau ada minimal 1 upload revisi dari pemohon (di dokumen mana pun)
       */
      $allDocStatuses = collect($docKeys)->map(fn($k) => optional($row->docs[$k] ?? null)->status ?? 'pending');
      $canSend = $allDocStatuses->contains(fn($st) => in_array($st, ['ok','revisi']));

      $canApprove = collect($docKeys)->contains(function($k) use ($incomingByDoc){
        $incoming = $incomingByDoc->get($k) ?? collect();
        return $incoming->contains(fn($x) => !empty($x->pemohon_file_path));
      });
    @endphp

    <div class="paten-wrap" data-cipta-detail>

      <div class="page-head page-head-left">
        <a href="{{ route('admin.dashboard',['tab'=>'paten']) }}" class="btn-ghost back-btn">
          ← Kembali
        </a>
      </div>

      <div class="card card-paten">
        <div class="paten-head">
          <h2 class="paten-title">Detail Paten</h2>
          <div class="paten-sub">
            {{ $row->no_pendaftaran ?? '-' }} • {{ $row->judul_paten ?? '-' }}
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
                <div class="p-value">{{ $row->jenis_paten ?? '-' }}</div>
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
                <div class="p-value">{{ $row->judul_paten ?? '-' }}</div>
              </div>

              <div class="paten-row">
                <div class="p-label">Prototipe</div><div class="p-colon">:</div>
                <div class="p-value">{{ $row->prototipe ?? '-' }}</div>
              </div>

              <div class="paten-row">
                <div class="p-label">Sumber Dana</div><div class="p-colon">:</div>
                <div class="p-value">{{ $row->sumber_dana ?? '-' }}</div>
              </div>

              <div class="paten-row">
                <div class="p-label">Status Pengajuan</div><div class="p-colon">:</div>
                <div class="p-value">
                 <span
                    id="statusPengajuanBadge"
                    class="status-badge s-{{ strtolower($row->status ?? 'pending') }}"
                  >
                    {{ strtoupper($row->status ?? '-') }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="acc-wrap">

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

                  $incomingRaw = $incomingByDoc->get($k) ?? collect();

                  $incomingSorted = $incomingRaw->sortByDesc(function($x){
                    return $x->pemohon_uploaded_at ?? $x->updated_at ?? $x->created_at;
                  })->values();

                  $hasPemohonUpload = $incomingSorted->contains(fn($x) => !empty($x->pemohon_file_path));

                  // admin dianggap "ngirim revisi" kalau ada note ATAU statusDoc revisi
                  $hasAdminRevisi = (!empty($note)) || ($statusDoc === 'revisi');

                  // dot hanya muncul kalau ada revisi admin ATAU ada upload pemohon
                  $showDot  = $hasAdminRevisi || $hasPemohonUpload;
                  $dotClass = $hasPemohonUpload ? 'green' : 'red';
                @endphp

                <div class="doc-item" data-doc-wrap>
                  <div class="doc-top">
                    <div>
                      <div class="doc-name">
                        @if($showDot)
                          <span class="doc-dot {{ $dotClass }}"
                            title="{{ $hasPemohonUpload ? 'Ada upload revisi dari pemohon' : 'Menunggu upload revisi dari pemohon' }}"></span>
                        @endif
                        {{ $docLabels[$k] }}
                      </div>

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
                          action="{{ route('admin.verifikasi_dokumen.set',['type'=>'paten','id'=>$row->id]) }}">
                      @csrf
                      <input type="hidden" name="doc_key" value="{{ $k }}">
                      <input type="hidden" name="action" value="ok">
                      <button class="btn-mini" type="submit">OK</button>
                    </form>

                    <div class="rev-dd" data-rev>
                      <button type="button" class="btn-mini rev-btn" data-rev-btn>Revisi</button>

                      <div class="rev-pop" data-rev-pop hidden>
                        <form class="js-doc-form" method="POST" enctype="multipart/form-data"
                              action="{{ route('admin.verifikasi_dokumen.set',['type'=>'paten','id'=>$row->id]) }}">
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

                  {{-- REVISI box hanya muncul kalau admin revisi atau pemohon sudah upload --}}
                  @if($hasAdminRevisi || $hasPemohonUpload)
                    <div class="incoming-wrap">
                      <div class="incoming-title">Revisi</div>

                      @if(!empty($note))
                        <div class="rev-admin-box">
                          <div class="ttl">Catatan revisi dari admin</div>
                          <div class="note-admin">{{ $note }}</div>
                          <div class="meta">
                            Update (Admin):
                            {{ optional($doc)->updated_at ? \Carbon\Carbon::parse(optional($doc)->updated_at)->format('d M Y') : '-' }}
                          </div>
                        </div>
                      @endif

                      @if(!$hasPemohonUpload)
                        <div class="muted" style="font-size:12px; margin-top:10px;">
                          Belum ada upload revisi dari pemohon.
                        </div>
                      @else
                        @php
                          $pemohonList = $incomingSorted->filter(fn($x) => !empty($x->pemohon_file_path))->values();
                        @endphp

                        <details class="incoming-details" style="margin-top:10px;">
                          <summary class="incoming-summary">
                            Detail Revisi ({{ $pemohonList->count() }})
                          </summary>

                          <div class="incoming-table">
                            <div class="incoming-row incoming-head">
                              <div>Catatan</div>
                              <div>File Pemohon</div>
                              <div>Update</div>
                            </div>

                            @foreach($pemohonList as $rv)
                              @php
                                $pemohonTime = $rv->pemohon_uploaded_at ?? $rv->updated_at ?? $rv->created_at;
                                $noteText = $rv->note ?? '-';
                              @endphp

                              <div class="incoming-row">
                                <div class="incoming-cell">
                                  <div class="truncate-1" title="{{ $noteText }}">{{ $noteText }}</div>
                                </div>

                                <div class="incoming-cell">
                                  <a target="_blank" href="{{ asset('storage/'.$rv->pemohon_file_path) }}">
                                    {{ $rv->pemohon_file_name ?? basename($rv->pemohon_file_path) }}
                                  </a>
                                </div>

                                <div class="incoming-cell muted">
                                  {{ $pemohonTime ? \Carbon\Carbon::parse($pemohonTime)->format('d M Y H:i') : '-' }}
                                </div>
                              </div>
                            @endforeach
                          </div>
                        </details>
                      @endif
                    </div>
                  @endif

                </div>
              @endforeach
            </div>

            {{-- FOOTER BUTTONS --}}
            <div class="docs-footer-actions">

              {{-- ✅ Kirim Revisi: pakai route yang ADA --}}
              <form id="sendRevisiForm"
                    class="js-send-revisi-form"
                    method="POST"
                    action="{{ route('admin.verifikasi_dokumen.sendRevisi', ['type'=>'paten','id'=>$row->id]) }}">
                @csrf
                <button id="btnSendRevisi"
                        type="submit"
                        class="btn-send-right"
                        {{ $canSend ? '' : 'disabled' }}>
                  Simpan & Kirim ke Pemohon
                </button>
              </form>

              {{-- ✅ Approve: disable kalau pemohon belum upload revisi sama sekali --}}
              {{-- ✅ Approve: AJAX, tidak reload halaman --}}
             <button
                type="button"
                id="btnApprove"
                class="btn-approve-right"
                data-url="{{ route('admin.verifikasi_dokumen.approve', ['type'=>'paten','id'=>$row->id]) }}"
                {{ $canApprove ? '' : 'disabled' }}
              >
                Approve
              </button>
            </div>
          </div>
        </div>

        {{-- INVENTOR --}}
        <div class="acc-card" data-acc-card="inv">
          <button type="button" class="acc-head" data-acc-toggle="inv" aria-expanded="false">
            <div class="acc-left">
              <div class="acc-title">Detail Inventor</div>
              <div class="acc-sub">Klik untuk melihat data inventor</div>
            </div>
            <span class="acc-caret" aria-hidden="true">˅</span>
          </button>

          <div class="acc-body" data-acc-body="inv" hidden>
            @if(empty($inventors))
              <div class="muted">Tidak ada data inventor.</div>
            @else
              <div class="inv-grid">
                @foreach($inventors as $idx => $inv)
                  <div class="inv-card">
                    <div class="inv-name">
                      {{ $idx+1 }}. {{ $inv['nama'] ?? '-' }}
                      <span class="muted">({{ $inv['status'] ?? '-' }})</span>
                    </div>

                    <div class="inv-info">
                      <div class="inv-row">
                        <div class="inv-k">NIP/NIM</div><div class="inv-colon">:</div><div class="inv-v">{{ $inv['nip_nim'] ?? '-' }}</div>
                      </div>
                      <div class="inv-row">
                        <div class="inv-k">Fakultas</div><div class="inv-colon">:</div><div class="inv-v">{{ $inv['fakultas'] ?? '-' }}</div>
                      </div>
                      <div class="inv-row">
                        <div class="inv-k">Email</div><div class="inv-colon">:</div><div class="inv-v">{{ $inv['email'] ?? '-' }}</div>
                      </div>
                      <div class="inv-row">
                        <div class="inv-k">No HP</div><div class="inv-colon">:</div><div class="inv-v">{{ $inv['no_hp'] ?? '-' }}</div>
                      </div>
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

@if(session('wa_link'))
  <div id="waPayload"
       data-wa="{{ session('wa_link') }}"
       data-label="{{ session('wa_label') ?? 'Kirim WhatsApp' }}"
       hidden></div>
@endif


</body>
</html>
