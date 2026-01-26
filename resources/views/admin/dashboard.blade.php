<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin</title>


    {{-- CSS + JS lewat Vite --}}
    @vite(['resources/css/admin.css', 'resources/js/app.js', 'resources/js/admin/dashboard.js'])
</head>
 <body class="admin-page">
    <header class="admin-header">
    <div class="brand">
        <img src="{{ asset('images/LogoDirinovki2026.png') }}" alt="Logo">
    </div>

    <div class="header-actions">

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
                <button type="button" class="user-menu-item" id="openChangePass">
                    Ubah Password
                </button>
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

@php
    $tab = $tab ?? 'stats';

    // LABEL untuk verifikasi dokumen (dipakai PATEN & CIPTA)
    $docLabels = [
        // PATEN
        'draft_paten' => 'Draft Paten',
        'form_permohonan' => 'Form Permohonan',
        'surat_kepemilikan' => 'Surat Kepemilikan',
        'surat_pengalihan' => 'Surat Pengalihan',
        'scan_ktp' => 'Scan KTP',
        'tanda_terima' => 'Tanda Terima',
        'gambar_prototipe' => 'Gambar Prototipe',

        // CIPTA
        'surat_permohonan' => 'Surat Permohonan',
        'surat_pernyataan' => 'Surat Pernyataan',
        'surat_pengalihan' => 'Surat Pengalihan',
        'tanda_terima' => 'Tanda Terima',
        'scan_ktp' => 'Scan KTP',
        'hasil_ciptaan' => 'Hasil Ciptaan',
    ];
