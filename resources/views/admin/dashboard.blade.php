<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    {{-- CSS + JS lewat Vite --}}
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
</head>
<body>

<header class="admin-header">
    <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>

    <div class="header-actions">
        <a href="#" class="user-icon">
            <img src="{{ asset('images/user.png') }}" alt="User">
        </a>

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
    // biar gak error undefined $tab
    $tab = $tab ?? 'stats';
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
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-card table-scroll">
                    <table class="data-table table-wide" id="ciptaTable">
                    <thead>
                        <tr>
                        <th rowspan="2" style="width:70px;">No</th>
                        <th rowspan="2" style="min-width:220px;">No Pendaftaran</th>
                        <th rowspan="2" style="min-width:250px;">Judul Cipta</th>
                        <th rowspan="2" style="width:160px;">Jenis</th>
                        <th rowspan="2" style="width:140px;">Status</th>

                        {{-- MERGE DOKUMEN --}}
                        <th colspan="5" class="th-doc-merge">DOKUMEN</th>

                        <th rowspan="2" style="min-width:220px;">Hasil Ciptaan</th>
                        <th rowspan="2" style="min-width:260px;">Link Ciptaan (Rekaman Video)</th>
                        </tr>

                        <tr>
                        <th style="min-width:180px;">Surat Permohonan</th>
                        <th style="min-width:180px;">Surat Pernyataan</th>
                        <th style="min-width:190px;">Surat Pengalihan</th>
                        <th style="min-width:160px;">Scan KTP</th>
                        <th style="min-width:180px;">Tanda Terima</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($dataCipta as $i => $row)
                        @php
                            // key buat fitur search JS (biar bisa cari judul/no/status/dll)
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
                            basename($row->scan_ktp ?? ''),
                            basename($row->tanda_terima ?? ''),
                            basename($row->hasil_ciptaan ?? ''),
                            $row->link_ciptaan ?? '',
                            ])));
                        @endphp

                        <tr data-key="{{ $ciptaKey }}">
                            <td>{{ $i+1 }}</td>

                            {{-- NO PENDAFTARAN --}}
                            <td>{{ $row->no_pendaftaran ?? '-' }}</td>

                            {{-- JUDUL + META (SAMAIN STYLE KAYAK PATEN) --}}
                            <td>
                            <div class="title-wrap">
                                <div class="title-main">{{ $row->judul_cipta ?? '-' }}</div>

                                <div class="title-meta">
                                <div class="meta-fakultas">{{ $row->fakultas ?? '-' }}</div>

                                <div class="meta-emails">
                                    @foreach(preg_split('/[\s,]+/', $row->email ?? '') as $mail)
                                    @if($mail)
                                        <a href="mailto:{{ $mail }}" class="email-chip">
                                        {{ $mail }}
                                        </a>
                                    @endif
                                    @endforeach
                                </div>
                                </div>
                            </div>
                            </td>

                            {{-- JENIS --}}
                            <td>{{ $row->jenis_cipta ?? '-' }}</td>

                            {{-- STATUS --}}
                            <td>
                            <span class="status-pill s-{{ $row->status }}">
                                {{ $row->status ?? '-' }}
                            </span>
                            </td>

                            {{-- Surat Permohonan --}}
                            <td>
                            @if($row->surat_permohonan)
                                <a class="doc-link" href="{{ asset('storage/'.$row->surat_permohonan) }}" target="_blank">
                                {{ basename($row->surat_permohonan) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                            </td>

                            {{-- Surat Pernyataan --}}
                            <td>
                            @if($row->surat_pernyataan)
                                <a class="doc-link" href="{{ asset('storage/'.$row->surat_pernyataan) }}" target="_blank">
                                {{ basename($row->surat_pernyataan) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                            </td>

                            {{-- Surat Pengalihan --}}
                            <td>
                            @if($row->surat_pengalihan)
                                <a class="doc-link" href="{{ asset('storage/'.$row->surat_pengalihan) }}" target="_blank">
                                {{ basename($row->surat_pengalihan) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                            </td>

                            {{-- Scan KTP --}}
                            <td>
                            @if($row->scan_ktp)
                                <a class="doc-link" href="{{ asset('storage/'.$row->scan_ktp) }}" target="_blank">
                                {{ basename($row->scan_ktp) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                            </td>

                            {{-- Tanda Terima --}}
                            <td>
                            @if($row->tanda_terima)
                                <a class="doc-link" href="{{ asset('storage/'.$row->tanda_terima) }}" target="_blank">
                                {{ basename($row->tanda_terima) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                            </td>

                            {{-- Hasil Ciptaan --}}
                            <td>
                            @if($row->hasil_ciptaan)
                                <a class="doc-link" href="{{ asset('storage/'.$row->hasil_ciptaan) }}" target="_blank">
                                {{ basename($row->hasil_ciptaan) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                            </td>

                            {{-- Link Ciptaan (jangan pakai storage karena ini URL) --}}
                            <td>
                            @if($row->link_ciptaan)
                                <a class="doc-link" href="{{ $row->link_ciptaan }}" target="_blank" rel="noopener">
                                {{ $row->link_ciptaan }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
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
                    <div class="alert-success">{{ session('success') }}</div>
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

                            {{-- MERGE DOKUMEN --}}
                            <th colspan="6" class="th-doc-merge">DOKUMEN</th>

                            <th rowspan="2" style="min-width:220px;">Gambar Prototipe</th>
                            <th rowspan="2" style="min-width:220px;">Deskripsi singkat prototipe/produk</th>
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
                    @endphp

                    <tr data-key="{{ $patenKey }}">
                        <td>{{ $i+1 }}</td>

                        {{-- NO PENDAFTARAN --}}
                        <td>{{ $row->no_pendaftaran ?? '-' }}</td>

                        {{-- JUDUL + META --}}
                        <td>
                            <div class="title-wrap">
                                <div 
                                class="title-main">{{ $row->judul_paten ?? '-' }}
                                </div>

                                <div class="title-meta">
                                    <div class="meta-fakultas">{{ $row->fakultas }}</div>

                                    <div class="meta-emails">
                                        @foreach(preg_split('/[\s,]+/', $row->email) as $mail)
                                            @if($mail)
                                                <a href="mailto:{{ $mail }}" class="email-chip">
                                                    {{ $mail }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- JENIS --}}
                        <td>{{ $row->jenis_paten }}</td>

                        {{-- STATUS --}}
                        <td>
                            <span class="status-pill s-{{ $row->status }}">
                                {{ $row->status }}
                            </span>
                        </td>

                        {{-- DRAFT --}}
                        <td>
                            @if($row->draft_paten)
                                <a class="doc-link" href="{{ asset('storage/'.$row->draft_paten) }}" target="_blank">
                                    {{ basename($row->draft_paten) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>

                        {{-- PERMOHONAN --}}
                        <td>
                            @if($row->form_permohonan)
                                <a class="doc-link" href="{{ asset('storage/'.$row->form_permohonan) }}" target="_blank">
                                    {{ basename($row->form_permohonan) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>

                        {{-- KEPEMILIKAN --}}
                        <td>
                            @if($row->surat_kepemilikan)
                                <a class="doc-link" href="{{ asset('storage/'.$row->surat_kepemilikan) }}" target="_blank">
                                    {{ basename($row->surat_kepemilikan) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>

                        {{-- PENGALIHAN --}}
                        <td>
                            @if($row->surat_pengalihan)
                                <a class="doc-link" href="{{ asset('storage/'.$row->surat_pengalihan) }}" target="_blank">
                                    {{ basename($row->surat_pengalihan) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>

                        {{-- SCAN KTP --}}
                        <td>
                            @if($row->scan_ktp)
                                <a class="doc-link" href="{{ asset('storage/'.$row->scan_ktp) }}" target="_blank">
                                    {{ basename($row->scan_ktp) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>

                        {{-- TANDA TERIMA --}}
                        <td>
                            @if($row->tanda_terima)
                                <a class="doc-link" href="{{ asset('storage/'.$row->tanda_terima) }}" target="_blank">
                                    {{ basename($row->tanda_terima) }}
                                </a>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>

                        {{-- GAMBAR PROTOTIPE --}}
                        <td>
                        @if($row->gambar_prototipe)
                            <a class="doc-link" href="{{ asset('storage/'.$row->gambar_prototipe) }}" target="_blank">
                            {{ basename($row->gambar_prototipe) }}
                            </a>
                        @else
                            <span class="muted">-</span>
                        @endif
                        </td>
                        
                        {{-- DESKRIPSI SINGKAT PROTOTIPE/PRODUK --}}
                        <td>
                        @if(!empty($row->deskripsi_singkat_prototipe))
                            {{ $row->deskripsi_singkat_prototipe }}
                        @else
                            <span class="muted">-</span>
                        @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="center muted">Belum ada data paten</td>
                    </tr>
                    @endforelse
                    </tbody>

                </table>
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

                        {{-- ini yang dipakai filter JS --}}
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
                            <th style="width:90px;">ID</th>
                            <th style="width:125px;">Jenis</th>
                            <th style="min-width:215px;">No Pendaftaran</th>
                            <th style="min-width:215px;">Judul</th>
                            <th style="width:220px;">Update Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($dataStatus as $row)
                            <tr
                                data-type="{{ strtolower($row->type) }}"
                                data-key="{{ strtolower(($row->no_pendaftaran ?? '').' '.$row->status) }}"
                            >
                                <td class="center">{{ $row->id }}</td>
                                <td>{{ strtoupper($row->type) }}</td>
                                <td>{{ $row->no_pendaftaran ?? '-' }}</td>
                                <td>{{ $row->judul ?? '-' }}</td>

                                <td>
                                    <form class="inline-form" method="POST"
                                        action="{{ $row->type === 'paten'
                                            ? route('admin.paten.updateStatus', $row->id)
                                            : route('admin.cipta.updateStatus', $row->id) }}">
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

                                            {{-- ini yang beneran dikirim ke controller --}}
                                            <input type="hidden" name="status" value="{{ $row->status }}" data-dd-input>
                                        </div>

                                        <button class="btn-mini btn-save" type="submit">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="center muted">Belum ada data status verifikasi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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

</body>
</html>
