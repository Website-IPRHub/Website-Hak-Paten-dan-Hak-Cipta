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

      {{-- ✅ PENCARIAN / CEK DIHAPUS --}}

      <div class="pd-tracker" data-active="{{ $activeStatus }}">
        @foreach($steps as $s)
          <div class="pd-step" data-step="{{ $s['key'] }}">
            <div class="pd-dot"></div>
            <div class="pd-step-body">
              <div class="pd-step-title">{{ $s['label'] }}</div>
              <div class="pd-step-sub">Terakhir diperbarui: {{ $s['updated_at'] }}</div>
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
