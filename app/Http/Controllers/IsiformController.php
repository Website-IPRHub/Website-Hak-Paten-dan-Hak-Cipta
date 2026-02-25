<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;


class IsiformController extends Controller
{
    public function store(Request $request)
    {
        $action = $request->input('action', 'download');

        $data = $request->validate([
            'jenis_paten'      => ['required', 'in:Paten,Paten Sederhana'],
            'nomor_permohonan' => ['required', 'string', 'max:100'],
            'judul_invensi'    => ['required', 'string', 'max:255'],
            'pecahan_paten'    => ['required', 'string', 'max:100'],

            'konsultanpaten' => ['required', 'in:Melalui,Tidak Melalui'],
            'nama_badan_hukum' => ['required_if:konsultanpaten,Melalui', 'nullable', 'string', 'max:150'],
            'alamat_badan_hukum' => ['required_if:konsultanpaten,Melalui', 'nullable', 'string', 'max:255'],
            'nama_konsultan_paten' => ['required_if:konsultanpaten,Melalui', 'nullable', 'string', 'max:150'],
            'alamat_konsultan_paten' => ['required_if:konsultanpaten,Melalui', 'nullable', 'string', 'max:255'],
            'nomor_konsultan_paten' => ['required_if:konsultanpaten,Melalui', 'nullable', 'string', 'max:50'],
            'telepon_fax' => ['required_if:konsultanpaten,Melalui', 'nullable', 'string', 'max:50'],

            'jumlah_inventor' => ['required', 'integer', 'min:1', 'max:20'],
            'inventor'        => ['required', 'array'],
            'inventor.*'      => ['required', 'string', 'max:200'],

            'hak_prioritas'   => ['required', 'in:Ya,Tidak'],
            'negara'          => ['required_if:hak_prioritas,Ya', 'nullable', 'string', 'max:120'],
            'tgl_penerimaan'  => ['required_if:hak_prioritas,Ya', 'nullable', 'string', 'max:60'],
            'nomor_prioritas' => ['required_if:hak_prioritas,Ya', 'nullable', 'string', 'max:120'],

            'uraian_halaman'  => ['required', 'integer', 'min:1'],
            'klaim_buah'      => ['required', 'integer', 'min:1'],
            'abstrak_buah'    => ['required', 'integer', 'min:1'],
            'gambar_buah'     => ['required', 'integer', 'min:1'],

            'gambar_dari'     => ['required', 'integer', 'min:1'],
            'gambar_sampai'   => ['required', 'integer', 'gte:gambar_dari'],

            // lampiran checkbox
            'lampiran' => ['nullable', 'array'],
            'lampiran.*' => ['in:surat_kuasa,pengalihan,bukti_pemilikan,do_eo,dok_prioritas,dok_pct,jasad_renik,dok_lain'],
            'lampiran_lainnya' => ['nullable', 'string', 'max:1000'],

            'download_format' => ['required', 'in:pdf,docx'],

        ]);

        // validasi jumlah inventor vs inventor[]
        if (count($data['inventor'] ?? []) !== (int) $data['jumlah_inventor']) {
            return back()->withErrors(['inventor' => 'Jumlah inventor tidak sesuai.'])->withInput();
        }

        // kalau bukan download, balik
        if ($action !== 'download') {
            return back()->with('success', 'OK')->withInput();
        }


        // ===== TEMPLATE PATH =====
        $templatePath = public_path('templates/Form Daftar Paten (2025).docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template DOCX tidak ditemukan: ' . $templatePath);
        }

        $tp = new TemplateProcessor($templatePath);

        // helper coret (unicode overlay)
        $coret = function (string $text): string {
            $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
            return implode('', array_map(fn($c) => $c . "\u{0336}", $chars));
        };

        $val = function ($v): string {
            $s = trim((string)($v ?? ''));
            return $s === '' ? '-' : $s;
        };

        // ===== 1) Jenis paten (coret yang gak dipilih) =====
        $isPaten = ($data['jenis_paten'] ?? '') === 'Paten';
        $tp->setValue('paten_dicoret', $isPaten ? 'Paten' : $coret('Paten'));
        $tp->setValue('paten_sederhana_dicoret', $isPaten ? $coret('Paten Sederhana') : 'Paten Sederhana');

        // ===== 2) Nomor PCT =====
        $tp->setValue('nomor_permohonan', $val($data['nomor_permohonan']));

        // ===== 3) Melalui / Tidak melalui (yang dipilih tampil, yang tidak dipilih dicoret) =====
        $isMelalui = ($data['konsultanpaten'] ?? '') === 'Melalui';

        $tp->setValue('melalui', $isMelalui ? 'melalui' : $coret('melalui'));
        $tp->setValue('tidak_melalui', $isMelalui ? $coret('tidak melalui') : 'tidak melalui');


