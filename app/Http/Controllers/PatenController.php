<?php

namespace App\Http\Controllers;

use App\Models\Paten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatenController extends Controller
{

    public function start(Request $request)
    {
        // 1) Ambil data dari daftar ENUM kolom fakultas
        $column = DB::selectOne("SHOW COLUMNS FROM paten WHERE Field = 'fakultas'");
        $enumValues = [];

        if ($column && isset($column->Type)) {
            if (preg_match("/^enum\((.*)\)$/", $column->Type, $matches)) {
                $enumValues = str_getcsv($matches[1], ',', "'"); 
            }
        }

        $column2 = DB::selectOne("SHOW COLUMNS FROM paten WHERE Field = 'sumber_dana'");
        $enumValues2 = [];

        if ($column2 && isset($column2->Type)) {
            if (preg_match("/^enum\((.*)\)$/", $column2->Type, $matches)) {
                $enumValues2 = str_getcsv($matches[1], ',', "'"); 
            }
        }

        // 2) Validasi Rule::in($enumValues)
        $data = $request->validate([
            'jenis_paten'       => 'required|in:Paten,Paten Sederhana',
            'judul_paten'       => 'required|string|max:255',
            'nama_pencipta'     => 'required|string|max:255',
            'nip_nim'           => 'required|string|max:255',
            'no_hp'             => 'required|string|max:255',
            'fakultas'          => ['required', Rule::in($enumValues)],
            'email'             => 'required|email|max:255',
            'prototipe'         => 'required|in:Sudah,Belum',
            'nilai_perolehan'   => 'required|string|max:255',
            'sumber_dana'       => ['required', Rule::in($enumValues2)],
            'skema_penelitian'  => 'required|string|max:255',
        ]);

        $data['no_pendaftaran'] = $this->generateNoPendaftaran();
        $data['status'] = $data['status'] ?? 'draft';
        $data['draft_paten'] = $data['draft_paten'] ?? ''; 
        $data['form_permohonan'] = $data['form_permohonan'] ?? '';
        $data['surat_kepemilikan'] = $data['surat_kepemilikan'] ?? '';
        $data['surat_pengalihan'] = $data['surat_pengalihan'] ?? '';
        $data['scan_ktp'] = $data['scan_ktp'] ?? '';
        $data['tanda_terima'] = $data['tanda_terima'] ?? '';
        $data['gambar_prototipe'] = $data['gambar_prototipe'] ?? '';
        $data['deskripsi_singkat_prototipe'] = $data['deskripsi_singkat_prototipe'] ?? '';

        $paten = Paten::create($data);

        session(['paten_id' => $paten->id]);

        return redirect()->route('draftpaten');
    }

    private function generateNoPendaftaran(): string
    {
        $prefix = 'PAT-' . now()->format('Ymd') . '-';

        $last = DB::table('paten')
            ->where('no_pendaftaran', 'like', $prefix . '%')
            ->orderByDesc('no_pendaftaran')
            ->value('no_pendaftaran');

        $nextNumber = 1;
        if ($last) {
            $lastNumber = (int) substr($last, -4);
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
