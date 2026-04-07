<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 12px; }

    body {
      font-family: "Times New Roman", Times, serif;
      font-size: 9px;
    }

    h3 { margin: 0 0 8px 0; }

    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
    }

    th, td {
      border: 1px solid #333;
      padding: 4px;
      vertical-align: top;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }

    th {
      text-align: center;
      background: #f2f2f2;
      font-weight: bold;
    }

    .w-no     { width: 7%; }
    .w-judul  { width: 20%; }
    .w-jenis  { width: 8%; }
    .w-inv    { width: 4%;  text-align:center; }
    .w-nama   { width: 12%; }
    .w-status { width: 7%;  }
    .w-nip    { width: 9%;  }
    .w-email  { width: 15%; }
    .w-fak    { width: 15%; }
    .w-hp     { width: 8%;  }

    thead { display: table-header-group; }
    tr { page-break-inside: avoid; }
  </style>
</head>
<body>

<h3>Data Paten</h3>

<table>
  <thead>
    <tr>
      <th class="w-no">No Pendaftaran</th>
      <th class="w-judul">Judul Paten</th>
      <th class="w-jenis">Jenis Paten</th>
      <th class="w-inv">Inv</th>
      <th class="w-nama">Nama</th>
      <th class="w-status">Status</th>
      <th class="w-nip">NIP/NIM</th>
      <th class="w-email">Email</th>
      <th class="w-fak">Fakultas</th>
      <th class="w-hp">No HP</th>
    </tr>
  </thead>

  <tbody>
  @foreach($items as $item)
    @php
      $inventors = $item->inventors_arr ?? ($item->inventors ?? []);

      if ($inventors instanceof \Illuminate\Support\Collection) {
        $inventors = $inventors->values();
      }

      $rowspan = is_countable($inventors) ? count($inventors) : 0;
    @endphp

    {{-- kalau inventor kosong --}}
    @if($rowspan === 0)
      <tr>
        <td>{{ $item->no_pendaftaran }}</td>
        <td>{{ $item->judul_paten }}</td>
        <td>{{ $item->jenis_paten }}</td>
        <td colspan="7" style="text-align:center;">- Tidak ada inventor -</td>
      </tr>
    @else
      @foreach($inventors as $i => $inv)
        @php
          $nama     = is_array($inv) ? ($inv['nama'] ?? '')     : ($inv->nama ?? '');
          $status   = is_array($inv) ? ($inv['status'] ?? '')   : ($inv->status ?? '');
          $nip_nim  = is_array($inv) ? ($inv['nip_nim'] ?? '')  : ($inv->nip_nim ?? '');
          $email    = is_array($inv) ? ($inv['email'] ?? '')    : ($inv->email ?? '');
          $fakultas = is_array($inv) ? ($inv['fakultas'] ?? '') : ($inv->fakultas ?? '');
          $no_hp    = is_array($inv) ? ($inv['no_hp'] ?? '')    : ($inv->no_hp ?? '');
        @endphp

        <tr>
          {{-- merge kolom utama --}}
          @if($i === 0)
            <td rowspan="{{ $rowspan }}">{{ $item->no_pendaftaran }}</td>
            <td rowspan="{{ $rowspan }}">{{ $item->judul_paten }}</td>
            <td rowspan="{{ $rowspan }}">{{ $item->jenis_paten }}</td>
          @endif

          <td class="w-inv">{{ $i + 1 }}</td>
          <td>{{ $nama }}</td>
          <td>{{ $status }}</td>
          <td>{{ $nip_nim }}</td>
          <td>{{ $email }}</td>
          <td>{{ $fakultas }}</td>
          <td>{{ $no_hp }}</td>
        </tr>
      @endforeach
    @endif
  @endforeach
  </tbody>
</table>

</body>
</html>
