@extends('layouts.app')

@section('title', 'Login Pemohon')

@vite(['resources/js/pemohon/login.js'])

@section('content')
  <main class="login-page">
    <div class="login-spacer" aria-hidden="true"></div>

    <section class="login-shell">
      <div class="login-panel">
        <div class="login-grid">

          <div class="login-left">
            <div class="logo-badge">
              <img src="{{ asset('images/dirinov26.png') }}" alt="Inovasi & Kerja Sama" class="login-logo" />
            </div>
          </div>

          <div class="login-right">
            <div class="login-card">
              <h1 class="login-title">Login</h1>

              @if ($errors->any())
                <div class="login-alert">{{ $errors->first() }}</div>
              @endif

              @if (session('success'))
                <div class="login-alert success">{{ session('success') }}</div>
              @endif

              <form method="POST" action="{{ route('pemohon.login') }}" class="login-form">
                @csrf

                <div class="field">
                  <label class="login-label">Username / Kode Unik</label>
                  <input type="text" name="username" class="login-input" placeholder="Masukkan kode unik" autocomplete="username" required />
                </div>

                <div class="field">
                  <label class="login-label">Password</label>
                  <input type="password" name="password" class="login-input" placeholder="Masukkan password" autocomplete="current-password" required />
                </div>

                <div class="login-actions">
                  <a href="#" class="login-link" id="openChangePw" onclick="return false;">Ganti Password</a>
                </div>

                <div class="login-btn-row">
                  <button type="submit" class="login-btn">Login</button>
                </div>

                <p class="login-hint">
                  Gunakan <b>kode unik</b> yang kamu terima setelah submit verifikasi dokumen.
                </p>
              </form>
            </div>
          </div>

        </div>
      </div>
    </section>

    <div class="login-spacer" aria-hidden="true"></div>
  </main>

  {{-- MODAL: GANTI PASSWORD --}}
  <div class="cp-backdrop" id="cpBackdrop" hidden></div>

  <div class="cp-modal" id="cpModal" hidden role="dialog" aria-modal="true" aria-labelledby="cpTitle">
    <div class="cp-card">
      <div class="cp-head">
        <div>
          <div class="cp-title" id="cpTitle">Ganti Password</div>
          <div class="cp-subtitle">Masukkan kode unik dan password baru.</div>
        </div>
        <button type="button" class="cp-close" id="closeChangePw" aria-label="Tutup">✕</button>
      </div>

      {{-- DUMMY dulu biar gak error route --}}
      <form method="POST" action="#" class="cp-form" id="cpForm" onsubmit="return false;">
        @csrf

        <div class="cp-field">
          <label class="cp-label">Username / Kode Unik</label>
          <input type="text" name="username" class="cp-input" placeholder="Masukkan kode unik" required>
        </div>

        <div class="cp-field">
          <label class="cp-label">Password Baru</label>
          <input type="password" name="new_password" class="cp-input" placeholder="Minimal 6 karakter" minlength="6" required>
        </div>

        <div class="cp-field">
          <label class="cp-label">Konfirmasi Password Baru</label>
          <input type="password" name="new_password_confirmation" class="cp-input" placeholder="Ulangi password baru" minlength="6" required>
        </div>

        <div class="cp-actions">
          <button type="button" class="cp-btn ghost" id="cancelChangePw">Batal</button>
          <button type="submit" class="cp-btn primary" id="saveChangePw">Simpan</button>
        </div>

        <div class="cp-inline-msg" id="cpMsg" aria-live="polite"></div>
      </form>
    </div>
  </div>
@endsection
