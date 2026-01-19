@extends('layouts.app')
@section('title','Tracking')

@section('content')

@php
  // urutan step
  $steps = [
    ['key' => 'terkirim', 'title' => 'Terkirim'],
    ['key' => 'proses',   'title' => 'Diproses'],
    ['key' => 'revisi',   'title' => 'Revisi'],
    ['key' => 'diterima', 'title' => 'Diterima'],
    ['key' => 'ditolak',  'title' => 'Ditolak'],
  ];

  // mapping posisi status
  $rank = ['terkirim'=>1, 'proses'=>2, 'revisi'=>3, 'diterima'=>4, 'ditolak'=>4];
  $currentRank = $rank[$status] ?? 1;

  // status akhir: diterima / ditolak
  $isFinalAccepted = ($status === 'diterima');
  $isFinalRejected = ($status === 'ditolak');
@endphp

<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box tracking-box">
      <h2>Cek Status Pendaftaran</h2>

      <form action="{{ route('tracking') }}" method="GET" class="tracking-form">
        <input type="text" name="q" class="input tracking-input"
               placeholder="Masukkan No Pendaftaran"
               value="{{ request('q') }}" required>
        <button type="submit" class="btn-selanjutnya tracking-btn">Cek</button>
      </form>

      <div class="timeline">
  <div class="timeline-line"></div>

  @foreach ($steps as $i => $s)
    @php
      $stepRank = $rank[$s['key']] ?? ($i+1);

      // warna
      // kalau ditolak: step ditolak = merah (nggak jalan)
      if ($isFinalRejected) {
        if ($s['key'] === 'ditolak') $state = 'done';
        elseif ($s['key'] === 'diterima') $state = 'todo';
        else $state = ($stepRank <= $currentRank) ? 'done' : 'todo';
      } 
      else {
        // normal flow
        if ($stepRank < $currentRank) $state = 'done';
        elseif ($stepRank == $currentRank) $state = 'doing';
        else $state = 'todo';

        // kalau final diterima, step diterima jadi done
        if ($isFinalAccepted && $s['key'] === 'diterima') $state = 'done';
        if ($isFinalAccepted && $s['key'] === 'ditolak') $state = 'todo';

      }
    @endphp

    <div class="timeline-item">
      <div class="timeline-dot {{ $state }}"></div>
        <div class="timeline-card">
            <div class="timeline-title">{{ strtoupper($s['title']) }}</div>
            <div class="timeline-time">
            <p>Terakhir diperbarui: {{ $updatedAt ?? '' }}</p>
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
