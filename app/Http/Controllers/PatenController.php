<?php

namespace App\Http\Controllers;

use App\Models\Paten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatenController extends Controller
{
    public function start(Request $request)
    {
        $data = $request->validate([
            'jenis_paten'       => 'required|in:Paten,Paten Sederhana',
            'judul_paten'       => 'required|string|max:255',
            'prototipe'         => 'required|in:Sudah,Belum',
            'nilai_perolehan'   => 'required|string|max:255',
            'sumber_dana'       => 'required|string|max:255',
            'skema_penelitian'  => 'required|string|max:255',

            'inventor.nama'     => 'required|array|min:1',
            'inventor.nip_nim'  => 'required|array|min:1',
            'inventor.fakultas' => 'required|array|min:1',
            'inventor.no_hp'    => 'required|array|min:1',
            'inventor.email'    => 'required|array|min:1',
            'inventor.status'   => 'required|array|min:1',
        ]);

        $count = count($data['inventor']['nama']);
        $inventors = [];
        for ($i = 0; $i < $count; $i++) {
            $inventors[] = [
                'nama'     => $data['inventor']['nama'][$i] ?? null,
                'nip_nim'  => $data['inventor']['nip_nim'][$i] ?? null,
                'fakultas' => $data['inventor']['fakultas'][$i] ?? null,
                'no_hp'    => $data['inventor']['no_hp'][$i] ?? null,
                'email'    => $data['inventor']['email'][$i] ?? null,
                'status'   => $data['inventor']['status'][$i] ?? null,
            ];
        }

        $first = $inventors[0] ?? [];

        $paten = Paten::create([
            'no_pendaftaran'   => $this->generateNoPendaftaran(),
            'jenis_paten'      => $data['jenis_paten'],
            'judul_paten'      => $data['judul_paten'],
            'inventors'        => $inventors, // karena casts array, simpan array aja

            'nama_pencipta'    => $first['nama'] ?? null,
            'nip_nim'          => $first['nip_nim'] ?? null,
            'fakultas'         => $first['fakultas'] ?? null,
            'no_hp'            => $first['no_hp'] ?? null,
            'email'            => $first['email'] ?? null,

            'prototipe'        => $data['prototipe'],
            'nilai_perolehan'  => $data['nilai_perolehan'],
            'sumber_dana'      => $data['sumber_dana'],
            'skema_penelitian' => $data['skema_penelitian'],

            // status awal (sesuaikan flow kamu)
            'status'           => 'Terkirim',
        ]);

        session(['paten_id' => $paten->id]);

        $nextRoute = 'draftpaten';
        if ($paten->skema_penelitian === 'Penelitian Pengembangan (TKT 7 - 9)') {
            $nextRoute = 'hakpaten.skema.form';
        }

        return redirect()->route($nextRoute, $paten->id);
    }

    private function storeUploadedOriginalName(Request $request, string $dir): string
    {
        $file = $request->file('file');

        $original = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);

        return $file->storeAs($dir, $safeName, 'public');
    }

    public function draftPaten()
    {
        $paten = Paten::findOrFail(session('paten_id'));

        return view('hakpaten.draftpaten', compact('paten'));
    }


    public function uploadDraft(Request $request)
    {
        $patenId = session('paten_id');
        abort_unless($patenId, 403, 'Session paten_id tidak ada');

        $paten = Paten::findOrFail($patenId);

        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $path = $this->storeUploadedOriginalName($request, 'paten/draft');

        $paten->update(['draft_paten' => $path]);

        // PENTING: jangan redirect ke next step
        return redirect()->back()->with('success', 'Draft berhasil diupload');
    }

    public function submitDeskripsi(Request $request, Paten $paten)
    {
        $request->validate([
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        $paten->update([
            'deskripsi_singkat_prototipe' => $request->filled('deskripsi') ? $request->deskripsi : null,
        ]);

        return redirect()->route('deskripsi', $paten->id)
            ->with('success', 'Tersimpan');
    }

    private function generateNoPendaftaran(): string
    {
        $year   = now()->format('Y');
        $prefix = 'P00' . $year;

        $last = DB::table('paten')
            ->where('no_pendaftaran', 'like', $prefix . '%')
            ->orderByDesc('no_pendaftaran')
            ->value('no_pendaftaran');

        $next = 1;
        if ($last) $next = ((int) substr($last, -5)) + 1;

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function getEnumValues(string $table, string $field): array
    {
        $column = DB::selectOne("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'");
        if (!$column || !isset($column->Type)) return [];

        if (preg_match("/^enum\((.*)\)$/", $column->Type, $matches)) {
            return str_getcsv($matches[1], ',', "'");
        }

        return [];
    }

    public function uploadForm(Request $request, Paten $paten)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
    ]);

    $path = $this->storeUploadedOriginalName($request, 'paten/form_permohonan');

    $paten->update(['form_permohonan' => $path]);

    return redirect()->back()->with('success', 'Form Permohonan berhasil diupload');
}


    public function uploadInvensi(Request $request, Paten $paten)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
    ]);

    $path = $this->storeUploadedOriginalName($request, 'pat$paten/surat_kepemilikan');

    $paten->update(['surat_kepemilikan' => $path]);

    return redirect()->back()->with('success', 'Surat Invensi berhasil diupload');
}

    public function uploadPengalihan(Request $request, Paten $paten)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
    ]);

    $path = $this->storeUploadedOriginalName($request, 'pat$paten/surat_pengalihan');

    $paten->update(['surat_pengalihan' => $path]);

    return redirect()->back()->with('success', 'Surat Pengalihan Hak berhasil diupload');
}


    public function uploadKTP(Request $request, Paten $paten)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
    ]);

    $file = $request->file('file');
    $original = $file->getClientOriginalName();
    $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);

    $path = $file->storeAs('pat$paten/scan_ktp', $safeName, 'public');

    $paten->update(['scan_ktp' => $path]);

    return redirect()->back()->with('success', 'Scan KTP berhasil diupload');
}


    public function uploadGambarr(Request $request, Paten $paten)
{
    $request->validate([
        'file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg', 'max:10240'],
    ]);

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $original = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);

        $path = $file->storeAs('pat$paten/gambar_prototipe', $safeName, 'public');

        $paten->update(['gambar_prototipe' => $path]);
    }

    return redirect()->back()->with('success', 'Gambar berhasil diupload');
}

private function getDraft(Paten $paten, string $step): array
{
    return session("draft.{$paten->id}.{$step}", []);
}

}
