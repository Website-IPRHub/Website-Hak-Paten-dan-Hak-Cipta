<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paten;

class HakPatenSubmitController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $patenId = session('paten_id');
        if (!$patenId) {
            abort(400, 'Session paten_id belum ada. Mulai dari step pertama.');
        }

        Paten::where('id', $patenId)->update([
            'deskripsi_singkat_prototipe' => $request->deskripsi,
            'status' => 'terkirim',
        ]);

        session()->forget('paten_id');

        return redirect()->route('hakpaten.sukses');
    }

}
