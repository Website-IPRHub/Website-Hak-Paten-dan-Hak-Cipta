@extends('layouts.app')
@section('title','Tracking')

@section('content')

@php
  // urutan step
  $steps = [
    ['key' => 'terkirim', 'title' => 'Terkirim'],
    ['key' => 'proses',   'title' => 'Proses'],
    ['key' => 'revisi',   'title' => 'Revisi'],
    ['key' => 'approve', 'title' => 'Approve'],
  ];

  // mapping posisi status
  $rank = ['terkirim'=>1, 'proses'=>2, 'revisi'=>3, 'approve'=>4];
  $currentRank = $rank[$status] ?? 1;

  // status akhir: approve / ditolak
  $isFinalAccepted = ($status === 'approve');
@endphp

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box tracking-box">
      <h2>Cek Status Pendaftaran</h2>

      <form action="{{ route('tracking') }}" method="GET" class="tracking-form horizontal">
        <input
          type="text"
          name="q"
          class="input tracking-input"
          placeholder="Masukkan No Pendaftaran"
          value="{{ request('q') }}"
          required
        >

        <button type="submit" class="btn-selanjutnya tracking-btn">
          Cek
        </button>
      </form>


      <div class="timeline">
  <div class="timeline-line"></div>
  @foreach ($steps as $i => $s)
  @php
    // posisi step saat ini
    $stepRank = $rank[$s['key']] ?? ($i + 1);

    if ($stepRank < $currentRank) {
        $state = 'done';
    } elseif ($stepRank == $currentRank) {
        $state = 'doing';
    } else {
        $state = 'todo';
    }

    // kalau final approve → approve jadi done
    if ($isFinalAccepted && $s['key'] === 'approve') {
        $state = 'done';
    }
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
</section>
@endsection
