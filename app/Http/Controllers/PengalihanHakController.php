<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class PengalihanHakController extends Controller
{
    private function pickTemplate(int $jumlah): string
    {
        // Ganti nama file sesuai punyamu
        if ($jumlah >= 1 && $jumlah <= 7)  return public_path('templates/pengalihan hak 1-4.docx');
        // if ($jumlah >= 5 && $jumlah <= 8)  return public_path('templates/pengalihan hak 5-8.docx');
        if ($jumlah >= 8 && $jumlah <= 14) return public_path('templates/pengalihan hak 9-14.docx');

        abort(422, 'Jumlah inventor tidak didukung template.');
    }

    private function val($v): string
    {
        $s = trim((string)($v ?? ''));
        return $s === '' ? '' : $s; // kosongin aja kalau kosong
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jumlah_inventor'   => ['required', 'integer', 'min:1', 'max:20'],
            'judul_paten'     => ['required', 'string', 'max:255'],
            'tanggal_pengisian' => ['required', 'date'],

            'inventor'                    => ['required', 'array'],
            'inventor.nama'               => ['required', 'array'],
            'inventor.nama.*'             => ['required', 'string', 'max:200'],
            'inventor.pekerjaan.*'        => ['required', 'string', 'max:100'],
            'inventor.alamat.*'           => ['required', 'string'],
            'inventor.kode_pos.*'         => ['required', 'string', 'max:20'],
        ]);

        $jumlah = (int) $data['jumlah_inventor'];
        $actual = count($data['inventor']['nama'] ?? []);
        if ($actual !== $jumlah) {
            return back()->withErrors(['inventor' => 'Jumlah inventor tidak sesuai.'])->withInput();
        }

        $templatePath = $this->pickTemplate($jumlah);
        if (!file_exists($templatePath)) {
            abort(500, 'Template DOCX tidak ditemukan: ' . $templatePath);
        }

        $tp = new TemplateProcessor($templatePath);

        // Field umum
        $tp->setValue('judul_paten', $this->val($data['judul_paten']));
        $tgl = Carbon::parse($data['tanggal_pengisian'])->locale('id');
        $tp->setValue('tanggal_pengisian', $tgl->translatedFormat('d F Y'));

        /**
         * === 1) CLONE tabel inventor ===
         * Pastikan di DOCX ada placeholder ${nama_lengkap} di row template tabel.
         */

        // === CLONE IDENTITAS INVENTOR (atas) pakai BLOCK ===
        $tp->cloneBlock('inventor_block', $jumlah, true, true);

        for ($i = 1; $i <= $jumlah; $i++) {
            $idx = $i - 1;

            $tp->setValue("no#{$i}", $i);
            $tp->setValue("nama_lengkap#{$i}", $this->val($data['inventor']['nama'][$idx] ?? ''));
            $tp->setValue("pekerjaan#{$i}", $this->val($data['inventor']['pekerjaan'][$idx] ?? ''));
            $tp->setValue("alamat#{$i}", $this->val($data['inventor']['alamat'][$idx] ?? ''));
            $tp->setValue("kode_pos#{$i}", $this->val($data['inventor']['kode_pos'][$idx] ?? ''));
        }



        // CLONE daftar bawah (bukan tabel, bukan ttd)
        $tp->cloneBlock('list_inventor', $jumlah, true, true);

        for ($i = 1; $i <= $jumlah; $i++) {
            $idx = $i - 1;

            $tp->setValue("no_list#{$i}", $i);
            $tp->setValue("nama_list#{$i}", $this->val($data['inventor']['nama'][$idx] ?? ''));
        }


        $out = tempnam(sys_get_temp_dir(), 'invensi_') . '.docx';
        $tp->saveAs($out);

        return response()->download($out, 'Surat Pernyataan Pengalihan Hak.docx')->deleteFileAfterSend(true);
    }
}
