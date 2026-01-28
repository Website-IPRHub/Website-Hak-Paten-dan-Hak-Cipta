<?php

namespace App\Http\Controllers;

use App\Models\Paten;
use App\Models\PatenVerif;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class SkemaController extends Controller
{
    // ===== PATEN VERIF =====
    public function showVerif(PatenVerif $verif)
    {
        $draft = session("patenverif.{$verif->id}.skema", []);
        return view('hakpaten.verifikasidokumen.skema.skemapengembangan', compact('verif','draft'));
    }

    public function downloadVerif(Request $request, PatenVerif $verif)
    {
        return $this->downloadDocx($request);
    }

    public function uploadVerif(Request $request, PatenVerif $verif)
    {
        return $this->handleUpload(
            request: $request,
            noPendaftaran: $verif->no_pendaftaran ?? ('VP_'.$verif->id),
            storeDir: 'paten-verif/skema',
            updateModel: fn(string $path) => $verif->update(['skema_tkt_template_path' => $path]),
            fallbackRedirect: route('patenverif.skema.form', ['verif' => $verif->id]),
        );
    }

    // ===== PATEN =====
    public function showPaten(Paten $paten)
    {
        $draft = session("paten.{$paten->id}.skema", []);
        return view('hakpaten.skemapengembanganpaten', compact('paten','draft'));
    }

    public function downloadPaten(Request $request, Paten $paten)
    {
        return $this->downloadDocx($request);
    }

    public function uploadPaten(Request $request, Paten $paten)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
    ]);

    $file = $request->file('file');
    $no  = $paten->no_pendaftaran ?? ('P_'.$paten->id);
    $ext = $file->getClientOriginalExtension();

    $filename = $no.'_skema_tkt_'.now()->format('Ymd_His').'.'.$ext;
    $path = $file->storeAs('hak-paten/skema', $filename, 'public');

    $paten->update([
        'skema_tkt_template_path' => $path,
    ]);

      return redirect()->route('draftpaten');
}


    // ===== Shared helpers =====
    private function downloadDocx(Request $request)
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

        foreach (['nama_lengkap','program_studi','judul_paten','nidn_nip','fakultas'] as $k) {
            $tp->setValue($k, $data[$k]);
        }

        $tgl = Carbon::parse($data['tanggal_pengisian'])->locale('id')->translatedFormat('d F Y');
        $tp->setValue('tanggal_pengisian', $tgl);

        $out = tempnam(sys_get_temp_dir(), 'tkt_') . '.docx';
        $tp->saveAs($out);

        return response()
            ->download($out, 'Surat Pernyataan TKT 7-9.docx')
            ->deleteFileAfterSend(true);
    }

    private function handleUpload(
        Request $request,
        string $noPendaftaran,
        string $storeDir,
        callable $updateModel,
        string $fallbackRedirect,
    ) {
        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $file = $request->file('file');
        $ext  = $file->getClientOriginalExtension();
        $filename = $noPendaftaran.'_skema_tkt_'.now()->format('Ymd_His').'.'.$ext;

        $path = $file->storeAs($storeDir, $filename, 'public');

        $updateModel($path);

        $to = $request->input('redirect');
        $redirectTo = $to ?: $fallbackRedirect;

        return redirect($redirectTo)
            ->with('success', 'File skema berhasil diupload')
            ->withInput();
    }
}
