<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DuplicateIsiFormController extends Controller
{
    public function store(Request $request)
{
    $action = $request->input('action', 'download');

    // 1. VALIDASI DATA (Sudah benar seperti punyamu)
    $data = $request->validate([
        'jenis_paten' => ['required','in:Paten,Paten Sederhana'],
        'is_pct' => ['required','in:Ya,Tidak'],
        'nomor_permohonan' => ['required_if:is_pct,Ya','nullable','string','max:100'],
        'judul_invensi' => ['required','string','max:255'],
        'is_pecahan' => ['required','in:Ya,Tidak'],
        'pecahan_paten' => ['required_if:is_pecahan,Ya','nullable','string','max:100'],
        'konsultanpaten' => ['required','in:Melalui,Tidak Melalui'],
        // ... validasi lainnya ...
        'jumlah_inventor' => ['required','integer','min:1','max:20'],
        'inventor' => ['required','array'],
        'uraian_halaman'  => ['required', 'integer', 'min:1'],
        'klaim_buah'      => ['required', 'integer', 'min:1'],
        'abstrak_buah'    => ['required', 'integer', 'min:1'],
        'gambar_buah'     => ['required', 'integer', 'min:1'],
        'gambar_dari'     => ['required', 'integer', 'min:1'],
        'gambar_sampai'   => ['required', 'integer', 'gte:gambar_dari'],
        'download_format' => ['required','in:pdf,docx'],
    ]);

    // 2. AMBIL ID DARI REQUEST ATAU SESSION
    $refId = $request->input('ref') ?? session('edit_ref_id');

    // 3. SIMPAN KE SESSION SPESIFIK ID (Agar data tidak hilang pas refresh)
    if ($refId) {
        session()->put("hakpaten.isiform.$refId", $data);
    }
    session()->put('hakpaten.isiform', $data); // Fallback global

    // 4. LOGIC SIMPAN (REPLACE DATABASE)
    if ($action === 'save') {
        if ($refId) {
            // Format ulang array inventor untuk disimpan ke JSON database
            $formattedInventors = [];
            for ($i = 0; $i < (int)$data['jumlah_inventor']; $i++) {
                $formattedInventors[] = [
                    'nama'            => $data['inventor']['nama'][$i] ?? '',
                    'nip_nim'         => $data['inventor']['nip_nim'][$i] ?? '',
                    'alamat'          => $data['inventor']['alamat'][$i] ?? '',
                    'kode_pos'        => $data['inventor']['kode_pos'][$i] ?? '',
                    'fakultas'        => $data['inventor']['fakultas'][$i] ?? '',
                    'no_hp'           => $data['inventor']['no_hp'][$i] ?? '',
                    'email'           => $data['inventor']['email'][$i] ?? '',
                    'status'          => $data['inventor']['status'][$i] ?? '',
                    'nidn'            => $data['inventor']['nidn'][$i] ?? '',
                    'pekerjaan'       => $data['inventor']['pekerjaan'][$i] ?? '',
                    'kewarganegaraan' => $data['inventor']['kewarganegaraan'][$i] ?? '',
                ];
            }

            // Update Database (Replace data lama)
            DB::table('paten_verifs')->where('id', $refId)->update([
                'jenis_paten'    => $data['jenis_paten'],
                'judul_paten'    => $data['judul_invensi'],
                'inventors'      => json_encode($formattedInventors),
                'updated_at'     => now(),
            ]);

            return response()->json(['ok' => true, 'message' => 'Data berhasil disimpan ke database.']);
        }
        return response()->json(['ok' => true, 'message' => 'Data disimpan di session.']);
    }

            if ($action === 'next') {
                return response()->json(['ok' => true]);
            }

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
        $isPaten = $data['jenis_paten'] === 'Paten';
        $tp->setValue('paten_normal', $isPaten ? 'Paten' : $coret('Paten'));
        $tp->setValue('paten_strike', $isPaten ? $coret('Paten Sederhana') : 'Paten Sederhana');

        $isMelalui = $data['konsultanpaten'] === 'Melalui';
        $tp->setValue('konsultan_normal', $isMelalui ? 'melalui' : $coret('melalui'));
        $tp->setValue('konsultan_strike', $isMelalui ? $coret('tidak melalui') : 'tidak melalui');

        $hak = ($data['hak_prioritas'] ?? 'Tidak') === 'Ya';
        $tp->setValue('hak_normal', $hak ? 'dengan' : $coret('dengan'));
        $tp->setValue('hak_strike', $hak ? $coret('tidak dengan') : 'tidak dengan');

        $tp->setValue('negara', $hak ? $val($data['negara'] ?? '') : '-');
        $tp->setValue('nomor_prioritas', $hak ? $val($data['nomor_prioritas'] ?? '') : '-');
        $tp->setValue('tgl_penerimaan', $hak ? $val($data['tgl_penerimaan'] ?? '') : '-');


        // ===== 2) Nomor PCT =====
        $tp->setValue('nomor_permohonan', $val($data['nomor_permohonan']));

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
         // build inventor_input: "1. Nama (WN)" per baris
            $inventorInput = '';
            for ($i=0; $i < (int)$data['jumlah_inventor']; $i++) {
            $nama = trim($data['inventor']['nama'][$i] ?? '');
            $wn   = trim($data['inventor']['kewarganegaraan'][$i] ?? '');
            $inventorInput .= ($i+1).". {$nama} ({$wn})\n";
            }
            $tp->setValue('inventor_input', trim($inventorInput) ?: '-');

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

       // ===== dokumen lain (sebutkan) =====
        $raw = trim((string)($data['lampiran_lainnya'] ?? ''));

        $lampLainList = '-';
        if ($raw !== '') {
            $lines = preg_split("/\r\n|\r|\n/", $raw);

            // bersihin: trim + buang nomor manual user
            $clean = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;

                // buang "1. xxx", "4.xxx", dll
                $line = preg_replace('/^\d+\.\s*/', '', $line);
                $clean[] = $line;
            }

            if (count($clean) > 0) {
                $start  = 4;      // lanjutin setelah 1–3
                $indent = "\t";   // Word lebih rapi pakai tab

                $out = [];
                foreach ($clean as $i => $text) {
                    $out[] = $indent . ($start + $i) . '. ' . $text;
                }

                $lampLainList = implode("\n", $out);
            }
        }

        // PENTING: placeholder harus di paragraf BARU
        $tp->setValue('lampiran_lainnya_list', $lampLainList);



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

        return redirect()->route('patenverif.datadiri'); // halaman yang kamu kirim ini
    }
}