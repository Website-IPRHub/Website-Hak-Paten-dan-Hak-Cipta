<?php

namespace App\Http\Controllers;

use App\Models\Paten;
use App\Models\PatenVerif;
use App\Models\HakCiptaVerif;
use App\Models\HakCipta;
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
            'nidn_nip'          => ['required', 'regex:/^(\d{8}|\d{18})$/'],
            'fakultas'          => ['required', 'string', 'max:255'],
            'tanggal_pengisian' => ['required', 'date'],
            'download_format'   => ['required', 'in:pdf,docx'],   // <--- baru
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

        // generate DOCX temp
        $out = tempnam(sys_get_temp_dir(), 'tkt_') . '.docx';
        $tp->saveAs($out);

        // ===== DOCX =====
        if ($data['download_format'] === 'docx') {
            return response()
                ->download($out, 'Surat Pernyataan TKT 7-9.docx')
                ->deleteFileAfterSend(true);
        }

        // ===== PDF (convert dari DOCX template via LibreOffice) =====
        $soffice = 'C:\Program Files\LibreOffice\program\soffice.exe';
        if (!file_exists($soffice)) {
            $soffice = 'D:\Program Files (x86)\LibreOffice\program\soffice.exe';
        }
        if (!file_exists($soffice)) {
            abort(500, 'soffice.exe tidak ditemukan. Cek instalasi LibreOffice.');
        }

        $outDir  = dirname($out);
        $pdfPath = preg_replace('/\.docx$/i', '.pdf', $out);

        $cmd = '"' . $soffice . '" --headless --nologo --nofirststartwizard '
            . '--convert-to pdf --outdir "' . $outDir . '" "' . $out . '" 2>&1';

        $output = [];
        $code = 0;
        exec($cmd, $output, $code);

        if ($code !== 0 || !file_exists($pdfPath)) {
            @unlink($out);
            abort(500, "Gagal convert PDF. ExitCode=$code\n" . implode("\n", $output));
        }

        @unlink($out); 

        return response()
            ->download($pdfPath, 'Surat Pernyataan TKT 7-9.pdf')
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
            'file' => ['required', 'file', 'mimes:doc,docx,pdf', 'max:10240'],
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
