<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="font-family:Arial, sans-serif; line-height:1.6;">
  <h3>Update Status Pengajuan</h3>

  <p>Halo,</p>

  <p>Status pengajuan Anda telah diperbarui oleh admin. Berikut detailnya:</p>

  <ul>
    <li><b>Kategori:</b> {{ $kategori }}</li>
    <li><b>No Pendaftaran:</b> {{ $noPendaftaran }}</li>
    <li><b>Judul:</b> {{ $judul }}</li>
    <li><b>Status Terbaru:</b> <b>{{ strtoupper($status) }}</b></li>
  </ul>

  @if($status === 'terkirim')
    <p>Pengajuan Anda sudah <b>terkirim</b> dan akan diproses.</p>
  @elseif($status === 'proses')
    <p>Pengajuan Anda sedang dalam tahap <b>proses</b> verifikasi.</p>
  @elseif($status === 'ditolak')
    <p>Mohon maaf, pengajuan Anda berstatus <b>ditolak</b>.</p>
  @endif

  <p>Terima kasih.</p>
</body>
</html>
