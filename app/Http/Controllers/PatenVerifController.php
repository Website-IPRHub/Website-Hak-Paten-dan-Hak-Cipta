<?php

namespace App\Http\Controllers;

use App\Models\PatenVerif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class PatenVerifController extends Controller
{
    // =========================
    // WEB FLOW (redirect + session) - SAMA SEPERTI PATENCONTROLLER
    // =========================
    public function start(Request $request)
    {
        $enumFakultas   = $this->getEnumValues('paten_verifs', 'fakultas');
        $enumSumberDana = $this->getEnumValues('paten_verifs', 'sumber_dana');

        $jumlah = (int) $request->input('jumlah_inventor', 1);
        $jumlah = max(1, min(20, $jumlah));

        $validated = $request->validate([
            'jumlah_inventor'  => ['required', 'integer', 'min:1', 'max:20'],
            'jenis_paten'      => ['required', 'in:Paten,Paten Sederhana'],
            'judul_paten'      => ['required', 'string', 'max:255'],

            'inventor'               => ['required', 'array'],
            'inventor.nama'          => ['required', 'array', "size:$jumlah"],
            'inventor.nip_nim'       => ['required', 'array', "size:$jumlah"],
            'inventor.fakultas'      => ['required', 'array', "size:$jumlah"],
            'inventor.no_hp'         => ['required', 'array', "size:$jumlah"],
            'inventor.email'         => ['required', 'array', "size:$jumlah"],
            'inventor.status'        => ['required', 'array', "size:$jumlah"],

            'inventor.nama.*'        => ['required', 'string', 'max:255'],
            'inventor.nip_nim.*'     => ['required', 'string', 'max:255'],
            'inventor.fakultas.*'    => empty($enumFakultas) ? ['required','string'] : ['required', Rule::in($enumFakultas)],
            'inventor.no_hp.*'       => ['required', 'string', 'max:255'],
            'inventor.email.*'       => ['required', 'email', 'max:255'],
            'inventor.status.*'      => ['required', 'in:Dosen,Mahasiswa'],

            'prototipe'        => ['required', 'in:Sudah,Belum'],
            'nilai_perolehan'  => ['required', 'string', 'max:255'],
            'sumber_dana'      => empty($enumSumberDana) ? ['required','string'] : ['required', Rule::in($enumSumberDana)],
            'skema_penelitian' => ['required', 'string', 'max:255'],
        ]);

        // build inventors JSON
        $inventors = [];
        for ($i = 0; $i < $jumlah; $i++) {
            $inventors[] = [
                'urut'     => $i + 1,
                'nama'     => trim((string) $validated['inventor']['nama'][$i]),
                'nip_nim'  => trim((string) $validated['inventor']['nip_nim'][$i]),
                'fakultas' => trim((string) $validated['inventor']['fakultas'][$i]),
                'no_hp'    => trim((string) $validated['inventor']['no_hp'][$i]),
                'email'    => trim((string) $validated['inventor']['email'][$i]),
                'status'   => trim((string) $validated['inventor']['status'][$i]),
            ];
        }

        $fakultas = $inventors[0]['fakultas'] ?? null;

        $payload = [
            'no_pendaftaran'   => $this->generateNoPendaftaranVerif(),
            'jenis_paten'      => $validated['jenis_paten'],
            'judul_paten'      => $validated['judul_paten'],

            'inventors'        => $inventors,

            // kalau kolom single ini memang ada di tabel, ini oke
            'nama_pencipta'    => $inventors[0]['nama'] ?? '',
            'nip_nim'          => $inventors[0]['nip_nim'] ?? '',
            'fakultas'         => $fakultas,
            'no_hp'            => $inventors[0]['no_hp'] ?? '',
            'email'            => $inventors[0]['email'] ?? '',

            'prototipe'        => $validated['prototipe'],
            'nilai_perolehan'  => $validated['nilai_perolehan'],
            'sumber_dana'      => $validated['sumber_dana'],
            'skema_penelitian' => $validated['skema_penelitian'],

            'status_verif'     => 'Menunggu',
        ];

        // default dokumen kalau belum ada (biar sama seperti controller sebelumnya)
        foreach ([
            'draft_paten',
            'form_permohonan',
            'surat_kepemilikan',
            'surat_pengalihan',
            'scan_ktp',
            'tanda_terima',
            'gambar_prototipe',
            'deskripsi_singkat_prototipe',
        ] as $field) {
            $payload[$field] = $payload[$field] ?? '';
        }

        $verif = PatenVerif::create($payload);

        // SIMPAN SESSION (sama persis seperti PatenController)
        session(['verif_id' => $verif->id]);

        // tentukan next step (redirect, bukan json)
        $nextRoute = 'patenverif.draft';
        if ($verif->skema_penelitian === 'Penelitian Pengembangan (TKT 7 - 9)') {
            $nextRoute = 'patenverif.skema.form';
        }

        return redirect()->route($nextRoute, $verif->id);
    }

    // =========================
    // API FLOW (JSON) - kalau kamu butuh versi API
    // =========================
    public function store(Request $request)
    {
        $enumFakultas   = $this->getEnumValues('paten_verifs', 'fakultas');
        $enumSumberDana = $this->getEnumValues('paten_verifs', 'sumber_dana');

        $jumlah = (int) $request->input('jumlah_inventor', 1);
        $jumlah = max(1, min(20, $jumlah));

        $validated = $request->validate([
            'jumlah_inventor'  => ['required', 'integer', 'min:1', 'max:20'],
            'jenis_paten'      => ['required', 'in:Paten,Paten Sederhana'],
            'judul_paten'      => ['required', 'string', 'max:255'],

            'inventor'               => ['required', 'array'],
            'inventor.nama'          => ['required', 'array', "size:$jumlah"],
            'inventor.nip_nim'       => ['required', 'array', "size:$jumlah"],
            'inventor.fakultas'      => ['required', 'array', "size:$jumlah"],
            'inventor.no_hp'         => ['required', 'array', "size:$jumlah"],
            'inventor.email'         => ['required', 'array', "size:$jumlah"],
            'inventor.status'        => ['required', 'array', "size:$jumlah"],

            'inventor.nama.*'        => ['required', 'string', 'max:255'],
            'inventor.nip_nim.*'     => ['required', 'string', 'max:255'],
            'inventor.fakultas.*'    => empty($enumFakultas) ? ['required','string'] : ['required', Rule::in($enumFakultas)],
            'inventor.no_hp.*'       => ['required', 'string', 'max:255'],
            'inventor.email.*'       => ['required', 'email', 'max:255'],
            'inventor.status.*'      => ['required', 'in:Dosen,Mahasiswa'],

            'prototipe'        => ['required', 'in:Sudah,Belum'],
            'nilai_perolehan'  => ['required', 'string', 'max:255'],
            'sumber_dana'      => empty($enumSumberDana) ? ['required','string'] : ['required', Rule::in($enumSumberDana)],
            'skema_penelitian' => ['required', 'string', 'max:255'],
        ]);

        $inventors = [];
        for ($i = 0; $i < $jumlah; $i++) {
            $inventors[] = [
                'urut'     => $i + 1,
                'nama'     => trim((string) $validated['inventor']['nama'][$i]),
                'nip_nim'  => trim((string) $validated['inventor']['nip_nim'][$i]),
                'fakultas' => trim((string) $validated['inventor']['fakultas'][$i]),
                'no_hp'    => trim((string) $validated['inventor']['no_hp'][$i]),
                'email'    => trim((string) $validated['inventor']['email'][$i]),
                'status'   => trim((string) $validated['inventor']['status'][$i]),
            ];
        }

        $payload = [
            'no_pendaftaran'   => $this->generateNoPendaftaranVerif(),
            'jenis_paten'      => $validated['jenis_paten'],
            'judul_paten'      => $validated['judul_paten'],
            'inventors'        => $inventors,

            'nama_pencipta'    => $inventors[0]['nama'] ?? '',
            'nip_nim'          => $inventors[0]['nip_nim'] ?? '',
            'fakultas'         => $inventors[0]['fakultas'] ?? '',
            'no_hp'            => $inventors[0]['no_hp'] ?? '',
            'email'            => $inventors[0]['email'] ?? '',

            'prototipe'        => $validated['prototipe'],
            'nilai_perolehan'  => $validated['nilai_perolehan'],
            'sumber_dana'      => $validated['sumber_dana'],
            'skema_penelitian' => $validated['skema_penelitian'],
            'status_verif'     => 'Menunggu',
        ];

        foreach ([
            'draft_paten',
            'form_permohonan',
            'surat_kepemilikan',
            'surat_pengalihan',
            'scan_ktp',
            'tanda_terima',
            'gambar_prototipe',
            'deskripsi_singkat_prototipe',
        ] as $field) {
            $payload[$field] = $payload[$field] ?? '';
        }

        $verif = PatenVerif::create($payload);

        return response()->json([
            'message'       => 'Pengajuan verifikasi paten berhasil',
            'verif_id'      => $verif->id,
            'no_pendaftaran'=> $verif->no_pendaftaran,
        ]);
    }

    public function uploadDraft(Request $request, PatenVerif $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:5120'],
        ]);

        $path = $request->file('file')->store('verif/draft', 'public');

        $verif->update([
            'draft_paten' => $path,
        ]);

        return redirect()->route('patenverif.formpermohonan', $verif->id);
    }

    public function submitDeskripsi(Request $request, PatenVerif $verif)
    {
        $request->validate([
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        $verif->update([
            'deskripsi_singkat_prototipe' => $request->filled('deskripsi') ? $request->deskripsi : null,
        ]);

        return redirect()->route('patenverif.deskripsi', $verif->id)
            ->with('success', 'Tersimpan');
    }

    private function generateNoPendaftaranVerif(): string
    {
        $year   = now()->format('Y');
        $prefix = 'VP0' . $year;

        $last = DB::table('paten_verifs')
            ->where('no_pendaftaran', 'like', $prefix . '%')
            ->orderByDesc('no_pendaftaran')
            ->value('no_pendaftaran');

        $next = 1;
        if ($last) {
            $next = ((int) substr($last, -5)) + 1;
        }

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

    // views
    // views
public function draft(PatenVerif $verif){
    $draft = $this->getDraft($verif, 'draft');
    return view('hakpaten.verifikasidokumen.draftpatenverif', compact('verif','draft'));
}

public function formpermohonan(PatenVerif $verif){
    $draft = $this->getDraft($verif, 'formpermohonan');
    return view('hakpaten.verifikasidokumen.formpermohonanverif', compact('verif','draft'));
}

public function invensi(PatenVerif $verif){
    $draft = $this->getDraft($verif, 'invensi');
    return view('hakpaten.verifikasidokumen.invensiverif', compact('verif','draft'));
}

public function pengalihanhak(PatenVerif $verif){
    $draft = $this->getDraft($verif, 'pengalihanhak');
    return view('hakpaten.verifikasidokumen.pengalihanhakverif', compact('verif','draft'));
}

public function scanktp(PatenVerif $verif){
    $draft = $this->getDraft($verif, 'scanktp');
    return view('hakpaten.verifikasidokumen.scanktpverif', compact('verif','draft'));
}

public function uploadgambar(PatenVerif $verif){
    $draft = $this->getDraft($verif, 'uploadgambar');
    return view('hakpaten.verifikasidokumen.uploadgambarverif', compact('verif','draft'));
}

public function deskripsiprodukverif(PatenVerif $verif){
    $draft = $this->getDraft($verif, 'deskripsi');
    return view('hakpaten.verifikasidokumen.deskripsiprodukverif', compact('verif','draft'));
}

    public function uploadForm(Request $request, PatenVerif $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'], // 10MB
        ]);

        $path = $request->file('file')->store('verif/form_permohonan', 'public');

        $verif->update([
            'form_permohonan' => $path,
        ]);

        return redirect()->route('patenverif.invensi', ['verif' => $verif->id])
            ->with('success', 'Form Permohonan berhasil diupload');
    }

    public function uploadInvensi(Request $request, PatenVerif $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'], // 10MB
        ]);

        $path = $request->file('file')->store('verif/surat_kepemilikan', 'public');

        $verif->update([
            'surat_kepemilikan' => $path,
        ]);

        return redirect()->route('patenverif.pengalihanhak', ['verif' => $verif->id])
            ->with('success', 'Surat Invensi berhasil diupload');
    }

    public function uploadPengalihan(Request $request, PatenVerif $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'], // 10MB
        ]);

        $path = $request->file('file')->store('verif/surat_pengalihan', 'public');

        $verif->update([
            'surat_pengalihan' => $path,
        ]);

        return redirect()->route('patenverif.scanktp', ['verif' => $verif->id])
            ->with('success', 'Surat Pengalihan Hak berhasil diupload');
    }

    public function uploadKTP(Request $request, PatenVerif $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $path = $request->file('file')->store('verif/scan_ktp', 'public');

        $verif->update([
            'scan_ktp' => $path,
        ]);

        return redirect()->route('patenverif.uploadgambar', ['verif' => $verif->id])
            ->with('success', 'Scan KTP berhasil diupload');
    }


    public function uploadGambarr(Request $request, PatenVerif $verif)
    {
        $request->validate([
            'file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg', 'max:10240'],
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('verif/gambar_prototipe', 'public');

            $verif->update([
                'gambar_prototipe' => $path,
            ]);
        }

        return redirect()->route('patenverif.deskripsi', ['verif' => $verif->id])
            ->with('success', 'Gambar berhasil diupload');
    }

    // =========================
    // SUBMIT FINAL
    // =========================
    public function submitFinal(PatenVerif $verif)
    {
        // update status + waktu submit
        $verif->update([
            'status_verif' => 'Diajukan',
            'submitted_at' => now(), // kalau ada kolomnya
        ]);

        return redirect()->route('patenverif.hasil', [
            'verif' => $verif->id
        ]);
    }

    // =========================
    // HALAMAN HASIL SUBMIT
    // =========================
    public function hasilSubmit(PatenVerif $verif)
    {
        return view('hakpaten.verifikasidokumen.hasilsubmit', compact('verif'));
    }

    private function saveDraft(Request $request, PatenVerif $verif, string $step, array $onlyKeys)
{
    $data = $request->only($onlyKeys);

    // bersihin spasi biar rapi (opsional)
    foreach ($data as $k => $v) {
        if (is_string($v)) $data[$k] = trim($v);
    }

    session()->put("draft.{$verif->id}.{$step}", array_merge(
        session("draft.{$verif->id}.{$step}", []),
        $data
    ));
}

private function getDraft(PatenVerif $verif, string $step): array
{
    return session("draft.{$verif->id}.{$step}", []);
}



}
