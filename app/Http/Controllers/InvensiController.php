<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class InvensiController extends Controller
{
    private function pickTemplate(int $jumlah): string
    {
        // Ganti nama file sesuai punyamu
        if ($jumlah >= 1 && $jumlah <= 4)  return public_path('templates/1-4 invensi.docx');
        if ($jumlah >= 5 && $jumlah <= 8)  return public_path('templates/5-8 invensi.docx');
        if ($jumlah >= 9 && $jumlah <= 14) return public_path('templates/9-14 invensi.docx');

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
        'judul_invensi'     => ['required', 'string', 'max:255'],
        'tanggal_pengisian' => ['required', 'date'],

        'inventor'                   => ['required', 'array'],
        'inventor.nama'              => ['required', 'array'],
        'inventor.nama.*'            => ['required', 'string', 'max:200'],
        'inventor.kewarganegaraan.*' => ['required', 'string', 'max:100'],
        'inventor.alamat.*'          => ['required', 'string'],
        'inventor.no_hp.*'           => ['required', 'string', 'max:50'],
        'inventor.email.*'           => ['required', 'email', 'max:100'],
        'inventor.kode_pos.*'        => ['required', 'string', 'max:20'],
    ]);

    $jumlah = (int) $data['jumlah_inventor'];
    $actual = count($data['inventor']['nama'] ?? []);
    if ($actual !== $jumlah) {
        return back()->withErrors(['inventor' => 'Jumlah inventor tidak sesuai.'])->withInput();
    }

    session(['hakpaten.invensi' => $data]);

    // ✅ kalau klik tombol Next
    if ($request->input('action') === 'next') {
        return redirect()
            ->route('hakpaten.pengalihanhakformulir')
            ->with('success', 'Data invensi tersimpan.');
    }

    // ✅ selain itu: download docx
    $templatePath = $this->pickTemplate($jumlah);
    if (!file_exists($templatePath)) {
        abort(500, 'Template DOCX tidak ditemukan: ' . $templatePath);
    }

    $tp = new TemplateProcessor($templatePath);

    $tp->setValue('judul_invensi', $this->val($data['judul_invensi']));
    $tgl = Carbon::parse($data['tanggal_pengisian'])->locale('id');
    $tp->setValue('tanggal_pengisian', $tgl->translatedFormat('d F Y'));

    $tp->cloneRow('nama_lengkap', $jumlah);

    for ($i = 1; $i <= $jumlah; $i++) {
        $idx = $i - 1;
        $tp->setValue("no#{$i}", $i);
        $tp->setValue("nama_lengkap#{$i}", $data['inventor']['nama'][$idx] ?? '');
        $tp->setValue("alamat#{$i}", $data['inventor']['alamat'][$idx] ?? '');
        $tp->setValue("kode_pos#{$i}", $data['inventor']['kode_pos'][$idx] ?? '');
        $tp->setValue("email#{$i}", $data['inventor']['email'][$idx] ?? '');
        $tp->setValue("no_hp#{$i}", $data['inventor']['no_hp'][$idx] ?? '');
        $tp->setValue("kewarganegaraan#{$i}", $data['inventor']['kewarganegaraan'][$idx] ?? '');
    }

    $tp->cloneBlock('list_inventor', $jumlah, true, true);
    for ($i = 1; $i <= $jumlah; $i++) {
        $idx = $i - 1;
        $tp->setValue("no_list#{$i}", $i);
        $tp->setValue("nama_list#{$i}", $this->val($data['inventor']['nama'][$idx] ?? ''));
    }

    $out = tempnam(sys_get_temp_dir(), 'invensi_') . '.docx';
    $tp->saveAs($out);

    return response()
        ->download($out, 'Surat Pernyataan Kepemilikan Invensi oleh Inventor.docx')
        ->deleteFileAfterSend(true);
}

}