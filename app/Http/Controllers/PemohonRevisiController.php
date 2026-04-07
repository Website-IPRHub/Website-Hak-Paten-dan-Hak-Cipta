<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemohonRevisiController extends Controller
{
    public function upload(string $type, int $ref_id, Request $request)
    {
        $pemohon = Auth::guard('pemohon')->user();
        if (!$pemohon) return redirect()->route('pemohon.login.form');

        if (!in_array($type, ['paten','cipta'])) abort(404);

        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'note' => 'nullable|string',
        ]);

        if ($type === 'paten') {
            $row = DB::table('paten_verifs')->where('id', $ref_id)->first();
            if (!$row || $row->no_pendaftaran !== $pemohon->kode_unik) abort(403);

            DB::table('paten_verifs')->where('id',$ref_id)->update(['status' => 'PROSES']);
        } else {
            $row = DB::table('hak_cipta')->where('id', $ref_id)->first();
            if (!$row) abort(403);

            DB::table('hak_cipta')->where('id',$ref_id)->update(['status' => 'PROSES']);
        }

        $path = $request->file('file')->store("revisi/{$type}/pemohon", 'public');

        DB::table('revisiDocs')->insert([
            'type' => $type,
            'ref_id' => $ref_id,
            'from_role' => 'pemohon',
            'note' => $request->note ?? 'Pemohon mengunggah revisi',
            'file_path' => $path,
            'is_read_admin' => false,
            'is_read_pemohon' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Revisi berhasil diupload. Menunggu pengecekan admin.');
    }
}
