<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paten;
use App\Models\PatenVerif;

class HakPatenSubmitController extends Controller
{

    public function submit(Request $request, PatenVerif $verif)
    {
        $request->validate([
            'deskripsi' => ['nullable','string','max:255'],
        ]);

        $verif->update([
            'deskripsi_singkat_prototipe' => $request->filled('deskripsi') ? $request->deskripsi : null,
        ]);

        return redirect()->route('patenverif.deskripsi', $verif->id)
            ->with('success', 'Tersimpan');
    }
}
