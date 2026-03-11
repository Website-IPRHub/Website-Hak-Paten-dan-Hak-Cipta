<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Akun Login Pemohon</title>
</head>
<body>
  <p>Yth. Bapak/Ibu,</p>

  @if(($type ?? 'created') === 'changed')
    <p>
      Password akun pengajuan <b>{{ $kodePengajuan }}</b> baru saja <b>diperbarui</b>.
      Berikut kredensial terbaru untuk login:
    </p>
  @else
    <p>
      Akun login pemohon Anda telah dibuat.
      Silakan gunakan kredensial berikut untuk login:
    </p>
  @endif

  <p>
    <strong>Username:</strong> {{ $username }} <br>
    <strong>Password:</strong> {{ $password }}
  </p>

  <p>
    Login melalui:<br>
    <a href="{{ url('/pemohon/login') }}">{{ url('/pemohon/login') }}</a>
  </p>

  @if(($type ?? 'created') !== 'changed')
    <p>Setelah login, Anda dapat mengganti password sesuai kebutuhan.</p>
  @else
    <p>Jika Anda tidak merasa melakukan perubahan password, segera hubungi admin.</p>
  @endif

  <br>
  <p>Hormat kami,<br>Admin KIHub</p>
</body>
</html>