@endphp

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

        <a class="side-link {{ $tab==='status' ? 'active' : '' }}"
           href="{{ route('admin.dashboard', ['tab'=>'status']) }}">
            <img class="side-ic-img" src="{{ asset('images/status.png') }}" alt="">
            Status Verifikasi
        </a>

    </aside>

    <main class="dash-content">

        {{-- ================= TAB: STATS (statistik kamu tetap) ================= --}}
        @if($tab === 'stats')
            <section class="stats-wrap">

                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-head">
                            <div class="stat-title">Jumlah Keseluruhan</div>
                            <div class="stat-badge">TOTAL</div>
                        </div>
                        <div class="stat-value">{{ $totalAll }}</div>
                        <div class="stat-sub">Total pendaftaran/pengajuan</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-head">
                            <div class="stat-title">Jumlah Paten</div>
                            <div class="stat-badge">PATEN</div>
                        </div>
                        <div class="stat-value">{{ $totalPaten }}</div>
                        <div class="stat-sub">Semua jenis hak paten</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-head">
                            <div class="stat-title">Jumlah Hak Cipta</div>
                            <div class="stat-badge">CIPTA</div>
                        </div>
                        <div class="stat-value">{{ $totalCipta }}</div>
                        <div class="stat-sub">Semua jenis hak cipta</div>
                    </div>
                </div>

                <div class="stats-charts">
                    <div class="chart-card">
                        <div class="chart-title">Jumlah Jenis Hak Paten</div>
                        <canvas id="chartPaten"></canvas>
                    </div>

                    <div class="chart-card">
                        <div class="chart-title">Jumlah Jenis Hak Cipta</div>
                        <canvas id="chartCipta"></canvas>
                    </div>
                </div>

            </section>
        @endif

        {{-- ================= TAB: DATA HAK CIPTA ================= --}}
        @if($tab === 'cipta')
        <div class="page-head">
            <h2 class="page-title">Data Hak Cipta</h2>

            <div class="page-actions">
            <input id="searchCipta" class="search-input" type="text" placeholder="Cari..." />
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">
            <div>{{ session('success') }}</div>

            @if(session('wa_link'))
                <div style="margin-top:10px;">
                <a class="btn-mini" target="_blank" href="{{ session('wa_link') }}">
                    {{ session('wa_label') ?? 'Kirim WhatsApp' }}
                </a>
                </div>
            @endif
            </div>
        @endif

        <div class="table-card table-scroll">
            <table class="data-table table-wide" id="ciptaTable">
            <thead>
                <tr>
                    <th rowspan="2" style="width:70px;">No</th>
                    <th rowspan="2" style="min-width:120px;">No Pendaftaran</th>
                    <th rowspan="2" style="min-width:250px;">Judul Cipta</th>
                    <th rowspan="2" style="width:140px;">Jenis</th>
                    <th rowspan="2" style="width:140px;">Status</th>

                    <th colspan="5" class="th-doc-merge">DOKUMEN</th>

                    <th rowspan="2" style="min-width:220px;">Hasil Ciptaan</th>
                    <th rowspan="2" style="min-width:260px;">Link Ciptaan (khusus Rekaman Video)</th>
                    <th rowspan="2" style="width:170px;">Aksi</th>
                </tr>

                <tr>
                    <th style="min-width:180px;">Surat Permohonan</th>
                    <th style="min-width:180px;">Surat Pernyataan</th>
                    <th style="min-width:190px;">Surat Pengalihan</th>
                    <th style="min-width:180px;">Tanda Terima</th>
                    <th style="min-width:160px;">Scan KTP</th>
                </tr>
            </thead>

            <tbody>
                @forelse($dataCipta as $i => $row)
                @php
                    $ciptaKey = strtolower(implode(' ', array_filter([
                    $row->no_pendaftaran ?? '',
                    $row->judul_cipta ?? '',
                    $row->jenis_cipta ?? '',
                    $row->status ?? '',
                    $row->fakultas ?? '',
                    $row->email ?? '',
                    basename($row->surat_permohonan ?? ''),
                    basename($row->surat_pernyataan ?? ''),
                    basename($row->surat_pengalihan ?? ''),
                    basename($row->tanda_terima ?? ''),
                    basename($row->scan_ktp ?? ''),
                    basename($row->hasil_ciptaan ?? ''),
                    $row->link_ciptaan ?? '',
                    ])));

                    $hasDocRevisi = collect($row->docs ?? [])
                    ->contains(fn($d) => (($d->status ?? '') === 'revisi'));
                @endphp

                <tr data-key="{{ $ciptaKey }}">
                    <td>{{ $i+1 }}</td>

                    <td>{{ $row->no_pendaftaran ?? '-' }}</td>

                    <td>
                    <div class="title-wrap">
                        <div class="title-main">{{ $row->judul_cipta ?? '-' }}</div>

                        <div class="title-meta">
                        <div class="meta-fakultas">{{ $row->fakultas ?? '-' }}</div>

                        <div class="meta-emails">
                            @foreach(preg_split('/[\s,;]+/', $row->email ?? '') as $mail)
                            @php $mail = trim($mail); @endphp
                            @if($mail)
                                <a href="mailto:{{ $mail }}" class="email-chip">{{ $mail }}</a>
                            @endif
                            @endforeach
                        </div>
                        </div>
                    </div>
                    </td>

                    <td>{{ $row->jenis_cipta ?? '-' }}</td>

                    <td>
                    <span class="status-pill s-{{ $row->status }}">{{ $row->status }}</span>
                    </td>

                    {{-- helper render cell dokumen + verifikasi --}}
                    @php
                    $renderDocCell = function($k) use ($row) {
                        $filePath = $row->$k ?? null;
                        $doc = $row->docs[$k] ?? null;
                        $statusDoc = optional($doc)->status ?? 'pending';
                    @endphp
                    <div>
                        @if($filePath)
                        <a class="doc-link" href="{{ asset('storage/'.$filePath) }}" target="_blank">
                            {{ basename($filePath) }}
                        </a>
                        @else
                        <span class="muted">-</span>
                        @endif

                        <div class="verif-mini">
                        <div class="verif-mini-head">
                            <span class="badge badge-{{ $statusDoc }}"
                                data-doc-badge
                                data-doc-key="{{ $k }}">
                            {{ strtoupper($statusDoc) }}
                            </span>
                        </div>

                        <div class="verif-mini-actions">
                            {{-- ✅ OK (AJAX) --}}
                            <form class="js-doc-form"
                                method="POST"
                                action="{{ route('admin.verifikasi_dokumen.set', ['type'=>'cipta','id'=>$row->id]) }}">
                            @csrf
                            <input type="hidden" name="doc_key" value="{{ $k }}">
                            <input type="hidden" name="action" value="ok">
                            <button class="btn-mini" type="submit">OK</button>
                            </form>

                            {{-- ✅ REVISI (AJAX) --}}
                            {{-- ✅ REVISI (POPUP, gak nambah tinggi baris) --}}
                            <div class="rev-dd" data-rev>
                                <button type="button" class="btn-mini rev-btn" data-rev-btn>Revisi</button>

                                <div class="rev-pop" data-rev-pop hidden>
                                    <form class="js-doc-form"
                                        method="POST"
                                        enctype="multipart/form-data"
                                        action="{{ route('admin.verifikasi_dokumen.set', ['type'=>'cipta','id'=>$row->id]) }}">
                                    @csrf
                                    <input type="hidden" name="doc_key" value="{{ $k }}">
                                    <input type="hidden" name="action" value="revisi">

                                    <textarea name="note" rows="3" class="input"
                                        placeholder="Catatan revisi (wajib)">{{ optional($doc)->note }}</textarea>

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
                    </div>

                    @php }; @endphp

                    {{-- Surat Permohonan --}}
                    <td>@php $renderDocCell('surat_permohonan'); @endphp</td>

                    {{-- Surat Pernyataan --}}
                    <td>@php $renderDocCell('surat_pernyataan'); @endphp</td>

                    {{-- Surat Pengalihan --}}
                    <td>@php $renderDocCell('surat_pengalihan'); @endphp</td>

                    {{-- Tanda Terima --}}
                    <td>@php $renderDocCell('tanda_terima'); @endphp</td>

                    {{-- Scan KTP --}}
                    <td>@php $renderDocCell('scan_ktp'); @endphp</td>

                    {{-- Hasil Ciptaan --}}
                    <td>@php $renderDocCell('hasil_ciptaan'); @endphp</td>

                    {{-- Link Ciptaan --}}
                    <td>
                    @if($row->link_ciptaan)
                        <a class="doc-link" href="{{ $row->link_ciptaan }}" target="_blank">
                        {{ $row->link_ciptaan }}
                        </a>
                    @else
                        <span class="muted">-</span>
                    @endif
                    </td>

                    {{-- HAPUS --}}
                    <td class="cell-actions">
                        <div class="action-stack">
                            {{-- Detail --}}
                            <button type="button"
                            class="btn-mini btn-detail"
                            data-detail-type="cipta"
                            data-no="{{ $row->no_pendaftaran }}"
                            data-judul="{{ $row->judul_cipta }}"
                            data-jenis="{{ $row->jenis_cipta }}"
                            data-jenis-lainnya="{{ $row->jenis_lainnya }}"
                            data-nama="{{ $row->nama_pencipta }}"
                            data-nip="{{ $row->nip_nim }}"
                            data-hp="{{ $row->nomor_hp ?? $row->no_hp }}"
                            data-email="{{ $row->email }}"
                            data-fakultas="{{ $row->fakultas }}"
                            data-nilai="{{ $row->nilai_perolehan }}"
                            data-sumber="{{ $row->sumber_dana }}"
                            >
                            Detail
                            </button>

                            {{-- Hapus --}}
                            <button type="button"
                            class="btn-delete"
                            data-delete-action="{{ route('admin.cipta.destroy', $row->id) }}"
                            data-delete-type="cipta"
                            title="Hapus"
                            >
                            <img src="{{ asset('images/delete.png') }}" alt="Hapus" class="ic-delete">
                            </button>

                            {{-- Kirim Permintaan Revisi (muncul kalau ada revisi) --}}
                            <form class="js-send-revisi-form"
                            method="POST"
                            action="{{ route('admin.verifikasi_dokumen.sendRevisi', ['type'=>'cipta','id'=>$row->id]) }}"
                            @if(!$hasDocRevisi) hidden @endif
                            data-send-revisi
                            >
                            @csrf
                            <button type="submit" class="btn-mini">Kirim Permintaan Revisi</button>
                            <div class="inline-msg" data-inline-msg style="margin-top:6px; font-size:12px;"></div>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="center muted">Belum ada data cipta</td>
                </tr>
                @endforelse
            </tbody>
            </table>
        </div>
        <div class="table-footer" data-pager="cipta">
            <div class="table-info" data-info>Showing 0 to 0 of 0 entries</div>

            <div class="table-controls">
                <label class="entries-wrap">
                Show
                <select class="entries-select" data-entries>
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                entries
                </label>

                <div class="pagination" data-pagination></div>
            </div>
        </div>
        @endif

       {{-- ================= TAB: DATA PATEN ================= --}}
        @if($tab === 'paten')
        <div class="page-head">
            <h2 class="page-title">Data Paten</h2>

            <div class="page-actions">
            <input id="searchPaten" class="search-input" type="text" placeholder="Cari..." />
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">
            <div>{{ session('success') }}</div>

            @if(session('wa_link'))
                <div style="margin-top:10px;">
                <a class="btn-mini" target="_blank" href="{{ session('wa_link') }}">
                    {{ session('wa_label') ?? 'Kirim WhatsApp' }}
                </a>
                </div>
            @endif
            </div>
        @endif

        <div class="table-card table-scroll">
            <table class="data-table table-wide" id="patenTable">
            <thead>
                <tr>
                    <th rowspan="2" style="width:70px;">No</th>
                    <th rowspan="2" style="min-width:120px;">No Pendaftaran</th>
                    <th rowspan="2" style="min-width:250px;">Judul Paten</th>
                    <th rowspan="2" style="width:140px;">Jenis</th>
                    <th rowspan="2" style="width:140px;">Status</th>

                    <th colspan="6" class="th-doc-merge">DOKUMEN</th>

                    <th rowspan="2" style="min-width:220px;">Gambar Prototipe</th>
                    <th rowspan="2" style="min-width:220px;">Deskripsi singkat prototipe/produk</th>
                    <th rowspan="2" style="width:170px;">Aksi</th>
                </tr>

                <tr>
                    <th style="min-width:180px;">Draft Paten</th>
                    <th style="min-width:180px;">Form Permohonan</th>
                    <th style="min-width:190px;">Surat Kepemilikan</th>
                    <th style="min-width:180px;">Surat Pengalihan</th>
                    <th style="min-width:160px;">Scan KTP</th>
                    <th style="min-width:180px;">Tanda Terima</th>
                </tr>
            </thead>

            <tbody>
                @forelse($dataPaten as $i => $row)
                @php
                    $patenKey = strtolower(implode(' ', array_filter([
                    $row->no_pendaftaran ?? '',
                    $row->judul_paten ?? '',
                    $row->jenis_paten ?? '',
                    $row->status ?? '',
                    $row->fakultas ?? '',
                    $row->email ?? '',
                    basename($row->draft_paten ?? ''),
                    basename($row->form_permohonan ?? ''),
                    basename($row->surat_kepemilikan ?? ''),
                    basename($row->surat_pengalihan ?? ''),
                    basename($row->scan_ktp ?? ''),
                    basename($row->tanda_terima ?? ''),
                    ])));

                    $hasDocRevisi = collect($row->docs ?? [])
                    ->contains(fn($d) => (($d->status ?? '') === 'revisi'));
                @endphp

                <tr data-key="{{ $patenKey }}">
                    <td>{{ $i+1 }}</td>

                    <td>{{ $row->no_pendaftaran ?? '-' }}</td>

                    <td>
                    <div class="title-wrap">
                        <div class="title-main">{{ $row->judul_paten ?? '-' }}</div>

                        <div class="title-meta">
                        <div class="meta-fakultas">{{ $row->fakultas ?? '-' }}</div>

                        <div class="meta-emails">
                            @foreach(preg_split('/[\s,;]+/', $row->email ?? '') as $mail)
                            @php $mail = trim($mail); @endphp
                            @if($mail)
                                <a href="mailto:{{ $mail }}" class="email-chip">{{ $mail }}</a>
                            @endif
                            @endforeach
                        </div>
                        </div>
                    </div>

                    <td>{{ $row->jenis_paten ?? '-' }}</td>

                    <td>
                    <span class="status-pill s-{{ $row->status }}">{{ $row->status }}</span>
                    </td>

                    {{-- helper render dokumen + verifikasi --}}
                    @php
                    $renderDocCell = function($k) use ($row) {
                        $filePath = $row->$k ?? null;
                        $doc = $row->docs[$k] ?? null;
                        $statusDoc = optional($doc)->status ?? 'pending';
                    @endphp
                    <div>
                        @if($filePath)
                        <a class="doc-link" href="{{ asset('storage/'.$filePath) }}" target="_blank">
                            {{ basename($filePath) }}
                        </a>
                        @else
                        <span class="muted">-</span>
                        @endif

                        <div class="verif-mini">
                        <div class="verif-mini-head">
                            <span class="badge badge-{{ $statusDoc }}"
                                data-doc-badge
                                data-doc-key="{{ $k }}">
                            {{ strtoupper($statusDoc) }}
                            </span>
                        </div>

                        <div class="verif-mini-actions">
                            {{-- ✅ OK (AJAX) --}}
                            <form class="js-doc-form"
                                method="POST"
                                action="{{ route('admin.verifikasi_dokumen.set', ['type'=>'paten','id'=>$row->id]) }}">
                            @csrf
                            <input type="hidden" name="doc_key" value="{{ $k }}">
                            <input type="hidden" name="action" value="ok">
                            <button class="btn-mini" type="submit">OK</button>
                            </form>

                            {{-- ✅ REVISI (AJAX) --}}
                           {{-- ✅ REVISI (POPUP, gak nambah tinggi baris) --}}
                            <div class="rev-dd" data-rev>
                            <button type="button" class="btn-mini rev-btn" data-rev-btn>Revisi</button>

                            <div class="rev-pop" data-rev-pop hidden>
                                <form class="js-doc-form"
                                    method="POST"
                                    enctype="multipart/form-data"
                                    action="{{ route('admin.verifikasi_dokumen.set', ['type'=>'paten','id'=>$row->id]) }}">
                                @csrf
                                <input type="hidden" name="doc_key" value="{{ $k }}">
                                <input type="hidden" name="action" value="revisi">

                                <textarea name="note" rows="3" class="input"
                                    placeholder="Catatan revisi (wajib)">{{ optional($doc)->note }}</textarea>

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
                    </div>
                    @php }; @endphp

                    <td>@php $renderDocCell('draft_paten'); @endphp</td>
                    <td>@php $renderDocCell('form_permohonan'); @endphp</td>
                    <td>@php $renderDocCell('surat_kepemilikan'); @endphp</td>
                    <td>@php $renderDocCell('surat_pengalihan'); @endphp</td>
                    <td>@php $renderDocCell('scan_ktp'); @endphp</td>
                    <td>@php $renderDocCell('tanda_terima'); @endphp</td>

                    <td>@php $renderDocCell('gambar_prototipe'); @endphp</td>

                    <td>
                    @if(!empty($row->deskripsi_singkat_prototipe))
                        {{ $row->deskripsi_singkat_prototipe }}
                    @else
                        <span class="muted">-</span>
                    @endif
                    </td>

                    <td class="cell-actions">
                        <div class="action-box">

                            <div class="action-row">
                            <button type="button"
                                class="btn-mini btn-detail"
                                data-detail-type="paten"
                                data-no="{{ $row->no_pendaftaran }}"
                                data-judul="{{ $row->judul_paten }}"
                                data-jenis="{{ $row->jenis_paten }}"
                                data-nama="{{ $row->nama_pencipta }}"
                                data-nip="{{ $row->nip_nim }}"
                                data-hp="{{ $row->nomor_hp ?? $row->no_hp }}"
                                data-email="{{ $row->email }}"
                                data-fakultas="{{ $row->fakultas }}"
                                data-prototipe="{{ $row->prototipe }}"
                                data-nilai="{{ $row->nilai_perolehan }}"
                                data-sumber="{{ $row->sumber_dana }}"
                                data-skema="{{ $row->skema_penelitian }}"
                            >
                                Detail
                            </button>

                            <button type="button"
                                class="btn-delete"
                                data-delete-action="{{ route('admin.paten.destroy', $row->id) }}"
                                data-delete-type="paten"
                                title="Hapus">
                                <img src="{{ asset('images/delete.png') }}" alt="Hapus" class="ic-delete">
                            </button>
                            </div>

                            <form class="js-send-revisi-form action-revisi"
                            method="POST"
                            action="{{ route('admin.verifikasi_dokumen.sendRevisi', ['type'=>'paten','id'=>$row->id]) }}"
                            @if(!$hasDocRevisi) hidden @endif
                            data-send-revisi>
                            @csrf
                            <button type="submit" class="btn-mini btn-revisi">Kirim Permintaan Revisi</button>
                            <div class="inline-msg" data-inline-msg></div>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="center muted">Belum ada data paten</td>
                </tr>
                @endforelse
            </tbody>
            </table>
        </div>
        <div class="table-footer" data-pager="paten">
            <div class="table-info" data-info>Showing 0 to 0 of 0 entries</div>

            <div class="table-controls">
                <label class="entries-wrap">
                Show
                <select class="entries-select" data-entries>
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                entries
                </label>

                <div class="pagination" data-pagination></div>
            </div>
        </div>
        @endif

        {{-- ================= TAB: STATUS VERIFIKASI ================= --}}
        @if($tab === 'status')
        <div class="page-head">
            <h2 class="page-title">Status Verifikasi</h2>

            <div class="page-actions">
            <div class="dd" data-dd>
                <button type="button" class="dd-btn" data-dd-btn>
                <span data-dd-label id="filterTypeLabel">Semua</span>
                <span class="dd-caret"></span>
                </button>

                <div class="dd-menu" data-dd-menu hidden>
                <button type="button" class="dd-item active" data-value="all">Semua</button>
                <button type="button" class="dd-item" data-value="paten">Paten</button>
                <button type="button" class="dd-item" data-value="cipta">Hak Cipta</button>
                </div>

                <input type="hidden" id="filterType" value="all" data-dd-input>
            </div>

            <input id="searchStatus" class="search-input" type="text"
                    placeholder="Cari no pendaftaran / status" />
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-card">
            <table class="data-table" id="statusTable">
            <thead>
                <tr>
                <th style="width:70px;">No</th>
                <th style="width:125px;">Kategori</th>
                <th style="width:150px;">Jenis</th>
                <th style="min-width:120px;">No Pendaftaran</th>
                <th style="min-width:230px;">Judul</th>
                <th style="width:280px;">Update Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($dataStatus as $i => $row)
                <tr
                    data-type="{{ strtolower($row->type) }}"
                    data-key="{{ strtolower(($row->no_pendaftaran ?? '').' '.($row->status ?? '').' '.($row->jenis ?? '')) }}"
                >
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ strtoupper($row->type) }}</td>
                    <td>{{ $row->jenis ?? '-' }}</td>
                    <td>{{ $row->no_pendaftaran ?? '-' }}</td>
                    <td>{{ $row->judul ?? '-' }}</td>

                    <td>
                    {{-- ✅ FORM UPDATE STATUS (AJAX) --}}
                    <form class="inline-form js-status-form" method="POST"
                        action="{{ route('admin.status.update', ['type' => $row->type, 'id' => $row->id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="dd dd-status" data-dd>
                        <button type="button" class="dd-btn" data-dd-btn>
                        <span data-dd-label>{{ $row->status }}</span>
                        <span class="dd-caret"></span>
                        </button>

                        <div class="dd-menu" data-dd-menu hidden>
                        @foreach(['terkirim','proses','revisi','diterima','ditolak'] as $st)
                            <button type="button"
                                    class="dd-item {{ $row->status===$st ? 'active' : '' }}"
                                    data-value="{{ $st }}">
                            {{ $st }}
                            </button>
                        @endforeach
                        </div>

                        <input type="hidden" name="status" value="{{ $row->status }}" data-dd-input>
                    </div>

                    <button class="btn-mini btn-save" type="submit">Simpan</button>

                    {{-- ✅ pesan kecil untuk status simpan --}}
                    <div class="inline-msg" data-inline-msg style="margin-top:6px;font-size:12px;"></div>
                    </form>

                    {{-- ✅ slot WA kamu --}}
                    <div class="wa-slot" data-wa-slot style="margin-top:8px;"></div>

                    {{-- ringkasan dokumen revisi (kalau status revisi) --}}
                    @if(($row->status ?? '') === 'revisi')
                        @php
                        $revisiDocs = collect($row->docs ?? [])->filter(fn($d) => (($d->status ?? '') === 'revisi'));
                        @endphp

                        <div style="margin-top:10px;">
                        @if($revisiDocs->count() > 0)
                            <div style="font-size:12px; margin-bottom:6px;">
                            <b>Dokumen perlu revisi ({{ $revisiDocs->count() }})</b>
                            </div>

                            <div style="display:flex; flex-direction:column; gap:6px; font-size:12px;">
                            @foreach($revisiDocs as $k => $d)
                                <div style="padding:8px; border:1px solid #e6e6e6; border-radius:10px;">
                                <div><b>{{ $docLabels[$k] ?? $k }}</b></div>

                                @if(!empty($d->note))
                                    <div class="muted" style="margin-top:4px;">Catatan: {{ $d->note }}</div>
                                @endif

                                @if(!empty($d->admin_attachment_path))
                                    <div style="margin-top:4px;">
                                    Lampiran admin:
                                    <a href="{{ asset('storage/'.$d->admin_attachment_path) }}" target="_blank">
                                        {{ basename($d->admin_attachment_path) }}
                                    </a>
                                    </div>
                                @endif
                                </div>
                            @endforeach
                            </div>

                            <div class="muted" style="font-size:12px; margin-top:6px;">
                            *Permintaan revisi dikirim lewat tombol <b>Kirim Permintaan Revisi</b> (di tab Paten/Cipta).
                            </div>
                        @else
                            <span class="badge badge-warn" style="margin-top:6px; display:inline-block;">
                            Status revisi dipilih, tapi belum ada dokumen yang ditandai revisi.
                            </span>
                        @endif
                        </div>
                    @endif

                    {{-- upload sertifikat (kalau diterima) --}}
                    @if(($row->status ?? '') === 'diterima')
                        <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                        @if(!empty($row->sertifikat_path))
                            <span class="badge badge-success">Sertifikat: OK</span>
                        @else
                            <span class="badge badge-warn">Upload sertifikat DJKI</span>
                        @endif

                        @if(!empty($row->emailed_at))
                            <span class="badge badge-muted">Email terkirim</span>
                        @else
                            <span class="badge badge-muted">Email belum terkirim</span>
                        @endif
                        </div>

                        <form method="POST"
                            action="{{ route('admin.status.uploadSertifikat', ['type' => $row->type, 'id' => $row->id]) }}"
                            enctype="multipart/form-data"
                            style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                        @csrf
                        <input type="file" name="sertifikat" accept=".pdf,.jpg,.jpeg,.png" required>
                        <button type="submit" class="btn-mini">Upload Sertifikat</button>
                        </form>

                        @if(!empty($row->sertifikat_path))
                        <form method="POST"
                                action="{{ route('admin.status.resendEmail', ['type' => $row->type, 'id' => $row->id]) }}"
                                style="margin-top:6px;">
                            @csrf
                            <button type="submit" class="btn-mini">Kirim Ulang Email</button>
                        </form>
                        @endif
                    @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="center muted">Belum ada data status verifikasi</td>
                </tr>
                @endforelse
            </tbody>
            </table>
        </div>
        <div class="table-footer" data-pager="status">
            <div class="table-info" data-info>Showing 0 to 0 of 0 entries</div>

            <div class="table-controls">
                <label class="entries-wrap">
                Show
                <select class="entries-select" data-entries>
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                entries
                </label>

                <div class="pagination" data-pagination></div>
            </div>
        </div>
        @endif
    </main>
