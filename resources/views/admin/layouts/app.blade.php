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
@include('admin.partials.modals') {{-- nanti bikin file ini, atau paste modal yang sudah kamu punya --}}

@yield('page_scripts')
</body>
</html>
