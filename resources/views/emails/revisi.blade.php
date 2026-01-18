<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; background:#f6f7fb; padding:20px; }
    .card { background:#fff; border-radius:12px; padding:20px; max-width:720px; margin:auto; }
    .badge { display:inline-block; padding:6px 10px; border-radius:999px; background:#fff3cd; color:#856404; font-size:12px; font-weight:700; }
    .title { font-size:18px; font-weight:700; margin:10px 0; }
    .item { border:1px solid #e9ecf3; border-radius:12px; padding:12px; margin:10px 0; }
    .note { white-space:pre-line; margin:6px 0 0; }
  </style>
</head>
<body>
  <div class="card">
    <span class="badge">REVISI DIPERLUKAN</span>

    <div class="title">Permohonan {{ $kategori }} membutuhkan perbaikan</div>

    <p>
      <b>No Pendaftaran:</b> {{ $noPendaftaran }}<br>
      <b>Judul:</b> {{ $judul }}
    </p>

    <p><b>Dokumen yang perlu direvisi:</b></p>

    @foreach($items as $it)
      <div class="item">
        <b>{{ $it['label'] }}</b>
        <p class="note"><b>Catatan:</b><br>{{ $it['note'] ?: '-' }}</p>

        @if(!empty($it['has_attachment']))
          <p class="note"><i>Lampiran file revisi/admin disertakan di email ini.</i></p>
        @endif
      </div>
    @endforeach

    <p>Silakan perbaiki dokumen sesuai catatan di atas dan unggah ulang di sistem.</p>
  </div>
</body>
</html>
