<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakCipta;
use App\Models\Paten;
use App\Models\PatenVerif;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        // kalau halaman dibuka tanpa q, tampilkan form saja (biar gak error validate)
        if (!$request->filled('q')) {
            return view('tracking');
        }

        $request->validate([
            'q' => ['required', 'string', 'max:50'],
        ]);

        $q = trim($request->q);

        $hakCipta = null;
        $paten = null;
        $patenVerif = null;

        // routing by prefix (biar gak query tabel yang gak ada)
        if (str_starts_with($q, 'HC')) {
            $hakCipta = HakCipta::where('no_pendaftaran', $q)->first();
        } elseif (str_starts_with($q, 'VP')) {
            $patenVerif = PatenVerif::where('no_pendaftaran', $q)->first();
        } else {
            // fallback kalau format lain: coba semuanya (kalau tabel ada)
            $hakCipta = HakCipta::where('no_pendaftaran', $q)->first();
            // $paten = Paten::where('no_pendaftaran', $q)->first(); // jangan dulu kalau tabel belum ada
            $patenVerif = PatenVerif::where('no_pendaftaran', $q)->first();
        }

        if (!$hakCipta && !$paten && !$patenVerif) {
            return view('tracking', [
                'q' => $q,
                'found' => false,
            ]);
        }

        // tentukan type + data (prioritas: HakCipta > Paten > PatenVerif)
        if ($hakCipta) {
            $type  = 'cipta';
            $data  = $hakCipta;
            $jenis = 'Hak Cipta';
        } elseif ($paten) {
            $type  = 'paten';
            $data  = $paten;
            $jenis = 'Hak Paten';
        } else {
            $type  = 'paten_verif';     // pastikan ini MATCH dengan ref_type di tabel status_verifikasi
            $data  = $patenVerif;
            $jenis = 'Verifikasi Paten';
        }

        // ambil status dari status_verifikasi (sumber utama dari admin)
        $sv = DB::table('status_verifikasi')
            ->where('ref_type', $type)
            ->where('ref_id', $data->id)
            ->first();

        $status    = $sv->status ?? 'terkirim';
        $updatedAt = $sv->updated_at ?? $data->updated_at;

        return view('tracking', [
            'q' => $q,
            'found' => true,
            'jenis' => $jenis,
            'status' => $status,
            'updatedAt' => $updatedAt,
        ]);
    }
}
