<?php

namespace App\Http\Controllers;

use App\Models\Paten;
use App\Models\HakCipta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    private function patenIdOrAbort(): int
    {
        $id = session('paten_id');
        if (!$id) abort(400, 'Session paten_id belum ada. Mulai dari step pertama.');
        return (int) $id;
    }

    private function storeFile(Request $request, string $fieldName, string $folder, array $rules): ?string
    {
        $request->validate([$fieldName => $rules]);

        $file = $request->file($fieldName);
        if (!$file) return null;

        $patenId = $this->patenIdOrAbort();
        $noPendaftaran = Paten::where('id', $patenId)->value('no_pendaftaran');
        $originalName = $file->getClientOriginalName();
        $base = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $ext  = $file->getClientOriginalExtension();
        $finalName = $noPendaftaran . '_' . $base . '.' . $ext;

        return $file->storeAs($folder, $finalName, 'public');
    }

    public function draft(Request $request)
{
    
    $request->validate([
        'file' => 'required|mimes:doc,docx|max:10240',
    ]);

    $paten = Paten::findOrFail(session('paten_id'));

    $path = $request->file('file')->store('paten/draft', 'public');

    // ⬇️ INI WAJIB
    $paten->update([
        'draft_paten' => $path,
    ]);

    return redirect()->route('draftpaten')
        ->with('success', 'Draft paten berhasil diupload.');
}



    public function form(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $file = $request->file('file');
        $noPendaftaran = Paten::where('id', $patenId)->value('no_pendaftaran');
        
        $originalName = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $finalName = $safeName;

        $path = $file->storeAs('paten/form', $finalName, 'public');

        Paten::where('id', $patenId)->update([
            'form_permohonan' => $path,
        ]);

       return redirect()
    ->route('kepemilikaninvensi')
    ->with('success', 'Formulir Permohonan berhasil diupload');
    }


    public function suratInvensi(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $file = $request->file('file');

        $noPendaftaran = Paten::where('id', $patenId)->value('no_pendaftaran');
       
        $originalName = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $finalName = $safeName;

        $path = $file->storeAs('paten/surat-kepemilikan', $finalName, 'public');

        Paten::where('id', $patenId)->update([
            'surat_kepemilikan' => $path,
        ]);

        return redirect()
    ->route('pengalihanhak')
    ->with('success', 'Surat pengalihan hak berhasil diupload');
    }

    public function pengalihanhak(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $request->validate([
            'file' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
        ]);

        $file = $request->file('file');
        $noPendaftaran = Paten::where('id', $patenId)->value('no_pendaftaran');

        $originalName = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $finalName = $safeName;


        $path = $file->storeAs('paten/surat-pengalihan', $finalName, 'public');

        Paten::where('id', $patenId)->update([
            'surat_pengalihan' => $path,
        ]);
        

        return redirect()
    ->route('scanktp')
    ->with('success', 'KTP berhasil diupload');
    }

    public function scanKtp(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file');
        $noPendaftaran = Paten::where('id', $patenId)->value('no_pendaftaran');

        $originalName = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $finalName = $safeName;


        $path = $file->storeAs('paten/scan-ktp', $finalName, 'public');

        Paten::where('id', $patenId)->update([
            'scan_ktp' => $path,
        ]);

        return redirect()
    ->route('tandaterima')
    ->with('success', 'Surat tanda terima berhasil diupload');
    }

    public function tandaTerima(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file');

        $noPendaftaran = Paten::where('id', $patenId)->value('no_pendaftaran');

        $originalName = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $finalName = $safeName;


        $path = $file->storeAs('paten/tanda-terima', $finalName, 'public');

        Paten::where('id', $patenId)->update([
            'tanda_terima' => $path,
        ]);

        return redirect()
    ->route('uploadgambarprototipe')
    ->with('success', 'gambar prototipe berhasil diupload');

    }

    public function gambarPrototipe(Request $request)
    {
        $patenId = $this->patenIdOrAbort();

        $request->validate([
            'file' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
        ]);

        if (!$request->hasFile('file')) {
            return redirect()->route('deskripsiproduk')
                ->with('success', 'Lewati upload prototipe');
        }

    
        $path = $this->storeFile($request, 'file', 'paten/gambar-prototipe', [
            'required', 'image', 'mimes:jpg,jpeg,png', 'max:10240'
        ]);

        Paten::where('id', $patenId)->update([
            'gambar_prototipe' => $path,
        ]);

        return redirect()->route('deskripsiproduk')
            ->with('success', 'Gambar prototipe tersimpan');
    }

}
