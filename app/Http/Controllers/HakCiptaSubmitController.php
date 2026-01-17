<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakCipta;

class HakCiptaSubmitController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'cipta_id' => 'required|integer|exists:hak_cipta,id',
            'link'     => 'nullable|url',
        ]);

        $cipta = HakCipta::findOrFail($request->cipta_id);

        $cipta->update([
            'link_ciptaan' => $request->filled('link') ? $request->link : null,
            'status'       => 'terkirim',
        ]);

        session()->forget('cipta_id');

        return redirect()
            ->route('hakcipta.sukses')
            ->with('no_pendaftaran', $cipta->no_pendaftaran);
    }
}
