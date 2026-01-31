<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakCipta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HakCiptaController extends Controller
{
    private const TABLE = 'hak_cipta';
    public function start(Request $request)
    {
        $enumFakultas   = $this->getEnumValues('hak_cipta', 'fakultas');
        $enumSumberDana = $this->getEnumValues('hak_cipta', 'sumber_dana');

        $jumlah = (int) $request->input('jumlah_inventor', 1);
        $jumlah = max(1, min(20, $jumlah));

        $validated = $request->validate([
            'jenis_cipta' => 'required|in:Buku,Program Komputer,Karya Rekaman Video,Lainnya',
            'jenis_cipta_lainnya' => 'nullable|string|max:255',

            // FIX: ini harus sesuai form
            'judul_cipta' => 'required|string|max:255',

            'inventor'               => ['required', 'array'],
            'inventor.nama'          => ['required', 'array', "size:$jumlah"],
            'inventor.nip_nim'       => ['required', 'array', "size:$jumlah"],
            'inventor.fakultas'      => ['required', 'array', "size:$jumlah"],
            'inventor.no_hp'         => ['required', 'array', "size:$jumlah"],
            'inventor.email'         => ['required', 'array', "size:$jumlah"],
            'inventor.status'        => ['required', 'array', "size:$jumlah"],

            'inventor.nama.*'        => ['required', 'string', 'max:255'],
            'inventor.nip_nim.*'     => ['required', 'string', 'max:255'],
            'inventor.fakultas.*'    => empty($enumFakultas) ? ['required', 'string'] : ['required', Rule::in($enumFakultas)],
            'inventor.no_hp.*'       => ['required', 'string', 'max:255'],
            'inventor.email.*'       => ['required', 'email', 'max:255'],
            'inventor.status.*'      => ['required', 'in:Dosen,Mahasiswa'],

            'nilai_perolehan' => 'required|string|max:255',
            'sumber_dana' => empty($enumSumberDana) ? 'required|string|max:255' : ['required', Rule::in($enumSumberDana)],
            'skema_penelitian' => 'required|string|max:255',
        ]);

        // inventors aman (anti undefined index)
        $inventors = [];
        for ($i = 0; $i < $jumlah; $i++) {
            $inventors[] = [
                'urut'     => $i + 1,
                'nama'     => trim((string) data_get($validated, "inventor.nama.$i", '')),
                'nip_nim'  => trim((string) data_get($validated, "inventor.nip_nim.$i", '')),
                'fakultas' => trim((string) data_get($validated, "inventor.fakultas.$i", '')),
                'no_hp'    => trim((string) data_get($validated, "inventor.no_hp.$i", '')),
                'email'    => trim((string) data_get($validated, "inventor.email.$i", '')),
                'status'   => trim((string) data_get($validated, "inventor.status.$i", '')),
            ];
        }

        $jenisCipta = $validated['jenis_cipta']; // tetap enum
        $jenisLainnya = $validated['jenis_cipta'] === 'Lainnya'
        ? trim((string) ($validated['jenis_cipta_lainnya'] ?? ''))
        : null;



        $payload = [
            'no_pendaftaran'   => $this->generateNoPendaftaranVerif(),
            'jenis_cipta'      => $jenisCipta,
            'judul_cipta'      => $validated['judul_cipta'],

            'inventors'        => $inventors,

            // mirror inventor pertama ke kolom single
            'nama_pencipta'    => $inventors[0]['nama'] ?? '',
            'nip_nim'          => $inventors[0]['nip_nim'] ?? '',
            'fakultas'         => $inventors[0]['fakultas'] ?? '',
            'no_hp'            => $inventors[0]['no_hp'] ?? '',
            'email'            => $inventors[0]['email'] ?? '',

            'nilai_perolehan'  => $validated['nilai_perolehan'],
            'sumber_dana'      => $validated['sumber_dana'],
            'skema_penelitian' => $validated['skema_penelitian'],

            // PENTING: awalnya Draft (karena upload per-step)
            'status'           => 'Draft',
        ];

        
        // default kolom dokumen (boleh nusll)
        foreach ([
            'surat_permohonan',
            'surat_pernyataan',
            'surat_pengalihan',
            'tanda_terima',
            'scan_ktp',
            'hasil_ciptaan',
            'link_ciptaan',
        ] as $field) {
            $payload[$field] = $payload[$field] ?? null;
        }

        $verif = HakCipta::create($payload);

        // FIX: samain sama middleware
        session(['cipta_id' => $verif->id]);

        return redirect()->route('hakcipta.permohonanpendaftaran');
    }

    // API FLOW (JSON)
    public function store(Request $request)
    {
        $enumFakultas   = $this->getEnumValues(self::TABLE, 'fakultas');
        $enumSumberDana = $this->getEnumValues(self::TABLE, 'sumber_dana');

        $jumlah = (int) $request->input('jumlah_inventor', 1);
        $jumlah = max(1, min(20, $jumlah));

        $validated = $request->validate([
            'jumlah_inventor' => ['required', 'integer', 'min:1', 'max:20'],
            'jenis_cipta'     => ['required', 'in:Buku,Program Komputer,Karya Rekaman Video,Lainnya'],
            'judul_cipta'     => ['required', 'string', 'max:255'],

            'inventor'               => ['required', 'array'],
            'inventor.nama'          => ['required', 'array', "size:$jumlah"],
            'inventor.nip_nim'       => ['required', 'array', "size:$jumlah"],
            'inventor.fakultas'      => ['required', 'array', "size:$jumlah"],
            'inventor.no_hp'         => ['required', 'array', "size:$jumlah"],
            'inventor.email'         => ['required', 'array', "size:$jumlah"],
            'inventor.status'        => ['required', 'array', "size:$jumlah"],

            'inventor.nama.*'        => ['required', 'string', 'max:255'],
            'inventor.nip_nim.*'     => ['required', 'string', 'max:255'],
            'inventor.fakultas.*'    => empty($enumFakultas) ? ['required', 'string'] : ['required', Rule::in($enumFakultas)],
            'inventor.no_hp.*'       => ['required', 'string', 'max:255'],
            'inventor.email.*'       => ['required', 'email', 'max:255'],
            'inventor.status.*'      => ['required', 'in:Dosen,Mahasiswa'],

            'nilai_perolehan'  => ['required', 'string', 'max:255'],
            'sumber_dana'      => empty($enumSumberDana) ? ['required', 'string'] : ['required', Rule::in($enumSumberDana)],
            'skema_penelitian' => ['required', 'string', 'max:255'],
        ]);

        $inventors = [];
        for ($i = 0; $i < $jumlah; $i++) {
            $inventors[] = [
                'urut'     => $i + 1,
                'nama'     => trim((string) data_get($validated, "inventor.nama.$i", '')),
                'nip_nim'  => trim((string) data_get($validated, "inventor.nip_nim.$i", '')),
                'fakultas' => trim((string) data_get($validated, "inventor.fakultas.$i", '')),
                'no_hp'    => trim((string) data_get($validated, "inventor.no_hp.$i", '')),
                'email'    => trim((string) data_get($validated, "inventor.email.$i", '')),
                'status'   => trim((string) data_get($validated, "inventor.status.$i", '')),
            ];
        }

        $payload = [
            'no_pendaftaran'   => $this->generateNoPendaftaranVerif(),
            'jenis_cipta'      => $validated['jenis_cipta'],
            'judul_cipta'      => $validated['judul_cipta'],

            'inventors'        => $inventors,

            'nama_pencipta'    => $inventors[0]['nama'] ?? '',
            'nip_nim'          => $inventors[0]['nip_nim'] ?? '',
            'fakultas'         => $inventors[0]['fakultas'] ?? '',
            'no_hp'            => $inventors[0]['no_hp'] ?? '',
            'email'            => $inventors[0]['email'] ?? '',

            'nilai_perolehan'  => $validated['nilai_perolehan'],
            'sumber_dana'      => $validated['sumber_dana'],
            'skema_penelitian' => $validated['skema_penelitian'],

            'status'           => 'Draft',
        ];

        foreach ([
            'surat_permohonan',
            'surat_pernyataan',
            'surat_pengalihan',
            'tanda_terima',
            'scan_ktp',
            'hasil_ciptaan',
            'link_ciptaan',
        ] as $field) {
            $payload[$field] = $payload[$field] ?? null;
        }

        $verif = HakCipta::create($payload);

        return response()->json([
            'message'        => 'Pengajuan hak cipta berhasil',
            'cipta_id'       => $verif->id,
            'no_pendaftaran' => $verif->no_pendaftaran,
        ]);
    }

    // =========================
    // STEP PAGES (GET)
    // =========================

    public function index()
    {
        return view('hakcipta.datadiricipta');
    }

    public function formpermohonan(HakCipta $verif)
    {
        $draft = $this->getDraft($verif, 'formpermohonan');
        return view('hakcipta.formulirdaftarcipta', compact('verif'));
    }


    public function suratpernyataan(HakCipta $verif)
    {
        $draft = $this->getDraft($verif, 'suratpernyataan');
        return view('hakcipta.suratpernyataanverif', compact('verif', 'draft'));
    }

    public function pengalihanhak(HakCipta $verif)
    {
        $draft = $this->getDraft($verif, 'pengalihanhak');
        return view('hakcipta.pengalihanhak', compact('verif', 'draft'));
    }

    public function scanktp(HakCipta $verif)
    {
        $draft = $this->getDraft($verif, 'scanktp');
        return view('hakcipta.scanktpverif', compact('verif', 'draft'));
    }

    public function hasilciptaan(HakCipta $verif)
    {
        $draft = $this->getDraft($verif, 'hasilciptaan');
        return view('hakcipta.hasilciptaanverif', compact('verif', 'draft'));
    }

    public function linkciptaan(HakCipta $verif)
    {
        $draft = $this->getDraft($verif, 'linkciptaan');
        return view('hakcipta.linkciptaan', compact('verif', 'draft'));
    }
    

    // =========================
    // UPLOADS (POST)
    // =========================

    /**
     * route name: ciptaverif.upload.form (blade kamu pakai ini)
     * kolom DB: surat_permohonan
     */
    public function uploadForm(Request $request, HakCipta $verif)
    {
        return $this->uploadSuratPermohonan($request, $verif);
    }

    public function uploadSuratPermohonan(Request $request, HakCipta $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $path = $this->storeUploadedOriginalName($request, 'verif/surat_permohonan');
        $verif->update(['surat_permohonan' => $path]);

        return back()->with('success', 'Surat Permohonan berhasil diupload');
    }

    public function uploadSuratPernyataan(Request $request, HakCipta $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $path = $this->storeUploadedOriginalName($request, 'verif/surat_pernyataan');
        $verif->update(['surat_pernyataan' => $path]);

        return back()->with('success', 'Surat Pernyataan berhasil diupload');
    }

    public function uploadSuratPengalihan(Request $request, HakCipta $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $path = $this->storeUploadedOriginalName($request, 'verif/surat_pengalihan');
        $verif->update(['surat_pengalihan' => $path]);

        return back()->with('success', 'Surat Pengalihan berhasil diupload');
    }

    public function uploadTandaTerima(Request $request, HakCipta $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $path = $this->storeUploadedOriginalName($request, 'verif/tanda_terima');
        $verif->update(['tanda_terima' => $path]);

        return back()->with('success', 'Tanda Terima berhasil diupload');
    }

    public function uploadKTP(Request $request, HakCipta $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $path = $this->storeUploadedOriginalName($request, 'verif/scan_ktp');
        $verif->update(['scan_ktp' => $path]);

        return back()->with('success', 'Scan KTP berhasil diupload');
    }

    public function uploadHasilCiptaan(Request $request, HakCipta $verif)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $path = $this->storeUploadedOriginalName($request, 'verif/hasil_ciptaan');
        $verif->update(['hasil_ciptaan' => $path]);

        return back()->with('success', 'Hasil Ciptaan berhasil diupload');
    }

    public function saveLinkCiptaan(Request $request, HakCipta $verif)
    {
        $request->validate([
            'link_ciptaan' => ['required', 'url', 'max:255'],
        ]);

        $verif->update(['link_ciptaan' => $request->link_ciptaan]);

        return back()->with('success', 'Link ciptaan tersimpan');
    }

    // =========================
    // FINAL SUBMIT
    // =========================
    public function submitFinal(HakCipta $verif)
    {
        $verif->update(['status' => 'Terkirim']);
        return redirect()->route('ciptaverif.hasil', ['verif' => $verif->id]);
    }

    public function hasilSubmit(HakCipta $verif)
    {
        return view('hakcipta.hasilsubmitverif', compact('verif'));
    }

    // =========================
    // HELPERS
    // =========================

    private function storeUploadedOriginalName(Request $request, string $dir): string
    {
        $file = $request->file('file');

        $original = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);

        return $file->storeAs($dir, $safeName, 'public');
    }

    private function generateNoPendaftaranVerif(): string
    {
        $year   = now()->format('Y');
        $prefix = 'VP0' . $year;

        $last = DB::table(self::TABLE)
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
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) return [];
        if (!preg_match('/^[A-Za-z0-9_]+$/', $field)) return [];

        $tableSql = DB::getQueryGrammar()->wrapTable($table);

        $column = DB::selectOne(
            "SHOW COLUMNS FROM {$tableSql} WHERE Field = ?",
            [$field]
        );

        if (!$column || !isset($column->Type)) return [];

        if (preg_match("/^enum\((.*)\)$/", $column->Type, $matches)) {
            return str_getcsv($matches[1], ',', "'");
        }

        return [];
    }

    private function saveDraft(Request $request, HakCipta $verif, string $step, array $onlyKeys)
    {
        $data = $request->only($onlyKeys);

        foreach ($data as $k => $v) {
            if (is_string($v)) $data[$k] = trim($v);
        }

        session()->put("draft.{$verif->id}.{$step}", array_merge(
            session("draft.{$verif->id}.{$step}", []),
            $data
        ));
    }

    private function getDraft(HakCipta $verif, string $step): array
    {
        return session("draft.{$verif->id}.{$step}", []);
    }
}
