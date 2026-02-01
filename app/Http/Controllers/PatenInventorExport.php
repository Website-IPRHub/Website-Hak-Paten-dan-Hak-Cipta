<?php

namespace App\Http\Controllers;

use App\Models\PatenVerif;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PatenInventorExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    private Collection $rows;

    public function __construct()
    {
        $data = PatenVerif::orderByDesc('id')->get();
        $this->rows = $this->flatten($data);
    }

    private function cleanText($v): string
    {
        $s = trim((string)($v ?? ''));
        // buang kutip biasa & kutip “”
        $s = str_replace(['"', '“', '”'], '', $s);
        return $s === '' ? '-' : $s;
    }

    private function flatten(Collection $data): Collection
    {
        return $data->flatMap(function ($p) {
            $raw = $p->inventors ?? null;

            if (is_string($raw) && trim($raw) !== '') {
                $inventors = json_decode($raw, true);
                $inventors = is_array($inventors) ? $inventors : [];
            } elseif (is_array($raw)) {
                $inventors = $raw;
            } else {
                $inventors = [];
            }

            if (count($inventors) === 0) {
                $inventors = [[
                    'nama'     => $p->nama_pencipta ?? '-',
                    'status'   => '-',
                    'nip_nim'  => $p->nip_nim ?? '-',
                    'fakultas' => $p->fakultas ?? '-',
                    'no_hp'    => $p->no_hp ?? '-',
                    'email'    => $p->email ?? '-',
                ]];
            }

            return collect($inventors)->map(function ($i, $idx) use ($p) {
                return (object)[
                    'no_pendaftaran' => $p->no_pendaftaran ?? '-',
                    'judul'          => $this->cleanText($p->judul_paten ?? '-'),
                    'jenis'          => $this->cleanText($p->jenis_paten ?? '-'),
                    'inventor_ke'    => $idx + 1,
                    'nama'           => $this->cleanText($i['nama'] ?? '-'),
                    'status'         => $this->cleanText($i['status'] ?? '-'),
                    'nip_nim'        => $this->cleanText($i['nip_nim'] ?? ($i['nip'] ?? ($i['nim'] ?? '-'))),
                    'fakultas'       => $this->cleanText($i['fakultas'] ?? '-'),
                    'no_hp'          => $this->cleanText($i['no_hp'] ?? '-'),
                    'email'          => $this->cleanText($i['email'] ?? '-'),
                ];
            });
        })->values();
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No Pendaftaran',
            'Judul Paten',
            'Jenis Paten',
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
            $row->judul ?? '-',
            $row->jenis ?? '-',
            $row->inventor_ke ?? 1,
            $row->nama ?? '-',
            $row->status ?? '-',
            (string)($row->nip_nim ?? '-'), // ❗ tanpa apostrophe
            $row->fakultas ?? '-',
            $row->no_hp ?? '-',
            $row->email ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 60,
            'C' => 18,
            'D' => 12,
            'E' => 22,
            'F' => 12,
            'G' => 20, // NIP/NIM
            'H' => 28,
            'I' => 16,
            'J' => 26,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B:B')->getAlignment()->setWrapText(true);
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:J1");

                // Kolom G jadi TEXT
                $sheet->getStyle("G2:G{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Paksa value kolom G sebagai STRING (anti scientific notation)
                for ($r = 2; $r <= $highestRow; $r++) {
                    $cell = $sheet->getCell("G{$r}");
                    $val  = (string)$cell->getValue();
                    $sheet->setCellValueExplicit("G{$r}", $val, DataType::TYPE_STRING);
                }
            }
        ];
    }
}