        // ===== 4) Data konsultan =====
        $tp->setValue('nama_badan_hukum', $isMelalui ? $val($data['nama_badan_hukum']) : '-');
        $tp->setValue('alamat_badan_hukum', $isMelalui ? $val($data['alamat_badan_hukum']) : '-');
        $tp->setValue('nama_konsultan_paten', $isMelalui ? $val($data['nama_konsultan_paten']) : '-');
        $tp->setValue('alamat_konsultan_paten', $isMelalui ? $val($data['alamat_konsultan_paten']) : '-');
        $tp->setValue('nomor_konsultan_paten', $isMelalui ? $val($data['nomor_konsultan_paten']) : '-');
        $tp->setValue('telepon_fax', $isMelalui ? $val($data['telepon_fax']) : '-');

        // ===== 5) Judul & Pecahan =====
        $tp->setValue('judul_invensi', $val($data['judul_invensi']));
        $tp->setValue('pecahan_paten', $val($data['pecahan_paten']));

        // ===== 6) Inventor list =====
        $inventorList = '';
        foreach (($data['inventor'] ?? []) as $i => $inv) {
            $inventorList .= ($i + 1) . '. ' . trim($inv) . "\n";
        }
        $tp->setValue('inventor_input', trim($inventorList) !== '' ? trim($inventorList) : '-');

        // ===== 7) Hak Prioritas =====
        $figsp = "\u{2007}"; 

        $padFixed = function (string $text, int $len) use ($figsp): string {
            $text = trim($text);

            // hitung panjang unicode
            $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
            $n = is_array($chars) ? count($chars) : strlen($text);

            if ($n >= $len) return $text;
            return $text . str_repeat($figsp, $len - $n);
        };

        $hak = ($data['hak_prioritas'] ?? '') === 'Ya';

        // 1) Coret opsi "dengan / tidak dengan"
        $tp->setValue('dengan', $hak ? 'dengan' : $coret('dengan'));
        $tp->setValue('tidak_dengan', $hak ? $coret('tidak dengan') : 'tidak dengan');

        // 2) Isi tabel prioritas
        if ($hak) {
            $tp->setValue('negara', $val($data['negara']));
            $tp->setValue('tgl_penerimaan', $val($data['tgl_penerimaan']));
            $tp->setValue('nomor_prioritas', $val($data['nomor_prioritas']));
        } else {
            // kalau tidak ada hak prioritas, tabel dikosongin (atau strip)
            $tp->setValue('negara', '-');
            $tp->setValue('tgl_penerimaan', '-');
            $tp->setValue('nomor_prioritas', '-');
        }

        // ===== 8) Lampiran invensi =====
        $tp->setValue('uraian_halaman', (string) $data['uraian_halaman']);
        $tp->setValue('klaim_buah', (string) $data['klaim_buah']);
        $tp->setValue('abstrak_buah', (string) $data['abstrak_buah']);
        $tp->setValue('gambar_buah', (string) $data['gambar_buah']);

        // ===== 9) Gambar abstrak =====
        $tp->setValue('gambar_dari', (string) $data['gambar_dari']);
        $tp->setValue('gambar_sampai', (string) $data['gambar_sampai']);

        // ===== 10) Checkbox lampiran (TANPA hack zip) =====
        $boxChecked = ' X ';  
        $nbsp = "\u{00A0}"; 

        $boxEmpty   = str_repeat($nbsp, 5);                 
        $boxChecked = str_repeat($nbsp, 1) . 'X' . str_repeat($nbsp, 1); 

        $selected = $request->input('lampiran', []);
        if (!is_array($selected)) $selected = [];

        $forced = ['pengalihan', 'bukti_pemilikan', 'dok_lain'];

        $isChecked = function (string $key) use ($selected, $forced): bool {
            return in_array($key, $forced, true) || in_array($key, $selected, true);
        };

        $tp->setValue('lamp_surat_kuasa',     $isChecked('surat_kuasa') ? $boxChecked : $boxEmpty);
        $tp->setValue('lamp_pengalihan',      $boxChecked);
        $tp->setValue('lamp_bukti_pemilikan', $boxChecked);
        $tp->setValue('lamp_do_eo',           $isChecked('do_eo') ? $boxChecked : $boxEmpty);
        $tp->setValue('lamp_dok_prioritas',   $isChecked('dok_prioritas') ? $boxChecked : $boxEmpty);
        $tp->setValue('lamp_dok_pct',         $isChecked('dok_pct') ? $boxChecked : $boxEmpty);
        $tp->setValue('lamp_jasad_renik',     $isChecked('jasad_renik') ? $boxChecked : $boxEmpty);
        $tp->setValue('lamp_dok_lain',        $boxChecked);

        // ===== OUTPUT =====
        $out = tempnam(sys_get_temp_dir(), 'paten_') . '.docx';
        $tp->saveAs($out);

        $format = $data['download_format'];

        if ($format === 'docx') {
            return response()
                ->download($out, 'Form Daftar Paten (2025).docx')
                ->deleteFileAfterSend(true);
        }

        // === Convert DOCX -> PDF
        $soffice = 'C:\Program Files\LibreOffice\program\soffice.exe';
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
            ->download($pdfPath, 'Form Daftar Paten (2025).pdf')
            ->deleteFileAfterSend(true);


    }
}