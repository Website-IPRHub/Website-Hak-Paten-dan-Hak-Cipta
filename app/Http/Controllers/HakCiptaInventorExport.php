<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class HakCiptaInventorExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    private Collection $rows;

    public function __construct()
    {
        $this->rows = collect();

        $items = DB::table('hak_cipta_verifs')
            ->orderByDesc('id')
            ->get();

        foreach ($items as $c) {
            $inventors = [];

            if (!empty($c->inventors) && is_string($c->inventors)) {
                $decoded = json_decode($c->inventors, true);
                $inventors = is_array($decoded) ? $decoded : [];
            } elseif (!empty($c->inventors) && is_array($c->inventors)) {
                $inventors = $c->inventors;
            }

            if (count($inventors) === 0) {
                $inventors = [[
                    'urut'     => 1,
                    'nama'     => $c->nama_pencipta ?? '-',
                    'status'   => $c->status_pencipta ?? ($c->status_inventor ?? ($c->role ?? '-')),
                    'nip_nim'  => $c->nip_nim ?? '-',
                    'fakultas' => $c->fakultas ?? '-',
                    'no_hp'    => $c->nomor_hp ?? ($c->no_hp ?? '-'),
                    'email'    => $c->email ?? '-',
                ]];
            }

            $inventors = collect($inventors)->map(function ($i) {
                return [
                    'urut'     => $i['urut'] ?? $i['inventor_ke'] ?? 1,
                    'nama'     => $i['nama'] ?? '-',
                    'status'   => $i['status'] ?? '-', // Mahasiswa/Dosen
                    'nip_nim'  => $i['nip_nim'] ?? ($i['nip'] ?? ($i['nim'] ?? '-')),
                    'fakultas' => $i['fakultas'] ?? '-',
                    'no_hp'    => $i['no_hp'] ?? ($i['nomor_hp'] ?? ($i['hp'] ?? '-')),
                    'email'    => $i['email'] ?? '-',
                ];
            })->values()->all();

            foreach ($inventors as $inv) {
                $this->rows->push((object)[
                    'no_pendaftaran' => $c->no_pendaftaran ?? '-',
                    'judul'          => $c->judul_cipta ?? '-',
                    'jenis'          => $c->jenis_cipta ?? '-',
                    'inventor_ke'    => $inv['urut'] ?? 1,
                    'nama'           => $inv['nama'] ?? '-',
                    'status'         => $inv['status'] ?? '-',
                    'nip_nim'         => $inv['nip_nim'] ?? '-',
                    'fakultas'        => $inv['fakultas'] ?? '-',
                    'no_hp'           => $inv['no_hp'] ?? '-',
                    'email'           => $inv['email'] ?? '-',
                ]);
            }
        }
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No Pendaftaran',
            'Judul Cipta',
            'Jenis Cipta',
            'Inventor Ke',
            'Nama Inventor',
            'Status',
            'NIP/NIM',
            'Fakultas',
            'No HP',
            'Email',
        ];
    }

   public function map($row): array
    {
        return [
            $row->no_pendaftaran ?? '-',
            $this->cleanText($row->judul ?? '-'), 
            $this->cleanText($row->jenis ?? '-'),
            $row->inventor_ke ?? 1,
            $this->cleanText($row->nama ?? '-'),
            $this->cleanText($row->status ?? '-'),
            (string)($row->nip_nim ?? '-'),       
            $this->cleanText($row->fakultas ?? '-'),
            $this->cleanText($row->no_hp ?? '-'),
            $this->cleanText($row->email ?? '-'),
        ];
    }


    public function columnWidths(): array
    {
        return [
            'A' => 16, // no pendaftaran
            'B' => 60, // judul
            'C' => 18, // jenis
            'D' => 12, // inventor ke
            'E' => 22, // nama
            'F' => 12, // status
            'G' => 18, // nip/nim (TEXT)
            'H' => 28, // fakultas
            'I' => 16, // no hp
            'J' => 26, // email
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B:B')->getAlignment()->setWrapText(true);

        return [];
    }

    private function cleanText($v): string
    {
        $s = trim((string)($v ?? ''));
        $s = str_replace(['"', '“', '”'], '', $s);
        return $s === '' ? '-' : $s;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:J1");
                $sheet->getStyle("A1:J{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("G2:G{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                $sheet->getStyle("J:J")->getAlignment()->setWrapText(false);

                for ($r = 2; $r <= $highestRow; $r++) {
                    $cell = $sheet->getCell("G{$r}");
                    $val  = (string)$cell->getValue();
                    $sheet->setCellValueExplicit("G{$r}", $val, DataType::TYPE_STRING);
                }
            }
        ];
    }
}
