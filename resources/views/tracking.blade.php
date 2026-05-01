@extends('layouts.app')
@section('title','Tracking')

@section('content')

{{-- SweetAlert error --}}
@if (!empty($swal_error ?? null))
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: 'Kode pengajuan tidak ditemukan, lakukan verifikasi berkas terlebih dahulu.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#2563eb',
        customClass: {
          popup: 'swal-popup'
        }
      });

    });
  </script>
@endif

<section class="section-full section-content tracking-page">
  <div class="section-inner">
    <div class="content-box tracking-box">
       <div class="tracking-content">
        <button type="button" class="btn-back" onclick="history.back()" aria-label="Kembali">
          <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h2>Cek Status Verifikasi</h2>

        <form action="{{ route('tracking') }}" method="GET" class="tracking-form horizontal">
          <input
            type="text"
            name="q"
            class="input tracking-input"
            placeholder="Masukkan No Pendaftaran"
            value="{{ $q ?? request('q') }}"
            required
          >
          <button type="submit" class="btn-selanjutnya tracking-btn">Cek</button>
        </form>

        {{-- Timeline hanya tampil kalau status tersedia (data ketemu) --}}
        @isset($status)
          @php
            $steps = [
              ['key' => 'terkirim', 'title' => 'Terkirim'],
              ['key' => 'proses',   'title' => 'Proses'],
              ['key' => 'revisi',   'title' => 'Revisi'],
              ['key' => 'approve',  'title' => 'Approve'],
            ];

            $rank = ['terkirim'=>1, 'proses'=>2, 'revisi'=>3, 'approve'=>4];
            $currentRank = $rank[$status] ?? 1;
            $isFinalAccepted = ($status === 'approve');
          @endphp

          <div class="timeline">
            <div class="timeline-line"></div>

            @foreach ($steps as $i => $s)
              @php
                $stepRank = $rank[$s['key']] ?? ($i + 1);

                if ($stepRank < $currentRank) $state = 'done';
                elseif ($stepRank == $currentRank) $state = 'doing';
                else $state = 'todo';

                if ($isFinalAccepted && $s['key'] === 'approve') $state = 'done';
              @endphp

              <div class="timeline-item">
                <div class="timeline-dot {{ $state }}"></div>
                <div class="timeline-card">
                  <div class="timeline-title">{{ strtoupper($s['title']) }}</div>
                  <div class="timeline-time">
                    <p>Terakhir diperbarui: {{ $updatedAt ?? '-' }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="legend">
            <span><i class="lg done"></i> Selesai</span>
            <span><i class="lg doing"></i> Sedang Berlangsung</span>
            <span><i class="lg todo"></i> Belum Diproses</span>
          </div>
        @endisset
      </div>
    </div>
  </div>
</section>
@endsection
