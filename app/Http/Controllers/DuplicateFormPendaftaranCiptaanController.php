<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DuplicateFormPendaftaranCiptaanController extends Controller
{
    private function val($v): string
    {
        $s = trim((string)($v ?? ''));
        return $s === '' ? '' : $s;
    }

public function index(Request $request)
{
    $ref = $request->query('ref') ?? session('edit_ref_id');
    $sessionKey = "hakcipta.form.$ref";

    $dbData = DB::table('hak_cipta_verifs')->where('id', $ref)->first();
    if (!$dbData) return "Data tidak ditemukan.";

    // 1. Ambil session cadangan (kalo ada)
    $s1 = session('hakcipta.form', []);
    $s2 = session('hakcipta.verif', []);

    // 2. LOGIKA DEFAULT: Kalo session editan belum ada ATAU isinya kosong melompong
    if (!session()->has($sessionKey) || empty(session($sessionKey)['uraian'])) {
        
        $invDb = json_decode($dbData->inventors, true) ?? [];
        
        $initialData = [
            'jumlah_inventor' => count($invDb) ?: 1,
            'judul_ciptaan'   => $dbData->judul_cipta,
            'link_ciptaan'    => $dbData->link_ciptaan, 
            'jenis_cipta'     => in_array($dbData->jenis_cipta, ['Buku','Program Komputer','Karya Rekaman Video']) 
                                 ? $dbData->jenis_cipta : 'Lainnya',
            'jenis_cipta_lainnya' => (!in_array($dbData->jenis_cipta, ['Buku','Program Komputer','Karya Rekaman Video'])) 
                                 ? $dbData->jenis_cipta : '',
            
            // ✅ FORCE DEFAULT: Kalo session asli gak ada, isi teks ini!
            'uraian'          => $s1['uraian'] ?? $s2['uraian'] ?? 'Produk karya inovatif.', 
            'berupa'          => $s1['berupa'] ?? $s2['berupa'] ?? 'Aplikasi Digital',
            'tempat'          => $s1['tempat'] ?? $s2['tempat'] ?? 'Semarang',
            'tanggal_pengisian' => $s1['tanggal_pengisian'] ?? $s2['tanggal_pengisian'] ?? now()->format('Y-m-d'),
            
            'inventor' => [
                'nama' => array_column($invDb, 'nama'),
                'nik'  => array_column($invDb, 'nik'),
                'nip_nim' => array_column($invDb, 'nip_nim'),
                'fakultas' => array_column($invDb, 'fakultas'),
                'status' => array_column($invDb, 'status'),
                'no_hp' => array_column($invDb, 'no_hp'),
                'email' => array_column($invDb, 'email'),
                'alamat' => array_column($invDb, 'alamat'),
                'kode_pos' => array_column($invDb, 'kode_pos'),
                'tlp_rumah' => array_column($invDb, 'tlp_rumah'),
            ],
        ];

        session()->put($sessionKey, $initialData);
    }

    $data = session($sessionKey);

    return view('isiform.hakcipta.duplicateformpendaftaranciptaan', [
        'data' => $data, 
        'ref'  => $ref
    ]);
}

    public function store(Request $request)
    {
        $action = $request->input('action', 'download');
        $refId = $request->input('ref') ?? session('edit_ref_id');
        $sessionKey = "hakcipta.form.$refId";

        // 1. Validasi (Sama persis dengan file asli agar konsisten)
        $validated = $request->validate([
            'jumlah_inventor'     => ['required', 'integer', 'min:1', 'max:20'],
            'jenis_cipta'         => ['required', 'in:Buku,Program Komputer,Karya Rekaman Video,Lainnya'],
            'jenis_cipta_lainnya' => ['nullable', 'string', 'max:255'],
            'judul_ciptaan'       => ['required', 'string', 'max:255'],
            'link_ciptaan'        => ['required', 'url'],
            'berupa'              => ['required', 'string', 'max:255'],
            'tanggal_pengisian'   => ['required', 'date'],
            'tempat'              => ['required', 'string', 'max:100'],
            'uraian'              => ['required', 'string', 'max:350'],
            'inventor'            => ['required', 'array'],
            'download_format'     => ['nullable', 'in:pdf,docx'],
        ]);

        // 2. Simpan ke Session (Logika array_merge seperti file asli)
        $existingData = session($sessionKey, []);
        $newPayload = array_merge($existingData, $request->all());
        session()->put($sessionKey, $newPayload);

        // 3. Simpan ke Database (AJAX Mode dari SweetAlert)
        if ($action === 'save' && $refId) {
            $formattedInventors = [];
            for ($i = 0; $i < (int)$request->jumlah_inventor; $i++) {
                $formattedInventors[] = [
                    'nama'      => $request->inventor['nama'][$i] ?? '',
                    'nik'       => $request->inventor['nik'][$i] ?? '',
                    'nip_nim'   => $request->inventor['nip_nim'][$i] ?? '',
                    'fakultas'  => $request->inventor['fakultas'][$i] ?? '',
                    'status'    => $request->inventor['status'][$i] ?? '',
                    'no_hp'     => $request->inventor['no_hp'][$i] ?? '',
                    'email'     => $request->inventor['email'][$i] ?? '',
                    'alamat'    => $request->inventor['alamat'][$i] ?? '',
                    'kode_pos'  => $request->inventor['kode_pos'][$i] ?? '',
                    'tlp_rumah' => $request->inventor['tlp_rumah'][$i] ?? '',
                ];
            }

            DB::table('hak_cipta_verifs')->where('id', $refId)->update([
                'judul_cipta'   => $request->judul_ciptaan,
                'jenis_cipta'   => ($request->jenis_cipta === 'Lainnya') ? $request->jenis_cipta_lainnya : $request->jenis_cipta,
                'link_ciptaan'  => $request->link_ciptaan,
                // 'hasil_ciptaan' => $request->uraian, // ⚠️ JANGAN diupdate ke DB kolom ini karena DB kamu isinya Path PDF
                'inventors'     => json_encode($formattedInventors),
                'updated_at'    => now(),
            ]);

            return response()->json(['ok' => true]);
        }

        // 4. Download Logic (Sama dengan file asli)
        return $this->generateDocument($request);
    }

    private function generateDocument($request)
    {
        $templatePath = public_path('templates/Permohonan Pendaftaran Ciptaan 2021.docx');
        $tp = new TemplateProcessor($templatePath);

        $tp->setValue('judul_ciptaan', $this->val($request->judul_ciptaan));
        $tp->setValue('link_ciptaan', $this->val($request->link_ciptaan));
        $tp->setValue('uraian', $this->val($request->uraian));
        $tp->setValue('tempat', $this->val($request->tempat));
        
        $tgl = Carbon::parse($request->tanggal_pengisian)->locale('id')->translatedFormat('d F Y');
        $tp->setValue('tanggal_terbit', $tgl);

        $names = array_map(fn($n) => $this->val($n), $request->inventor['nama'] ?? []);
        $namaGabung = implode(', ', array_filter($names));

        $tp->setValue('nama_lengkap', $namaGabung);
        $tp->setValue('alamat', $this->val($request->inventor['alamat'][0] ?? ''));
        $tp->setValue('tlp_rumah', $this->val($request->inventor['tlp_rumah'][0] ?? ''));
        $tp->setValue('no_hp', $this->val($request->inventor['no_hp'][0] ?? ''));
        $tp->setValue('email', $this->val($request->inventor['email'][0] ?? ''));

        $out = tempnam(sys_get_temp_dir(), 'cipta_') . '.docx';
        $tp->saveAs($out);

        if ($request->download_format === 'docx') {
            return response()->download($out, 'Permohonan Pendaftaran Ciptaan.docx')->deleteFileAfterSend(true);
        }

        // Convert PDF
        $soffice = 'C:\Program Files\LibreOffice\program\soffice.exe';
        if (!file_exists($soffice)) $soffice = 'C:\Program Files (x86)\LibreOffice\program\soffice.exe';
        
        $outDir = dirname($out);
        $pdfPath = preg_replace('/\.docx$/i', '.pdf', $out);
        $cmd = '"' . $soffice . '" --headless --convert-to pdf --outdir "' . $outDir . '" "' . $out . '"';
        exec($cmd);

        return response()->download($pdfPath, 'Permohonan Pendaftaran Ciptaan.pdf')->deleteFileAfterSend(true);
    }
}