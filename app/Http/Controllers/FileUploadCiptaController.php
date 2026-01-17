<?php

namespace App\Http\Controllers;

use App\Models\HakCipta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileUploadCiptaController extends Controller
{
    private function ciptaIdOrAbort(): int
    {
        $id = session('cipta_id');
        if (!$id) abort(400, 'Session cipta_id belum ada. Mulai dari step pertama.');
        return (int) $id;
    }

    private function storeFile(Request $request, string $fieldName, string $folder, array $rules): string
    {
        $request->validate([$fieldName => $rules]);

        $file = $request->file($fieldName);
        if (!$file) abort(422, 'File tidak ditemukan.');

        $ciptaId = $this->ciptaIdOrAbort();
        $noPendaftaran = HakCipta::where('id', $ciptaId)->value('no_pendaftaran');

        $originalName = $file->getClientOriginalName();
        $base = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $ext  = $file->getClientOriginalExtension();
        $finalName = $noPendaftaran . '_' . $base . '.' . $ext;

        return $file->storeAs($folder, $finalName, 'public');
    }

    // STEP 2: Surat Permohonan (simpan ke kolom surat_permohonan)
    public function suratPermohonan(Request $request)
    {
        $ciptaId = $this->ciptaIdOrAbort();

        $path = $this->storeFile($request, 'file', 'hakcipta/surat-permohonan', [
            'required', 'file', 'mimes:doc,docx,pdf', 'max:10240'
        ]);

        HakCipta::where('id', $ciptaId)->update([
            'surat_permohonan' => $path,
        ]);

        return redirect()->route('hakcipta.suratpernyataan')
            ->with('success', 'Surat permohonan tersimpan');
    }

    // STEP 3: Surat Pernyataan
    public function suratPernyataan(Request $request)
    {
        $ciptaId = $this->ciptaIdOrAbort();

        $path = $this->storeFile($request, 'file', 'hakcipta/surat-pernyataan', [
            'required', 'file', 'mimes:doc,docx,pdf', 'max:10240'
        ]);

        HakCipta::where('id', $ciptaId)->update([
            'surat_pernyataan' => $path,
        ]);

        return redirect()->route('hakcipta.pengalihanhak')
            ->with('success', 'Surat pernyataan tersimpan');
    }

    // STEP 4: Surat Pengalihan
    public function suratPengalihan(Request $request)
    {
        $ciptaId = $this->ciptaIdOrAbort();

        $path = $this->storeFile($request, 'file', 'hakcipta/surat-pengalihan', [
            'required', 'file', 'mimes:doc,docx,pdf', 'max:10240'
        ]);

        HakCipta::where('id', $ciptaId)->update([
            'surat_pengalihan' => $path,
        ]);

        return redirect()->route('hakcipta.scanktp')
            ->with('success', 'Surat pengalihan tersimpan');
    }

    // STEP 5: Scan KTP
    public function scanKtp(Request $request)
    {
        $ciptaId = $this->ciptaIdOrAbort();

        $path = $this->storeFile($request, 'file', 'hakcipta/scan-ktp', [
            'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'
        ]);

        HakCipta::where('id', $ciptaId)->update([
            'scan_ktp' => $path,
        ]);

        return redirect()->route('hakcipta.tandaterima')
            ->with('success', 'Scan KTP tersimpan');
    }

    // STEP 6: Tanda Terima
    public function tandaTerima(Request $request)
    {
        $ciptaId = $this->ciptaIdOrAbort();

        $path = $this->storeFile($request, 'file', 'hakcipta/tanda-terima', [
            'required', 'file', 'mimes:pdf', 'max:10240'
        ]);

        HakCipta::where('id', $ciptaId)->update([
            'tanda_terima' => $path,
        ]);

        return redirect()->route('hakcipta.hasilciptaan')
            ->with('success', 'Tanda terima tersimpan');
    }

    // STEP 7: Hasil Ciptaan (silakan sesuaikan mimes & max)
    public function hasilCiptaan(Request $request)
    {
        $ciptaId = $this->ciptaIdOrAbort();

        $path = $this->storeFile($request, 'file', 'hakcipta/hasil-ciptaan', [
            'required', 'file', 'mimes:pdf,jpg,jpeg,png,zip,rar,mp4', 'max:51200'
        ]);

        HakCipta::where('id', $ciptaId)->update([
            'hasil_ciptaan' => $path,
        ]);

        return redirect()->route('hakcipta.linkciptaan')
            ->with('success', 'Hasil ciptaan tersimpan');
    }

    // STEP 8: Link Ciptaan (input URL)
    public function linkCiptaan(Request $request)
    {
        $ciptaId = $this->ciptaIdOrAbort();

        $data = $request->validate([
            'link_ciptaan' => ['required', 'url', 'max:500'],
        ]);

        HakCipta::where('id', $ciptaId)->update([
            'link_ciptaan' => $data['link_ciptaan'],
        ]);

        return redirect()->route('hakcipta.linkciptaan')
            ->with('success', 'Link ciptaan tersimpan');
    }
}
