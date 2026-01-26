<?php

namespace App\Http\Controllers;

use App\Models\PatenVerif;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class SkemaController extends Controller
{
    // GET /paten-verif/{verif}/skema
    public function show(PatenVerif $verif)
    {
        return view('hakpaten.verifikasidokumen.skema.skemapengembangan', compact('verif'));
    }

    // POST /paten-verif/{verif}/skema/download
    public function pengembanganDownload(Request $request, PatenVerif $verif)
    {
        $data = $request->validate([
            'nama_lengkap'      => ['required', 'string', 'max:255'],
            'program_studi'     => ['required', 'string', 'max:255'],
            'judul_paten'       => ['required', 'string', 'max:255'],
            'nidn_nip'          => ['required', 'string', 'max:255'],
            'fakultas'          => ['required', 'string', 'max:255'],
            'tanggal_pengisian' => ['required', 'date'],
        ]);

        $templatePath = public_path('templates/Surat Pernyatan TKT 7-9.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template DOCX tidak ditemukan: ' . $templatePath);
        }

        $tp = new TemplateProcessor($templatePath);

        $tp->setValue('nama_lengkap', $data['nama_lengkap']);
        $tp->setValue('program_studi', $data['program_studi']);
        $tp->setValue('judul_paten', $data['judul_paten']);
        $tp->setValue('nidn_nip', $data['nidn_nip']);
        $tp->setValue('fakultas', $data['fakultas']);

        $tgl = Carbon::parse($data['tanggal_pengisian'])->locale('id')->translatedFormat('d F Y');
        $tp->setValue('tanggal_pengisian', $tgl);

        $out = tempnam(sys_get_temp_dir(), 'tkt_') . '.docx';
        $tp->saveAs($out);

        return response()
            ->download($out, 'Surat Pernyataan TKT 7-9.docx')
            ->deleteFileAfterSend(true);
    }

    public function pengembanganUpload(Request $request, PatenVerif $verif)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'], // 10MB
    ]);

    $file = $request->file('file');

    $no  = $verif->no_pendaftaran ?? ('VP_'.$verif->id);
    $ext = $file->getClientOriginalExtension();

    // nama rapi
    $filename = $no.'_skema_tkt_'.now()->format('Ymd_His').'.'.$ext;

    // simpan ke storage/public/paten-verif/skema/...
    $path = $file->storeAs('paten-verif/skema', $filename, 'public');

    // simpan path ke DB (field kamu sudah ada)
    $verif->update([
        'skema_tkt_template_path' => $path,
    ]);

    $to = $request->input('redirect');

    if ($to) {
        return redirect($to)
            ->with('success', 'File skema berhasil diupload')
            ->withInput(); // ✅ penting
    }

    return redirect()
        ->route('patenverif.skema.form', ['verif' => $verif->id])
        ->with('success', 'File skema berhasil diupload')
        ->withInput(); // ✅ penting

}
}