</div>

<!-- MODAL KONFIRMASI LOGOUT -->
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

{{-- ✅ MODAL UBAH PASSWORD --}}
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

{{-- MODAL KONFIRMASI HAPUS (PAKAI UNTUK PATEN & CIPTA) --}}
<div class="modal-backdrop" id="deleteBackdrop" hidden></div>

<div class="modal" id="deleteModal" hidden>
  <div class="modal-card modal-lg">
    <div class="modal-icon">!</div>

    <h3 class="modal-title" id="deleteTitle">Konfirmasi Hapus</h3>

    <p class="modal-text" id="deleteText">
      Apakah yakin ingin menghapus data ini?
      <br>
      <span class="modal-warning">Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</span>
    </p>

    <div class="modal-actions">
      <button type="button" class="btn-ghost" id="cancelDelete">Batal</button>

      <form method="POST" id="deleteForm">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Hapus</button>
      </form>
    </div>
  </div>
</div>

{{-- ✅ Data chart dipindah ke JSON (JS yang render) --}}
@if($tab === 'stats')
  <script id="chart-data" type="application/json">
    {!! json_encode([
      'patenLabels' => array_keys($patenJenis),
      'patenData'   => array_values($patenJenis),
      'ciptaLabels' => array_keys($ciptaJenis),
      'ciptaData'   => array_values($ciptaJenis),
    ]) !!}
  </script>
@endif

{{-- DRAWER DETAIL PEMOHON --}}
<div class="modal-backdrop" id="detailBackdrop" hidden></div>

<aside class="detail-drawer" id="detailDrawer" hidden aria-hidden="true">
  <div class="detail-drawer-head">
    <div>
      <div class="detail-title" id="detailTitle">Detail</div>
      <div class="detail-sub" id="detailSub">-</div>
    </div>

    <button type="button" class="btn-ghost" id="closeDetail">Tutup</button>
  </div>

  <div class="detail-drawer-body" id="detailBody">
    {{-- diisi via JS --}}
  </div>
</aside>

</body>
</html>