<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakCipta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HakCiptaController extends Controller
{
    public function start(Request $request)
    {
        $enumFakultas   = $this->getEnumValues('hak_cipta', 'fakultas');
        $enumSumberDana = $this->getEnumValues('hak_cipta', 'sumber_dana');

        $data = $request->validate([
            'jenis_hak_cipta' => 'required|in:Buku,Program Komputer,Karya Rekaman Video,Lainnya',
            'jenis_hak_cipta_lainnya' => 'nullable|string|max:255',

            // FIX: ini harus sesuai form
            'judul_hak_cipta' => 'required|string|max:255',

            'nama_pencipta' => 'required|string|max:255',
            'nip_nim' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255',
            'fakultas' => empty($enumFakultas) ? 'required|string|max:255' : ['required', Rule::in($enumFakultas)],
            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    $emails = array_values(array_filter(array_map('trim', explode(';', $value))));

                    if (count($emails) === 0) {
                        return $fail('Email wajib diisi.');
                    }

                    foreach ($emails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            return $fail("Format email tidak valid: $email");
                        }
                    }
                }
            ],

            'nilai_perolehan_hak_cipta' => 'required|string|max:255',
            'sumber_dana' => empty($enumSumberDana) ? 'required|string|max:255' : ['required', Rule::in($enumSumberDana)],
            'skema_penelitian' => 'required|string|max:255',
        ]);

        if ($data['jenis_hak_cipta'] === 'Lainnya' && empty($data['jenis_hak_cipta_lainnya'])) {
            return back()
                ->withErrors(['jenis_hak_cipta_lainnya' => 'Wajib diisi jika memilih "Lainnya".'])
                ->withInput();
        }

        // bikin nomor
        $data['no_pendaftaran'] = $this->generateNoPendaftaran();
        $data['status'] = 'terkirim';

        // default dokumen (sesuaikan kolom yang beneran ada di tabel hak_cipta kamu)
        foreach ([
            'surat_permohonan', 'surat_pernyataan', 'surat_pengalihan',
            'scan_ktp', 'tanda_terima', 'hasil_ciptaan', 'link_ciptaan'
        ] as $field) {
            $data[$field] = $data[$field] ?? '';
        }

        // MAPPING ke kolom DB (model kamu pakai judul_cipta, jenis_cipta, nilai_perolehan)
        $data['judul_cipta'] = $data['judul_hak_cipta'];
        $data['jenis_cipta'] = $data['jenis_hak_cipta'];
        $data['nilai_perolehan'] = $data['nilai_perolehan_hak_cipta'];

        unset(
            $data['judul_hak_cipta'],
            $data['jenis_hak_cipta'],
            $data['jenis_hak_cipta_lainnya'],
            $data['nilai_perolehan_hak_cipta']
        );

        $cipta = HakCipta::create($data);

        // FIX: samain sama middleware
        session(['cipta_id' => $cipta->id]);

        return redirect()->route('hakcipta.permohonanpendaftaran');
    }

    // API FLOW (JSON)
    public function store(Request $request)
    {
        // VALIDASI DASAR
        $request->validate([
            'jenis_cipta'      => 'required|in:Buku,Modul,Program Komputer,Karya Rekaman Video,Lainnya',
            'judul_cipta'      => 'required|string',
            'nama_pencipta'    => 'required|string',
            'nip_nim'          => 'required|string',
            'fakultas'         => 'required|string',
            'no_hp'            => 'required|string',

            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    $emails = array_values(array_filter(array_map('trim', explode(';', (string)$value))));
                    if (count($emails) === 0) {
                        return $fail('Email wajib diisi.');
                    }
                    foreach ($emails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            return $fail("Format email tidak valid: $email");
                        }
                    }
                }
            ],

            'nilai_perolehan'  => 'required|string',
            'sumber_dana'      => 'required|string',
            'skema_penelitian' => 'nullable|string',

            // dokumen (kalau API kamu kirim path/filename)
            'surat_permohonan' => 'nullable|string',
            'surat_pernyataan' => 'nullable|string',
            'surat_pengalihan' => 'nullable|string',
            'tanda_terima'     => 'nullable|string',
            'scan_ktp'         => 'nullable|string',
            'hasil_ciptaan'    => 'nullable|string',
            'link_ciptaan'     => 'nullable|string',
        ]);

        // AUTO GENERATE NO PENDAFTARAN
        $noPendaftaran = $this->generateNoPendaftaran();

        // SIMPAN DATA
        $cipta = new HakCipta();
        $cipta->no_pendaftaran = $noPendaftaran;

        $cipta->jenis_cipta = $request->jenis_cipta;
        $cipta->judul_cipta = $request->judul_cipta;
        $cipta->nama_pencipta = $request->nama_pencipta;
        $cipta->nip_nim = $request->nip_nim;
        $cipta->fakultas = $request->fakultas;
        $cipta->no_hp = $request->no_hp;
        $cipta->email = $request->email;
        $cipta->nilai_perolehan = $request->nilai_perolehan;
        $cipta->sumber_dana = $request->sumber_dana;
        $cipta->skema_penelitian = $request->skema_penelitian;

        // DOKUMEN
        $cipta->surat_permohonan = $request->surat_permohonan;
        $cipta->surat_pernyataan = $request->surat_pernyataan;
        $cipta->surat_pengalihan = $request->surat_pengalihan;
        $cipta->tanda_terima = $request->tanda_terima;
        $cipta->scan_ktp = $request->scan_ktp;
        $cipta->hasil_ciptaan = $request->hasil_ciptaan;
        $cipta->link_ciptaan = $request->link_ciptaan;

        // default status kalau kolom status ada
        if (empty($cipta->status)) {
            $cipta->status = 'draft';
        }

        $cipta->save();

        return response()->json([
            'message' => 'Pengajuan hak cipta berhasil',
            'id' => $cipta->id,
            'no_pendaftaran' => $cipta->no_pendaftaran,
            'status' => $cipta->status,
        ], 201);
    }

    private function generateNoPendaftaran(): string
    {
        $year   = now()->format('Y');
        $prefix = 'EC00' . $year;

        $last = HakCipta::where('no_pendaftaran', 'like', $prefix . '%')
            ->orderByDesc('no_pendaftaran')
            ->value('no_pendaftaran');

        $next = 1;
        if ($last) {
            $next = ((int) substr($last, -5)) + 1;
        }

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function getEnumValues(string $table, string $field): array
    {
        $column = DB::selectOne("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'");
        if (!$column || !isset($column->Type)) return [];

        if (preg_match("/^enum\((.*)\)$/", $column->Type, $matches)) {
            return str_getcsv($matches[1], ',', "'");
        }

        return [];
    }
}
