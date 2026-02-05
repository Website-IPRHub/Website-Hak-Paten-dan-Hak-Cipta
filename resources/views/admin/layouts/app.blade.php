@php
  // default
  $tab  = $tab ?? request()->get('tab', 'stats');
  $sub  = $sub ?? request()->get('sub', 'all');
  $name = $name ?? 'Admin';
  $notifCount = $notifCount ?? 0;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard Admin')</title>

  {{-- Core assets yang harus sama --}}
  @vite(['resources/css/admin.css', 'resources/js/app.js'])

  {{-- Halaman boleh nambah assets sendiri --}}
  @yield('page_assets')
</head>

<body class="admin-page @yield('body_class')">

<header class="admin-header">
  <div class="brand">
    <img src="{{ asset('images/logo.jpg') }}?v={{ filemtime(public_path('images/logo.jpg')) }}" alt="Logo">
  </div>

  <div class="header-actions">
    @php
        // fallback default (kalau belum ada notif terbaru yang bisa diarahkan)
        $notifUrl = $notifUrl ?? route('admin.dashboard', ['tab' => 'paten']);
    @endphp

    <a href="{{ $notifUrl }}" class="notif-icon-btn" title="Notif Revisi">
        <img src="{{ asset('images/notif.png') }}" alt="Notif" class="notif-ic">
        @if(($notifCount ?? 0) > 0)
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
    @yield('content')
  </main>
</div>

{{-- Modal logout + ubah password (sekali aja di layout biar konsisten) --}}
{{-- resources/views/admin/partials/modals.blade.php --}}

{{-- MODAL KONFIRMASI LOGOUT --}}
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

{{-- MODAL KONFIRMASI HAPUS --}}
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


@yield('page_scripts')
</body>
</html>
