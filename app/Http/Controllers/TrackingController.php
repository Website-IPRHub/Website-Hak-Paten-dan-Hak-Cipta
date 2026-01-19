<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakCipta;
use App\Models\Paten;
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

        // cari di dua tabel
        $hakCipta = HakCipta::where('no_pendaftaran', $q)->first();
        $paten    = Paten::where('no_pendaftaran', $q)->first();

        if (!$hakCipta && !$paten) {
            return view('tracking', [
                'q' => $q,
                'found' => false,
            ]);
        }

        // tentukan type + data
        $type = $hakCipta ? 'cipta' : 'paten';
        $data = $hakCipta ?? $paten;

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
            'jenis' => $type === 'cipta' ? 'Hak Cipta' : 'Hak Paten',
            'status' => $status,
            'updatedAt' => $updatedAt,
        ]);
    }
}
