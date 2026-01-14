<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakCipta;

class HakCiptaController extends Controller
{
    public function store(Request $request)
    {
        // VALIDASI DASAR
        $request->validate([
            'jenis_cipta' => 'required|in:Buku,Modul,Program Komputer,Karya Rekaman Video,Lainnya',
            'judul_cipta' => 'required|string',
            'nama_pencipta' => 'required|string',
            'nip_nim' => 'required|string',
            'fakultas' => 'required|string',
            'no_hp' => 'required|string',
            'email' => 'required|email',
            'nilai_perolehan' => 'required|string',
            'sumber_dana' => 'required|string',
        ]);

        // ===============================
        // AUTO GENERATE NO PENDAFTARAN
        // FORMAT: EC00202400001
        // ===============================
        $year = now()->format('Y');

        $last = HakCipta::whereYear('created_at', $year)
            ->whereNotNull('no_pendaftaran')
            ->orderBy('id', 'desc')
            ->first();

        $next = 1;
        if ($last) {
            $next = (int) substr($last->no_pendaftaran, -5) + 1;
        }

        $noPendaftaran = 'EC00' . $year . str_pad($next, 5, '0', STR_PAD_LEFT);

        // ===============================
        // SIMPAN DATA
        // ===============================
        $cipta = new HakCipta();
        $cipta->no_pendaftaran = $noPendaftaran;

        $cipta->jenis_cipta = $request->jenis_cipta;
        $cipta->judul_cipta = $request->judul_cipta;
        $cipta->nama_pencipta = $request->nama_pencipta;
        $cipta->nip_nim = $request->nip_nim;
        $cipta->fakultas = $request->fakultas;
        $cipta->no_hp = $request->no_hp;
        $cipta->email = $request->email;
        $cipta->nilai_perolehan = $request->nilai_perolehan;
        $cipta->sumber_dana = $request->sumber_dana;
        $cipta->skema_penelitian = $request->skema_penelitian;

        // DOKUMEN
        $cipta->surat_permohonan = $request->surat_permohonan;
        $cipta->surat_pernyataan = $request->surat_pernyataan;
        $cipta->surat_pengalihan = $request->surat_pengalihan;
        $cipta->tanda_terima = $request->tanda_terima;
        $cipta->scan_ktp = $request->scan_ktp;
        $cipta->hasil_ciptaan = $request->hasil_ciptaan;
        $cipta->link_ciptaan = $request->link_ciptaan;

        $cipta->save();

        return response()->json([
            'message' => 'Pengajuan hak cipta berhasil',
            'no_pendaftaran' => $noPendaftaran
        ]);
    }
}
