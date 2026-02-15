<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormulirMasterController extends Controller
{
    public function store(Request $request)
    {
        // ===============================
        // VALIDASI MASTER
        // ===============================
        $data = $request->validate([
            'judul'             => 'required|string|max:255',
            'jenis_paten'       => 'required|string',
            'nomor_permohonan'  => 'required|string',
            'pecahan_paten'     => 'nullable|string',
            'tanggal_pengisian' => 'required|date',

            'prototipe'        => 'required|string',
            'sumber_dana'      => 'required|string',
            'nilai_perolehan'  => 'required|string',

            'jumlah_inventor'  => 'required|integer|min:1|max:20',

            'inventor.nama'      => 'required|array',
            'inventor.nip_nim'   => 'required|array',
            'inventor.fakultas'  => 'required|array',
            'inventor.no_hp'     => 'required|array',
            'inventor.email'     => 'required|array',
            'inventor.status'    => 'required|array',
            'inventor.nidn'      => 'nullable|array',
        ]);

        // ===============================
        // MAPPING JUDUL (KOMPATIBILITAS)
        // ===============================
        $data['judul_invensi'] = $data['judul'];
        $data['judul_paten']   = $data['judul'];

        // ===============================
        // NORMALISASI DATA INVENTOR
        // ===============================
        $inventors = $this->normalizeInventors($data['inventor']);

        // ===============================
        // ROUTING ACTION
        // ===============================
        $action = $request->input('action');
        $format = $request->input('download_format', 'pdf');

        switch ($action) {
            case 'download_paten':
                return $this->downloadPaten($data, $inventors, $format);

            case 'download_invensi':
                return $this->downloadInvensi($data, $inventors, $format);

            case 'download_pengalihan':
                return $this->downloadPengalihan($data, $inventors, $format);

            default:
                return back()->with('success', 'Data berhasil diproses');
        }
    }

    // ======================================================
    // INVENTOR → FORMAT SERAGAM
    // ======================================================
    private function normalizeInventors(array $inventor)
    {
        $result = [];
        $count = count($inventor['nama']);

        for ($i = 0; $i < $count; $i++) {
            $result[] = [
                'nama'        => $inventor['nama'][$i] ?? '',
                'nip_nim'     => $inventor['nip_nim'][$i] ?? '',
                'fakultas'    => $inventor['fakultas'][$i] ?? '',
                'no_hp'       => $inventor['no_hp'][$i] ?? '',
                'email'       => $inventor['email'][$i] ?? '',
                'status'      => $inventor['status'][$i] ?? '',
                'nidn'        => $inventor['nidn'][$i] ?? null,
            ];
        }

        return $result;
    }

    // ======================================================
    // GENERATOR DOKUMEN
    // ======================================================
    private function downloadPaten($data, $inventors, $format)
    {
        // INVENTOR UNTUK PATEN (VERIF)
        $payload = [
            ...$data,
            'inventor' => $inventors,
        ];

        // contoh:
        // return app(PatenDocumentService::class)->generate($payload, $format);
        return response()->json(['dokumen' => 'PATEN', 'data' => $payload]);
    }

    private function downloadInvensi($data, $inventors, $format)
    {
        // INVENTOR UNTUK INVENSI (LEBIH SIMPLE)
        $payloadInventor = collect($inventors)->map(fn($i) => [
            'nama'  => $i['nama'],
            'email' => $i['email'],
            'no_hp' => $i['no_hp'],
        ])->toArray();

        $payload = [
            ...$data,
            'inventor' => $payloadInventor,
        ];

        return response()->json(['dokumen' => 'INVENSI', 'data' => $payload]);
    }

    private function downloadPengalihan($data, $inventors, $format)
    {
        // INVENTOR UNTUK PENGALIHAN
        $payloadInventor = collect($inventors)->map(fn($i) => [
            'nama'     => $i['nama'],
            'alamat'  => $i['fakultas'], // contoh mapping
            'pekerjaan' => $i['status'],
        ])->toArray();

        $payload = [
            ...$data,
            'inventor' => $payloadInventor,
        ];

        return response()->json(['dokumen' => 'PENGALIHAN', 'data' => $payload]);
    }
}
