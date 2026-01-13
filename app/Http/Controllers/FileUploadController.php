<?php

namespace App\Http\Controllers;

use App\Models\Paten;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    private function patenIdOrAbort(): int
    {
        $id = session('paten_id');
        if (!$id) abort(400, 'Session paten_id belum ada. Mulai dari step pertama.');
        return (int) $id;
    }

    private function storeFile(Request $request, string $fieldName, string $folder, array $rules)
    {
        $request->validate([
            $fieldName => $rules,
        ]);

        $file = $request->file($fieldName);

        if (!$file) {
            return null;
        }

        return $file->store($folder, 'public');
    }


    public function draft(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $path = $this->storeFile($request, 'file', 'paten/draft', [
            'required', 'file', 'mimes:pdf,doc,docx', 'max:10240'
        ]);

        Paten::where('id', $patenId)->update(['draft_paten' => $path]);

        return redirect()->route('formulirpermohonan')->with('success', 'Draft paten tersimpan');
    }

    public function form(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $path = $this->storeFile($request, 'file', 'paten/form', [
            'required', 'file', 'mimes:pdf,doc,docx', 'max:10240'
        ]);

        Paten::where('id', $patenId)->update(['form_permohonan' => $path]);

        return redirect()->route('kepemilikaninvensi')->with('success', 'Form permohonan tersimpan');
    }

    public function suratInvensi(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $path = $this->storeFile($request, 'file', 'paten/surat-kepemilikan', [
            'required', 'file', 'mimes:pdf,doc,docx', 'max:10240'
        ]);

        Paten::where('id', $patenId)->update(['surat_kepemilikan' => $path]);

        return redirect()->route('pengalihanhak')->with('success', 'Surat kepemilikan tersimpan');
    }

    public function suratPengalihan(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $path = $this->storeFile($request, 'file', 'paten/surat-pengalihan', [
            'required', 'file', 'mimes:pdf,doc,docx', 'max:10240'
        ]);

        Paten::where('id', $patenId)->update(['surat_pengalihan' => $path]);

        return redirect()->route('scanktp')->with('success', 'Surat pengalihan tersimpan');
    }

    public function scanKtp(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $path = $this->storeFile($request, 'file', 'paten/scan-ktp', [
            'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'
        ]);

        Paten::where('id', $patenId)->update(['scan_ktp' => $path]);

        return redirect()->route('tandaterima')->with('success', 'Scan KTP tersimpan');
    }

    public function tandaTerima(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $path = $this->storeFile($request, 'file', 'paten/tanda-terima', [
            'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'
        ]);

        Paten::where('id', $patenId)->update(['tanda_terima' => $path]);

        return redirect()->route('uploadgambarprototipe')->with('success', 'Tanda terima tersimpan');
    }

    public function gambarPrototipe(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $path = $this->storeFile($request, 'file', 'paten/gambar-prototipe', [
            'nullable', 'image', 'max:5120'
        ]);

        if ($path !== null) {
            Paten::where('id', $patenId)->update(['gambar_prototipe' => $path]);
        }

        return redirect()->route('deskripsiproduk')
            ->with('success', $path ? 'Gambar prototipe tersimpan' : 'Lewati upload prototipe');
    }
}
