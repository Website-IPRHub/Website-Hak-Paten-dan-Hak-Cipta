<?php

namespace App\Http\Controllers;

use App\Models\Paten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatenController extends Controller
{

public function start(Request $request)
{
    $data = $request->validate([
        'jenis_paten'       => 'required|in:Paten,Paten Sederhana',
        'judul_paten'       => 'required|string|max:255',
        'prototipe'         => 'required|in:Sudah,Belum',
        'nilai_perolehan'   => 'required|string|max:255',
        'sumber_dana'       => 'required|string|max:255',
        'skema_penelitian'  => 'required|string|max:255',

        'inventor.nama'     => 'required|array|min:1',
        'inventor.nip_nim'  => 'required|array|min:1',
        'inventor.fakultas' => 'required|array|min:1',
        'inventor.no_hp'    => 'required|array|min:1',
        'inventor.email'    => 'required|array|min:1',
        'inventor.status'   => 'required|array|min:1',
    ]);

    // rapihin inventors jadi array of objects
    $count = count($data['inventor']['nama']);
    $inventors = [];
    for ($i=0; $i<$count; $i++) {
        $inventors[] = [
            'nama'     => $data['inventor']['nama'][$i] ?? null,
            'nip_nim'  => $data['inventor']['nip_nim'][$i] ?? null,
            'fakultas' => $data['inventor']['fakultas'][$i] ?? null,
            'no_hp'    => $data['inventor']['no_hp'][$i] ?? null,
            'email'    => $data['inventor']['email'][$i] ?? null,
            'status'   => $data['inventor']['status'][$i] ?? null,
        ];
    }

    // inventor pertama buat ringkasan
    $first = $inventors[0] ?? [];

    $paten = Paten::create([
        'no_pendaftaran'   => $this->generateNoPendaftaran(), // kalau kamu punya fungsi ini
        'jenis_paten'      => $data['jenis_paten'],
        'judul_paten'      => $data['judul_paten'],
        'inventors'        => json_encode($inventors), // atau kalau kolom json + casts, bisa langsung array
        'nama_pencipta'    => $first['nama'] ?? null,
        'nip_nim'          => $first['nip_nim'] ?? null,
        'fakultas'         => $first['fakultas'] ?? null,
        'no_hp'            => $first['no_hp'] ?? null,
        'email'            => $first['email'] ?? null,
        'prototipe'        => $data['prototipe'],
        'nilai_perolehan'  => $data['nilai_perolehan'],
        'sumber_dana'      => $data['sumber_dana'],
        'skema_penelitian' => $data['skema_penelitian'],
    ]);

    session(['paten_id' => $paten->id]);

    $nextRoute = 'paten.draftpaten';
        if ($paten->skema_penelitian === 'Penelitian Pengembangan (TKT 7 - 9)') {
            $nextRoute ='hakpaten.skema.form';
        }

    // ini wajib ke flow hak-paten
    return redirect()->route($nextRoute, $paten->id);
}


    // API FLOW (JSON)
    public function store(Request $request)
    {
        $request->validate([
            'jenis_paten'     => 'required|in:Paten,Paten Sederhana',
            'judul_paten'     => 'required|string',
            'nama_pencipta'   => 'required|string',
            'nip_nim'         => 'required|string',
            'fakultas'        => 'required|string',
            'no_hp'           => 'required|string',
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
            'prototipe'       => 'required|in:Sudah,Belum',
            'nilai_perolehan' => 'required|string',
            'sumber_dana'     => 'required|string',
        ]);

        $noPendaftaran = $this->generateNoPendaftaran();

        $paten = new Paten();
        $paten->no_pendaftaran = $noPendaftaran;

        $paten->jenis_paten = $request->jenis_paten;
        $paten->judul_paten = $request->judul_paten;
        $paten->nama_pencipta = $request->nama_pencipta;
        $paten->nip_nim = $request->nip_nim;
        $paten->fakultas = $request->fakultas;
        $paten->no_hp = $request->no_hp;
        $paten->email = $request->email;
        $paten->prototipe = $request->prototipe;
        $paten->nilai_perolehan = $request->nilai_perolehan;
        $paten->sumber_dana = $request->sumber_dana;
        $paten->skema_penelitian = $request->skema_penelitian;

        // dokumen path (kalau memang dikirim)
        $paten->draft_paten = $request->draft_paten;
        $paten->form_permohonan = $request->form_permohonan;
        $paten->surat_kepemilikan = $request->surat_kepemilikan;
        $paten->surat_pengalihan = $request->surat_pengalihan;
        $paten->scan_ktp = $request->scan_ktp;
        $paten->tanda_terima = $request->tanda_terima;

        $paten->save();

        return response()->json([
            'message' => 'Pengajuan paten berhasil',
            'no_pendaftaran' => $noPendaftaran
        ]);
    }

    private function generateNoPendaftaran(): string
    {
        // Format: P00 + TAHUN(4) + 5 digit urut per tahun
        // Contoh: P00202600001

        $year   = now()->format('Y');
        $prefix = 'P00' . $year;

        $last = DB::table('paten')
            ->where('no_pendaftaran', 'like', $prefix . '%')
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