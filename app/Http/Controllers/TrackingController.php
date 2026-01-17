<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakCipta;
use App\Models\Paten;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'max:50'],
        ]);

        $q = trim($request->q);

        $hakCipta = HakCipta::where('no_pendaftaran', $q)->first();

        $paten = Paten::where('no_pendaftaran', $q)->first();

        $data = $hakCipta ?? $paten;

        if (!$data) {
            return view('tracking', [
                'q' => $q,
                'found' => false,
            ]);
        }

        $jenis = $hakCipta ? 'Hak Cipta' : 'Hak Paten';

        return view('tracking', [
            'q' => $q,
            'found' => true,
            'jenis' => $jenis,
            'status' => $data->status,          
            'updatedAt' => $data->updated_at,
            'nama' => $data->nama_pencipta ?? $data->nama_inventor ?? null, 
        ]);
    }
}
