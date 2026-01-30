<?php

namespace App\Http\Controllers;

use App\Models\PatenVerif;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PatenInventorExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private Collection $rows;

    public function __construct()
    {
        $data = PatenVerif::orderByDesc('id')->get();
        $this->rows = $this->flatten($data);
    }

    private function flatten(Collection $data): Collection
    {
        return $data->flatMap(function ($p) {

            $raw = $p->inventors ?? null;

            if (is_string($raw)) {
                $inventors = json_decode($raw, true);
                $inventors = is_array($inventors) ? $inventors : [];
            } elseif (is_array($raw)) {
                $inventors = $raw;
            } else {
                $inventors = [];
            }

            if (count($inventors) === 0) {
                $inventors = [[
                    'nama' => $p->nama_pencipta ?? '-',
                    'status' => '-',
                    'nip_nim' => $p->nip_nim ?? '-',
                    'fakultas' => $p->fakultas ?? '-',
                    'no_hp' => $p->no_hp ?? '-',
                    'email' => $p->email ?? '-',
                ]];
            }

            return collect($inventors)->map(function ($i, $idx) use ($p) {
                return [
                    'no_pendaftaran' => $p->no_pendaftaran,
                    'judul_paten'    => $p->judul_paten,
                    'jenis_paten'    => $p->jenis_paten,
                    'inventor_ke'    => $idx + 1,
                    'nama'           => $i['nama'] ?? '-',
                    'status'         => $i['status'] ?? '-',
                    'nip_nim'        => $i['nip_nim'] ?? '-',
                    'fakultas'       => $i['fakultas'] ?? '-',
                    'no_hp'          => $i['no_hp'] ?? '-',
                    'email'          => $i['email'] ?? '-',
                ];
            });
        });
    }

    public function collection(): Collection
    {
        return $this->rows->values()->map(function ($row) {
            return is_object($row) ? (array) $row : $row;
        });
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
        if (is_object($row)) $row = (array) $row;
        return array_values($row);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Freeze header
                $sheet->freezePane('A2');

                // Bold header
                $sheet->getStyle('A1:J1')->getFont()->setBold(true);

                // (opsional) auto filter biar enak cari
                $sheet->setAutoFilter('A1:J1');
            },
        ];
    }
}
