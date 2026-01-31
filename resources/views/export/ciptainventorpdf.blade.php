<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
    .title { font-weight: 700; margin-bottom: 8px; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #000; padding: 4px; vertical-align: top; word-wrap: break-word; }
    th { text-align: center; font-weight: 700; }

    /* width kolom biar mirip paten */
    .c-nop   { width: 11%; }
    .c-judul { width: 34%; }
    .c-jenis { width: 10%; }
    .c-inv   { width: 5%;  text-align:center; }
    .c-nama  { width: 10%; }
    .c-stat  { width: 7%;  }
    .c-nip   { width: 9%;  }
    .c-mail  { width: 13%; }
    .c-fak   { width: 13%; }
    .c-hp    { width: 10%; }
  </style>
</head>
<body>
  <div class="title">Data Hak Cipta</div>

  <table>
    <thead>
      <tr>
        <th class="c-nop">No Pendaftaran</th>
        <th class="c-judul">Judul Cipta</th>
        <th class="c-jenis">Jenis Cipta</th>
        <th class="c-inv">Inv</th>
        <th class="c-nama">Nama</th>
        <th class="c-stat">Status</th>
        <th class="c-nip">NIP/NIM</th>
        <th class="c-mail">Email</th>
        <th class="c-fak">Fakultas</th>
        <th class="c-hp">No HP</th>
      </tr>
    </thead>

    <tbody>
      @foreach($items as $row)
        <tr>
          <td class="c-nop">{{ $row->no_pendaftaran }}</td>
          <td class="c-judul">{{ str_replace(['"', '“', '”'], '', $row->judul) }}</td>
          <td class="c-jenis">{{ $row->jenis }}</td>
          <td class="c-inv">{{ $row->inventor_ke }}</td>
          <td class="c-nama">{{ $row->nama }}</td>
          <td class="c-stat">{{ $row->status }}</td>
          <td class="c-nip">{{ $row->nip_nim }}</td>
          <td class="c-mail">{{ $row->email }}</td>
          <td class="c-fak">{{ $row->fakultas }}</td>
          <td class="c-hp">{{ $row->no_hp }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
