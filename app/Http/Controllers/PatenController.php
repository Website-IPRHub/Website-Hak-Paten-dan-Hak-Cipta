<?php

namespace App\Http\Controllers;

use App\Models\Paten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatenController extends Controller
{
    // WEB FLOW (redirect)
    public function start(Request $request)
    {
        $enumFakultas   = $this->getEnumValues('paten', 'fakultas');
        $enumSumberDana = $this->getEnumValues('paten', 'sumber_dana');

        $data = $request->validate([
            'jenis_paten'       => 'required|in:Paten,Paten Sederhana',
            'judul_paten'       => 'required|string|max:255',
            'nama_pencipta'     => 'required|string|max:255',
            'nip_nim'           => 'required|string|max:255',
            'no_hp'             => 'required|string|max:255',
            'fakultas'          => ['required', Rule::in($enumFakultas)],
            'email'             => 'required|email|max:255',
            'prototipe'         => 'required|in:Sudah,Belum',
            'nilai_perolehan'   => 'required|string|max:255',
            'sumber_dana'       => ['required', Rule::in($enumSumberDana)],
            'skema_penelitian'  => 'required|string|max:255',
        ]);

        $data['no_pendaftaran'] = $this->generateNoPendaftaran(); // pilih salah satu format
        $data['status'] = $data['status'] ?? 'draft';

        // default dokumen kalau belum ada
        foreach ([
            'draft_paten', 'form_permohonan', 'surat_kepemilikan', 'surat_pengalihan',
            'scan_ktp', 'tanda_terima', 'gambar_prototipe', 'deskripsi_singkat_prototipe'
        ] as $field) {
            $data[$field] = $data[$field] ?? '';
        }

        $paten = Paten::create($data);
        session(['paten_id' => $paten->id]);

        return redirect()->route('draftpaten');
    }

    // API FLOW (JSON)
    public function store(Request $request)
    {
        $request->validate([
            'jenis_paten'     => 'required|in:Paten,Paten Sederhana',
            'judul_paten'     => 'required|string',
            'nama_pencipta'   => 'required|string',
            'nip_nim'         => 'required|string',
            'fakultas'        => 'required|string',
            'no_hp'           => 'required|string',
            'email'           => 'required|email',
            'prototipe'       => 'required|in:Sudah,Belum',
            'nilai_perolehan' => 'required|string',
            'sumber_dana'     => 'required|string',
        ]);

        $noPendaftaran = $this->generateNoPendaftaran();

        $paten = new Paten();
        $paten->no_pendaftaran = $noPendaftaran;

        $paten->jenis_paten = $request->jenis_paten;
        $paten->judul_paten = $request->judul_paten;
        $paten->nama_pencipta = $request->nama_pencipta;
        $paten->nip_nim = $request->nip_nim;
        $paten->fakultas = $request->fakultas;
        $paten->no_hp = $request->no_hp;
        $paten->email = $request->email;
        $paten->prototipe = $request->prototipe;
        $paten->nilai_perolehan = $request->nilai_perolehan;
        $paten->sumber_dana = $request->sumber_dana;
        $paten->skema_penelitian = $request->skema_penelitian;

        // dokumen path (kalau memang dikirim)
        $paten->draft_paten = $request->draft_paten;
        $paten->form_permohonan = $request->form_permohonan;
        $paten->surat_kepemilikan = $request->surat_kepemilikan;
        $paten->surat_pengalihan = $request->surat_pengalihan;
        $paten->scan_ktp = $request->scan_ktp;
        $paten->tanda_terima = $request->tanda_terima;

        $paten->save();

        return response()->json([
            'message' => 'Pengajuan paten berhasil',
            'no_pendaftaran' => $noPendaftaran
        ]);
    }

    private function generateNoPendaftaran(): string
    {
        // Format: P00 + TAHUN(4) + 5 digit urut per tahun
        // Contoh: P00202600001

        $year   = now()->format('Y');
        $prefix = 'P00' . $year;

        $last = DB::table('paten')
            ->where('no_pendaftaran', 'like', $prefix . '%')
            ->orderByDesc('no_pendaftaran')
            ->value('no_pendaftaran');

        $next = 1;
        if ($last) {
            $next = ((int) substr($last, -5)) + 1;
        }

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function getEnumValues(string $table, string $field): array
    {
        $column = DB::selectOne("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'");
        if (!$column || !isset($column->Type)) return [];

        if (preg_match("/^enum\((.*)\)$/", $column->Type, $matches)) {
            return str_getcsv($matches[1], ',', "'");
        }

        return [];
    }
}
