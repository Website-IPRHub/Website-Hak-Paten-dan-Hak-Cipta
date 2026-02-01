<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->filled('q')) {
            return view('tracking');
        }

        $request->validate([
            'q' => ['required', 'string', 'max:50'],
        ]);

        $q = trim($request->q);

        // Cari di tabel verif (sesuai admin)
        $cipta = null;
        $paten = null;

        if (str_starts_with($q, 'HC')) {
            $cipta = DB::table('hak_cipta_verifs')->where('no_pendaftaran', $q)->first();
        } elseif (str_starts_with($q, 'VP')) {
            $paten = DB::table('paten_verifs')->where('no_pendaftaran', $q)->first();
        } else {
            // fallback: coba dua-duanya
            $cipta = DB::table('hak_cipta_verifs')->where('no_pendaftaran', $q)->first();
            $paten = DB::table('paten_verifs')->where('no_pendaftaran', $q)->first();
        }

        if (!$cipta && !$paten) {
            return view('tracking', [
                'q' => $q,
                'found' => false,
            ]);
        }

        // Samain ref_type dengan admin
        if ($cipta) {
            $type  = 'cipta';          // ✅ sama dengan admin
            $data  = $cipta;
            $jenis = 'Hak Cipta';
        } else {
            $type  = 'paten';          // ✅ sama dengan admin
            $data  = $paten;
            $jenis = 'Paten';
        }

        $sv = DB::table('status_verifikasi')
            ->where('ref_type', $type)
            ->where('ref_id', $data->id)
            ->first();

        $status    = $sv->status ?? 'terkirim';
        $updatedAt = $sv->updated_at ?? ($data->updated_at ?? null);

        return view('tracking', [
            'q' => $q,
            'found' => true,
            'jenis' => $jenis,
            'status' => $status,
            'updatedAt' => $updatedAt,
        ]);
    }
}