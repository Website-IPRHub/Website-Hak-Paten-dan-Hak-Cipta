<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class PernyataanCiptaController extends Controller
{
    private function val($v): string
    {
        $s = trim((string)($v ?? ''));
        return $s === '' ? '' : $s;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'berupa'   => ['required', 'string', 'max:255'],
            'berjudul'     => ['required', 'string', 'max:255'],
            'tanggal_pengisian' => ['required', 'date'],
        ]);

        // simpan session kalau kamu masih butuh flow "Next"
        session(['hakcipta.form' => $data]);

        // kalau klik tombol Next (sesuaikan route kamu)
        if ($request->input('action') === 'next') {
            return redirect()
                ->route('hakcipta.pengalihanhak') // contoh: sesuaikan step berikutnya
                ->with('success', 'Data tersimpan.');
        }

        $templatePath = public_path('templates/Surat Pernyataan Hak Cipta 2021.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template DOCX tidak ditemukan: ' . $templatePath);
        }

        $tp = new TemplateProcessor($templatePath);


        $tp->setValue('berjudul', $this->val($data['berjudul']));
        $tp->setValue('berupa', $this->val($data['berupa']));
        $tgl = Carbon::parse($data['tanggal_pengisian'])->locale('id');
        $tp->setValue('tanggal_pengisian', $tgl->translatedFormat('d F Y'));

        $out = tempnam(sys_get_temp_dir(), 'cipta_') . '.docx';
    
        $tp->saveAs($out);

        return response()
            ->download($out, 'Surat Pernyataan Hak Cipta 2021.docx')
            ->deleteFileAfterSend(true);
    }
}
