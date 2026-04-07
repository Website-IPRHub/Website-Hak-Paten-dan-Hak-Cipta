<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paten;

class HakPatenSubmit2Controller extends Controller
{

    public function submit(Request $request)
    {
        $data = $request->validate([
            'paten_id' => ['required','integer','exists:paten,id'],
            'deskripsi' => ['nullable','string','max:255'],
        ]);

        $paten = Paten::findOrFail($data['paten_id']);

        $paten->update([
            'deskripsi_singkat_prototipe' => $request->filled('deskripsi') ? $data['deskripsi'] : null,
        ]);

        return redirect()->route('hakpaten.sukses')
            ->with('success', 'Data berhasil disimpan.')
            ->with('no_pendaftaran', $paten->no_pendaftaran ?? null);
    }

}
