<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DuplicateFormPendaftaranCiptaanController extends Controller
{

  public function index(Request $request)
{
    $ref = $request->query('ref') ?? session('edit_ref_id');
    
    // Samain kayak saku di Paten, tapi buat Hak Cipta
    $data = $ref 
        ? session("hakcipta.form.$ref", session('hakcipta.form', [])) 
        : session('hakcipta.form', []);

    return view('isiform.hakcipta.duplicateformpendaftaranciptaan', [
        'data' => $data,
        'ref'  => $ref
    ]);
}
    private function val($v): string
    {
        $s = trim((string)($v ?? ''));
        return $s === '' ? '' : $s;
    }

    public function store(Request $request)
{
    $action = $request->input('action', 'download');

    // 1. VALIDASI DATA (Gue samain kuncinya ama Blade lo: NIK Gede, Alamat, Kode Pos, dll)
    $data = $request->validate([
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
        'inventor.nama'       => ['required', 'array'],
        'inventor.nik'        => ['required', 'array'], // ✅ Sesuai Blade lo
        'inventor.nip_nim'    => ['required', 'array'],
        'inventor.fakultas'   => ['required', 'array'],
        'inventor.status'     => ['required', 'array'],
        'inventor.no_hp'      => ['required', 'array'],
        'inventor.email'      => ['required', 'array'],
        'inventor.alamat'     => ['required', 'array'],
        'inventor.kode_pos'   => ['required', 'array'],
        
        'inventor.nidn'       => ['nullable', 'array'],
        'inventor.tlp_rumah'  => ['nullable', 'array'],
        'download_format'     => ['nullable', 'in:pdf,docx'],
    ]);

    // 2. AMBIL ID DARI REQUEST ATAU SESSION (Copy logic Paten)
    $refId = $request->input('ref') ?? session('edit_ref_id');

    // 2. SIMPAN KE SESSION (Ini yang bikin NIK & Ulasan nempel pas refresh)
    if ($refId) {
        session()->put("hakcipta.form.$refId", $data);
    }
    session()->put('hakcipta.form', $data);

    // 3. LOGIC SIMPAN DATABASE (IKUTIN GAYA PATEN: Hanya kolom yang ADA)
    if ($action === 'save') {
        if ($refId) {
            $formattedInventors = [];
            for ($i = 0; $i < (int)$data['jumlah_inventor']; $i++) {
                $formattedInventors[] = [
                    'nama' => $data['inventor']['nama'][$i] ?? '',
                    'nik'  => $data['inventor']['nik'][$i] ?? '',
                    // ... sisanya samain ...
                ];
            }

            // ✅ UPDATE DB: Hapus kolom 'berupa' dari sini Tik!
            DB::table('hak_cipta_verifs')->where('id', $refId)->update([
                'judul_cipta' => $data['judul_ciptaan'],
                'jenis_cipta' => ($data['jenis_cipta'] === 'Lainnya') ? $data['jenis_cipta_lainnya'] : $data['jenis_cipta'],
                'inventors'   => json_encode($formattedInventors),
                'updated_at'  => now(),
            ]);

            return response()->json(['ok' => true, 'message' => 'Berhasil simpan revisi!']);
        }
    }

        // kalau klik tombol Next (sesuaikan route kamu)
        if ($request->input('action') === 'next') {
            return response()->json(['ok' => true]);
        }

        $templatePath = public_path('templates/Permohonan Pendaftaran Ciptaan 2021.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template DOCX tidak ditemukan: ' . $templatePath);
        }

        $tp = new TemplateProcessor($templatePath);

        // === header / info umum
        $tp->setValue('judul_ciptaan', $this->val($data['judul_ciptaan']));
        $tp->setValue('link_ciptaan', $this->val($data['link_ciptaan']));

        // tanggal + tempat (di template kamu: ${tempat}, ${tanggal_terbit})
        $tgl = Carbon::parse($data['tanggal_pengisian'])->locale('id')->translatedFormat('d F Y');
        $tp->setValue('tempat', $this->val($data['tempat']));
        $tp->setValue('tanggal_terbit', $tgl);

        // === inventor rows
        // === ambil semua nama, gabung koma
        $names = array_map(
            fn($n) => $this->val($n),
            $data['inventor']['nama'] ?? []
        );
        $names = array_values(array_filter($names, fn($n) => $n !== ''));
        $namaGabung = implode(', ', $names);

        // === ambil data dari inventor 1 (index 0)
        $alamat1 = $this->val($data['inventor']['alamat'][0] ?? '');
        $telp1   = $this->val($data['inventor']['tlp_rumah'][0] ?? ''); // sesuai input: tlp_rumah
        $hp1     = $this->val($data['inventor']['no_hp'][0] ?? '');
        $email1  = $this->val($data['inventor']['email'][0] ?? '');

        // set ke template
        $tp->setValue('nama_lengkap', $namaGabung);
        $tp->setValue('alamat', $alamat1);
        $tp->setValue('tlp_rumah', $telp1);
        $tp->setValue('no_hp', $hp1);
        $tp->setValue('email', $email1);

        // uraian kalau ada
        $tp->setValue('uraian', $this->val($request->input('uraian')));

        $out = tempnam(sys_get_temp_dir(), 'cipta_') . '.docx';
        $tp->saveAs($out);

        $format = $data['download_format'];

        if ($format === 'docx') {
        return response()
                    ->download($out, 'Permohonan Pendaftaran Ciptaan.docx')
                    ->deleteFileAfterSend(true);
        }

        // === Convert DOCX 
        $soffice = 'D:\Program Files\LibreOffice\program\soffice.exe';
        if (!file_exists($soffice)) {
            $soffice = 'C:\Program Files (x86)\LibreOffice\program\soffice.exe';
        }
        if (!file_exists($soffice)) {
            abort(500, 'soffice.exe tidak ditemukan. Cek instalasi LibreOffice.');
        }

        $outDir  = dirname($out);
        $pdfPath = preg_replace('/\.docx$/i', '.pdf', $out);

        // command (quotes penting di Windows)
        $cmd = '"' . $soffice . '" --headless --nologo --nofirststartwizard '
            . '--convert-to pdf --outdir "' . $outDir . '" "' . $out . '" 2>&1';

        $output = [];
        $code = 0;
        exec($cmd, $output, $code);

        if ($code !== 0 || !file_exists($pdfPath)) {
            abort(500, "Gagal convert PDF. ExitCode=$code\n" . implode("\n", $output));
        }

        return response()
            ->download($pdfPath, 'Permohonan Pendaftaran Ciptaan.pdf')
            ->deleteFileAfterSend(true);
    }
}
