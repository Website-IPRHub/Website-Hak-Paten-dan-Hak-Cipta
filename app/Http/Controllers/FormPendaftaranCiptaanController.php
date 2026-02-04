<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class FormPendaftaranCiptaanController extends Controller
{
    private function val($v): string
    {
        $s = trim((string)($v ?? ''));
        return $s === '' ? '' : $s;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jumlah_inventor'   => ['required', 'integer', 'min:1', 'max:20'],
            'judul_ciptaan'     => ['required', 'string', 'max:255'],
            'link_ciptaan'      => ['required', 'url'],
            'tanggal_pengisian' => ['required', 'date'],
            'tempat'            => ['required', 'string', 'max:100'],

            'inventor'             => ['required', 'array'],
            'inventor.nama'        => ['required', 'array'],
            'inventor.nama.*'      => ['required', 'string', 'max:200'],
            'inventor.alamat.*'    => ['required', 'string'],
            'inventor.tlp_rumah.*' => ['nullable', 'string', 'max:50'],
            'inventor.no_hp.*'     => ['required', 'string', 'max:50'],
            'inventor.email.*'     => ['required', 'email', 'max:100'],

            'download_format' => ['required', 'in:pdf,docx'],
        ]);

        $jumlah = (int) $data['jumlah_inventor'];
        $actual = count($data['inventor']['nama'] ?? []);
        if ($actual !== $jumlah) {
            return back()->withErrors(['inventor' => 'Jumlah inventor tidak sesuai.'])->withInput();
        }

        // simpan session kalau kamu masih butuh flow "Next"
        session(['hakcipta.form' => $data]);

        // kalau klik tombol Next (sesuaikan route kamu)
        if ($request->input('action') === 'next') {
            return redirect()
                ->route('hakcipta.suratpernyataan') // contoh: sesuaikan step berikutnya
                ->with('success', 'Data tersimpan.');
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
