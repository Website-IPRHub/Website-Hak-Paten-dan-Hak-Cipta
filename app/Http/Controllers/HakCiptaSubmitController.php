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
            'link'     => 'required|string|max:255', // atau 'url' kalau harus URL beneran
        ]);

        $ciptaId = $request->cipta_id; // jangan pakai session

        HakCipta::where('id', $ciptaId)->update([
            'link_ciptaan' => $request->link, // pastikan kolom ini ada
            'status'       => 'terkirim',
        ]);

        // optional: kalau masih mau bersihin session
        session()->forget('cipta_id');

        return redirect()->route('hakcipta.sukses'); // pastikan route ini ada
    }
}
