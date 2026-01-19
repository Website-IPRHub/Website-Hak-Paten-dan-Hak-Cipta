<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakCipta;
use App\Models\Paten;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // kalau halaman baru dibuka, tampilkan view kosong
        if ($q === '') {
            return view('tracking'); // atau kirim found => null
        }

        // validasi hanya kalau ada q
        $request->validate([
            'q' => ['string', 'max:50'],
        ]);

        // cari di Hak Cipta dulu
        $hakCipta = HakCipta::where('no_pendaftaran', $q)->first();
        if ($hakCipta) {
            return view('tracking', [
                'q' => $q,
                'found' => true,
                'jenis' => 'Hak Cipta',
                'status' => $hakCipta->status,
                'updatedAt' => optional($hakCipta->updated_at)->format('d M Y H:i'),
                'nama' => $hakCipta->nama_pencipta,
            ]);
        }

        // kalau ga ketemu, cari di Paten
        $paten = Paten::where('no_pendaftaran', $q)->first();
        if ($paten) {
            return view('tracking', [
                'q' => $q,
                'found' => true,
                'jenis' => 'Hak Paten',
                'status' => $paten->status,
                'updatedAt' => optional($paten->updated_at)->format('d M Y H:i'),
                'nama' => $paten->nama_inventor ?? null,
            ]);
        }

        // kalau ga ketemu dua-duanya
        return view('tracking', [
            'q' => $q,
            'found' => false,
        ]);
    }
}
