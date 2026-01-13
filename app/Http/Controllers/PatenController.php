<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paten;

class PatenController extends Controller
{
    public function store(Request $request)
    {
        // VALIDASI DASAR (sesuai migration kamu)
        $request->validate([
            'jenis_paten' => 'required|in:Paten,Paten Sederhana',
            'judul_paten' => 'required|string',
            'nama_pencipta' => 'required|string',
            'nip_nim' => 'required|string',
            'fakultas' => 'required|string',
            'no_hp' => 'required|string',
            'email' => 'required|email',
            'prototipe' => 'required|in:Sudah,Belum',
            'nilai_perolehan' => 'required|string',
            'sumber_dana' => 'required|string',
        ]);

        // ===============================
        // AUTO GENERATE NO PENDAFTARAN
        // ===============================
        $year = now()->format('Y');

        $last = Paten::whereYear('created_at', $year)
            ->whereNotNull('no_pendaftaran')
            ->orderBy('id', 'desc')
            ->first();

        $next = 1;
        if ($last) {
            $next = (int) substr($last->no_pendaftaran, -5) + 1;
        }

        $noPendaftaran = 'P00' . $year . str_pad($next, 5, '0', STR_PAD_LEFT);

        // ===============================
        // SIMPAN DATA
        // ===============================
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

        // DOKUMEN (anggap sudah diupload & path dikirim)
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
}
