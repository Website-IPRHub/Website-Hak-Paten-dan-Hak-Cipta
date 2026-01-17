<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paten;

class HakPatenSubmitController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        $patenId = session('paten_id');
        if (!$patenId) {
            abort(400, 'Session paten_id belum ada. Mulai dari step pertama.');
        }

        $paten = Paten::findOrFail($patenId);

        $paten->update([
            'deskripsi_singkat_prototipe' => $request->filled('deskripsi') ? $request->deskripsi : null,
            'status' => 'terkirim',
        ]);

        session()->forget('paten_id');

        return redirect()
            ->route('hakpaten.sukses') // kalau sukses paten beda halaman
            ->with('no_pendaftaran', $paten->no_pendaftaran); // pastikan kolom ini ada di tabel paten
    }
}
