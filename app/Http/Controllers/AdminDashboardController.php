<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paten;
use App\Models\HakCipta;
use App\Models\VerifikasiDokumen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\StatusUpdateMail;

use App\Mail\DiterimaMail;
use App\Mail\RevisiMail;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }

        $name = $request->session()->get('admin_name', 'Admin');
        $tab  = $request->query('tab', 'stats');

        // statistik
        $totalPaten = Paten::count();
        $totalCipta = HakCipta::count();
        $totalAll   = $totalPaten + $totalCipta;

        $patenJenis = Paten::select('jenis_paten', DB::raw('count(*) as total'))
            ->groupBy('jenis_paten')
            ->pluck('total', 'jenis_paten')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $ciptaJenis = HakCipta::select('jenis_cipta', DB::raw('count(*) as total'))
            ->groupBy('jenis_cipta')
            ->pluck('total', 'jenis_cipta')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $dataPaten  = collect();
        $dataCipta  = collect();
        $dataStatus = collect();

        // keys dokumen per tipe (dipakai inject docs)
        $keysByType = [
            'paten' => ['draft_paten','form_permohonan','surat_kepemilikan','surat_pengalihan','scan_ktp','tanda_terima','gambar_prototipe'],
            'cipta' => ['surat_permohonan','surat_pernyataan','surat_pengalihan','tanda_terima','scan_ktp','hasil_ciptaan'],
        ];

        if ($tab === 'paten') {
            $dataPaten = Paten::orderBy('id', 'desc')->get();

            // ✅ FIX: inject docs juga untuk tab paten (biar blade $row->docs aman)
            $dataPaten = $this->attachDocsToRows($dataPaten, 'paten', $keysByType['paten']);
        }

        if ($tab === 'cipta') {
            $dataCipta = HakCipta::orderBy('id', 'desc')->get();

            // ✅ FIX: inject docs juga untuk tab cipta (biar blade $row->docs aman)
            $dataCipta = $this->attachDocsToRows($dataCipta, 'cipta', $keysByType['cipta']);
        }

        // =========================
        // TAB STATUS (GABUNG + JOIN status_verifikasi + ambil dokumen)
        // =========================
        if ($tab === 'status') {

            // ---- PATEN
            $paten = Paten::query()
                ->select([
                    'paten.id',
                    'paten.no_pendaftaran',
                    'paten.judul_paten as judul',
                    'paten.jenis_paten as jenis',
                    'paten.email',

                    'paten.draft_paten',
                    'paten.form_permohonan',
                    'paten.surat_kepemilikan',
                    'paten.surat_pengalihan',
                    'paten.scan_ktp',
                    'paten.tanda_terima',
                    'paten.gambar_prototipe',

                    DB::raw("'paten' as type"),
                    'sv.status',
                    'sv.sertifikat_path',
                    'sv.emailed_at',
                ])
                ->leftJoin('status_verifikasi as sv', function ($join) {
                    $join->on('sv.ref_id', '=', 'paten.id')
                        ->where('sv.ref_type', '=', 'paten');
                })
                ->orderBy('paten.id', 'asc')
                ->get()
                ->map(function ($r) {
                    $r->status = $r->status ?? 'terkirim';
                    return $r;
                });

            // ---- CIPTA
            $cipta = HakCipta::query()
                ->select([
                    'hak_cipta.id',
                    'hak_cipta.no_pendaftaran',
                    'hak_cipta.judul_cipta as judul',
                    'hak_cipta.jenis_cipta as jenis',
                    'hak_cipta.jenis_lainnya',
                    'hak_cipta.email',

                    'hak_cipta.surat_permohonan',
                    'hak_cipta.surat_pernyataan',
                    'hak_cipta.surat_pengalihan',
                    'hak_cipta.tanda_terima',
                    'hak_cipta.scan_ktp',
                    'hak_cipta.hasil_ciptaan',

                    DB::raw("'cipta' as type"),
                    'sv.status',
                    'sv.sertifikat_path',
                    'sv.emailed_at',
                ])
                ->leftJoin('status_verifikasi as sv', function ($join) {
                    $join->on('sv.ref_id', '=', 'hak_cipta.id')
                        ->where('sv.ref_type', '=', 'cipta');
                })
                ->orderBy('hak_cipta.id', 'asc')
                ->get()
                ->map(function ($r) {
                    if (strtolower((string)$r->jenis) === 'lainnya') {
                        $r->jenis = $r->jenis_lainnya ?: 'Lainnya';
                    }
                    $r->status = $r->status ?? 'terkirim';
                    return $r;
                });

            $dataStatus = $paten->concat($cipta)->values();

            // ✅ inject docs per row (tetap seperti punyamu, tapi dibuat efisien & aman)
            $dataStatus = $dataStatus->map(function ($r) use ($keysByType) {
                $docKeys = $keysByType[$r->type] ?? [];

                $existing = VerifikasiDokumen::where([
                    'ref_type' => $r->type,
                    'ref_id'   => $r->id,
                ])->get()->keyBy('doc_key');

                $docsArr = [];
                foreach ($docKeys as $k) {
                    $docsArr[$k] = $existing[$k] ?? (object)[
                        'doc_key' => $k,
                        'status'  => 'pending',
                        'note'    => null,
                        'admin_attachment_path' => null,
                    ];
                }

                $r->setAttribute('docs', $docsArr);
                return $r;
            });
        }

        return view('admin.dashboard', compact(
            'name',
            'tab',
            'dataPaten',
            'dataCipta',
            'dataStatus',
            'totalAll',
            'totalPaten',
            'totalCipta',
            'patenJenis',
            'ciptaJenis'
        ));
    }

    /**
     * ✅ FIX helper: inject $row->docs untuk tab paten & tab cipta juga
     * Tanpa mengubah struktur blade kamu.
     */
    private function attachDocsToRows(Collection $rows, string $type, array $docKeys): Collection
    {
        if ($rows->count() === 0) return $rows;

        $ids = $rows->pluck('id')->filter()->values()->all();
        if (count($ids) === 0) return $rows;

        $allDocs = VerifikasiDokumen::where('ref_type', $type)
            ->whereIn('ref_id', $ids)
            ->get()
            ->groupBy('ref_id');

        return $rows->map(function ($r) use ($allDocs, $docKeys) {
            $existing = collect($allDocs->get($r->id, collect()))->keyBy('doc_key');

            $docsArr = [];
            foreach ($docKeys as $k) {
                $docsArr[$k] = $existing->get($k) ?? (object)[
                    'doc_key' => $k,
                    'status'  => 'pending',
                    'note'    => null,
                    'admin_attachment_path' => null,
                ];
            }

            $r->setAttribute('docs', $docsArr);
            return $r;
        });
    }

    // ========= helper email list =========
    private function parseEmails(?string $raw): array
    {
        if (!$raw) return [];
        $parts = preg_split('/[,\s;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $emails = [];
        foreach ($parts as $e) {
            $e = strtolower(trim($e));
            if (filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
        }
        return array_values(array_unique($emails));
    }

// ========= STATUS VERIFIKASI (tabel status_verifikasi) =========
    public function updateStatusVerifikasi(Request $request, string $type, int $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }
        if (!in_array($type, ['paten', 'cipta'])) abort(404);

        $request->validate([
            'status' => 'required|in:terkirim,proses,revisi,diterima,ditolak',
        ]);

        $newStatus = $request->input('status');

        // ambil status lama (biar gak spam kirim email kalau status sama)
        $old = DB::table('status_verifikasi')->where(['ref_type' => $type, 'ref_id' => $id])->first();
        $oldStatus = $old->status ?? null;

        DB::table('status_verifikasi')->updateOrInsert(
            ['ref_type' => $type, 'ref_id' => $id],
            ['status' => $newStatus, 'updated_at' => now(), 'created_at' => now()]
        );

        // kalau diterima tapi belum ada sertifikat
        if ($newStatus === 'diterima') {
            $sv = DB::table('status_verifikasi')->where(['ref_type' => $type, 'ref_id' => $id])->first();
            if ($sv && empty($sv->sertifikat_path)) {
                return redirect()->route('admin.dashboard', ['tab' => 'status'])
                    ->with('success', 'Status diterima. Silakan upload sertifikat DJKI untuk mengirim email otomatis.');
            }
        }

        // ✅ kirim email untuk status: terkirim, proses, ditolak
        // (hanya kalau status berubah)
        $autoMailStatuses = ['terkirim', 'proses', 'ditolak'];

        if (in_array($newStatus, $autoMailStatuses, true) && $newStatus !== $oldStatus) {
            try {
                if ($type === 'paten') {
                    $row = Paten::findOrFail($id);
                    $kategori = 'PATEN';
                    $judul = $row->judul_paten ?? '-';
                } else {
                    $row = HakCipta::findOrFail($id);
                    $kategori = 'HAK CIPTA';
                    $judul = $row->judul_cipta ?? '-';
                }

                $no = $row->no_pendaftaran ?? '-';
                $emails = $this->parseEmails($row->email);

                if (count($emails) === 0) {
                    return redirect()->route('admin.dashboard', ['tab' => 'status'])
                        ->with('success', 'Status tersimpan, tapi email pemohon kosong/tidak valid.');
                }

                foreach ($emails as $to) {
                    Mail::to($to)->send(new StatusUpdateMail($kategori, $judul, $no, $newStatus));
                }

                return redirect()->route('admin.dashboard', ['tab' => 'status'])
                    ->with('success', 'Status berhasil diupdate & email notifikasi terkirim ke: '.implode(', ', $emails));

            } catch (\Throwable $e) {
                Log::error('Gagal kirim email status update', [
                    'ref_type'=>$type,'ref_id'=>$id,'status'=>$newStatus,'err'=>$e->getMessage()
                ]);

                return redirect()->route('admin.dashboard', ['tab' => 'status'])
                    ->with('success', 'Status berhasil diupdate, tapi email gagal dikirim. Cek storage/logs/laravel.log');
            }
        }

        // NOTE: logic "revisi kirim email" yang kamu punya, biarkan tetap jalan (kalau kamu mau)
        // Kalau kamu sudah taruh blok kirim RevisiMail di bawah ini, biarkan.

        return redirect()->route('admin.dashboard', ['tab' => 'status'])
            ->with('success', 'Status berhasil diupdate.');
    }

    // ========= upload sertifikat DJKI + kirim email diterima =========
    public function uploadSertifikatVerifikasi(Request $request, string $type, int $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }
        if (!in_array($type, ['paten', 'cipta'])) abort(404);

        $request->validate([
            'sertifikat' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('sertifikat')->store('sertifikat_djki', 'public');

        DB::table('status_verifikasi')->updateOrInsert(
            ['ref_type' => $type, 'ref_id' => $id],
            ['sertifikat_path' => $path, 'updated_at' => now(), 'created_at' => now()]
        );

        $sv = DB::table('status_verifikasi')->where(['ref_type' => $type, 'ref_id' => $id])->first();

        if (!$sv || $sv->status !== 'diterima') {
            return redirect()->route('admin.dashboard', ['tab' => 'status'])
                ->with('success', 'Sertifikat berhasil diupload. Email akan dikirim otomatis setelah status = diterima.');
        }

        if (!empty($sv->emailed_at)) {
            return redirect()->route('admin.dashboard', ['tab' => 'status'])
                ->with('success', 'Sertifikat diupload. (Email sudah pernah dikirim, gunakan tombol Kirim Ulang Email jika perlu).');
        }

        if ($type === 'paten') {
            $row = Paten::findOrFail($id);
            $kategori = 'PATEN';
            $judul = $row->judul_paten ?? '-';
        } else {
            $row = HakCipta::findOrFail($id);
            $kategori = 'HAK CIPTA';
            $judul = $row->judul_cipta ?? '-';
        }

        $no = $row->no_pendaftaran ?? '-';
        $emails = $this->parseEmails($row->email);

        if (count($emails) === 0) {
            return redirect()->route('admin.dashboard', ['tab' => 'status'])
                ->with('success', 'Sertifikat diupload, tapi email pemohon kosong/tidak valid.');
        }

        $fullPath = storage_path('app/public/' . $path);

        try {
            foreach ($emails as $to) {
                Mail::to($to)->send(new DiterimaMail($kategori, $judul, $no, $fullPath, basename($path)));
            }

            DB::table('status_verifikasi')
                ->where(['ref_type' => $type, 'ref_id' => $id])
                ->update(['emailed_at' => Carbon::now(), 'updated_at' => now()]);

            return redirect()->route('admin.dashboard', ['tab' => 'status'])
                ->with('success', 'Sertifikat diupload & email otomatis berhasil dikirim.');
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email diterima', ['err' => $e->getMessage()]);
            return redirect()->route('admin.dashboard', ['tab' => 'status'])
                ->with('success', 'Gagal mengirim email. Cek storage/logs/laravel.log');
        }
    }

    // ========= resend email sertifikat =========
    public function resendEmail(Request $request, string $type, int $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }
        if (!in_array($type, ['paten', 'cipta'])) abort(404);

        $sv = DB::table('status_verifikasi')->where(['ref_type' => $type, 'ref_id' => $id])->first();
        if (!$sv || empty($sv->sertifikat_path)) {
            return redirect()->route('admin.dashboard', ['tab'=>'status'])
                ->with('success', 'Tidak bisa kirim ulang: sertifikat belum ada.');
        }

        if ($type === 'paten') {
            $row = Paten::findOrFail($id);
            $kategori = 'PATEN';
            $judul = $row->judul_paten ?? '-';
        } else {
            $row = HakCipta::findOrFail($id);
            $kategori = 'HAK CIPTA';
            $judul = $row->judul_cipta ?? '-';
        }

        $emails = $this->parseEmails($row->email);
        if (count($emails) === 0) {
            return redirect()->route('admin.dashboard', ['tab'=>'status'])
                ->with('success', 'Email pemohon kosong/tidak valid.');
        }

        $fullPath = storage_path('app/public/' . $sv->sertifikat_path);

        try {
            foreach ($emails as $to) {
                Mail::to($to)->send(new DiterimaMail($kategori, $judul, $row->no_pendaftaran ?? '-', $fullPath, basename($sv->sertifikat_path)));
            }

            DB::table('status_verifikasi')->where(['ref_type'=>$type,'ref_id'=>$id])
                ->update(['emailed_at'=>Carbon::now(),'updated_at'=>now()]);

            return redirect()->route('admin.dashboard', ['tab'=>'status'])
                ->with('success', 'Email berhasil dikirim ulang.');
        } catch (\Throwable $e) {
            Log::error('Gagal resend email', ['err' => $e->getMessage()]);
            return redirect()->route('admin.dashboard', ['tab'=>'status'])
                ->with('success', 'Gagal kirim ulang email. Cek storage/logs/laravel.log');
        }
        return $this->resendEmail($request, $type, $id);
    }

    // ========= verifikasi dokumen per-file (OK / REVISI) =========
    public function setVerifikasiDokumen(Request $request, string $type, int $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }
        if (!in_array($type, ['paten', 'cipta'])) abort(404);

        $request->validate([
            'doc_key' => 'required|string|max:100',
            'action'  => 'required|in:ok,revisi',
            'note'    => 'nullable|string',
            'admin_attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $docKey = $request->input('doc_key');
        $action = $request->input('action');

        $data = [
            'status' => $action === 'ok' ? 'ok' : 'revisi',
            'updated_at' => now(),
        ];

        if ($action === 'revisi') {
            if (!trim((string)$request->input('note'))) {
                // ✅ FIX: balik ke halaman asal (tab paten/cipta/status)
                return redirect()->back()->with('success', 'Catatan revisi wajib diisi.');
            }
            $data['note'] = $request->input('note');
            $data['requested_at'] = now();
        } else {
            $data['note'] = null;
            $data['requested_at'] = null;
            $data['admin_attachment_path'] = null;
        }

        if ($request->hasFile('admin_attachment')) {
            $path = $request->file('admin_attachment')->store('admin_revisi', 'public');
            $data['admin_attachment_path'] = $path;
        }

        VerifikasiDokumen::updateOrCreate(
            ['ref_type' => $type, 'ref_id' => $id, 'doc_key' => $docKey],
            $data + ['created_at' => now()]
        );

        // kalau ada minimal 1 dokumen revisi => status_verifikasi jadi revisi
        $hasRevisi = VerifikasiDokumen::where(['ref_type'=>$type,'ref_id'=>$id])
            ->where('status','revisi')->exists();

        if ($hasRevisi) {
            DB::table('status_verifikasi')->updateOrInsert(
                ['ref_type'=>$type,'ref_id'=>$id],
                ['status'=>'revisi','updated_at'=>now(),'created_at'=>now()]
            );
        }

        // ✅ FIX: balik ke halaman asal (tab paten/cipta/status)
        return redirect()->back()->with('success', 'Verifikasi dokumen berhasil disimpan.');
    }

    // ========= kirim email revisi (sekali kirim list dokumen revisi) =========
    public function sendRevisiEmail(Request $request, string $type, int $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }
        if (!in_array($type, ['paten', 'cipta'])) abort(404);

        if ($type === 'paten') {
            $row = Paten::findOrFail($id);
            $kategori = 'PATEN';
            $judul = $row->judul_paten ?? '-';
        } else {
            $row = HakCipta::findOrFail($id);
            $kategori = 'HAK CIPTA';
            $judul = $row->judul_cipta ?? '-';
        }

        $no = $row->no_pendaftaran ?? '-';
        $emails = $this->parseEmails($row->email);

        if (count($emails) === 0) {
            return redirect()->back()->with('success', 'Gagal kirim revisi: email pemohon kosong/tidak valid.');
        }

        $docs = VerifikasiDokumen::where(['ref_type'=>$type,'ref_id'=>$id])
            ->where('status','revisi')
            ->orderBy('doc_key')
            ->get();

        if ($docs->count() === 0) {
            return redirect()->back()->with('success', 'Tidak ada dokumen revisi untuk dikirim.');
        }

        $labels = [
            // paten
            'draft_paten' => 'Draft Paten',
            'form_permohonan' => 'Form Permohonan',
            'surat_kepemilikan' => 'Surat Kepemilikan',
            'surat_pengalihan' => 'Surat Pengalihan',
            'scan_ktp' => 'Scan KTP',
            'tanda_terima' => 'Tanda Terima',
            'gambar_prototipe' => 'Gambar Prototipe',

            // cipta
            'surat_permohonan' => 'Surat Permohonan',
            'surat_pernyataan' => 'Surat Pernyataan',
            'surat_pengalihan' => 'Surat Pengalihan',
            'tanda_terima' => 'Tanda Terima',
            'scan_ktp' => 'Scan KTP',
            'hasil_ciptaan' => 'Hasil Ciptaan',
        ];

        $items = [];
        foreach ($docs as $d) {
            $adminFull = null;
            if ($d->admin_attachment_path) {
                $adminFull = storage_path('app/public/' . $d->admin_attachment_path);
            }
            $items[] = [
                'label' => $labels[$d->doc_key] ?? $d->doc_key,
                'note' => $d->note,
                'has_attachment' => !empty($d->admin_attachment_path),
                'admin_attachment_fullpath' => $adminFull,
                'admin_attachment_name' => $d->admin_attachment_path ? basename($d->admin_attachment_path) : null,
            ];
        }

        try {
            foreach ($emails as $to) {
                Mail::to($to)->send(new RevisiMail($kategori, $judul, $no, $items));
            }

            // ✅ balik ke halaman asal
            return redirect()->back()->with('success', 'Email revisi terkirim ke: '.implode(', ', $emails));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email revisi', [
                'ref_type'=>$type,'ref_id'=>$id,'emails'=>$emails,'err'=>$e->getMessage()
            ]);

            return redirect()->back()->with('success', 'Gagal kirim email revisi. Cek storage/logs/laravel.log');
        }
    }

    public function destroyPaten($id)
    {
        $row = Paten::findOrFail($id);

        $paths = [
            $row->draft_paten,
            $row->form_permohonan,
            $row->surat_kepemilikan,
            $row->surat_pengalihan,
            $row->scan_ktp,
            $row->tanda_terima,
            $row->gambar_prototipe,
        ];

        foreach ($paths as $p) {
            if ($p) Storage::disk('public')->delete($p);
        }

        $row->delete();

        return back()->with('success', 'Data paten berhasil dihapus.');
    }

    public function destroyCipta($id)
    {
        $row = HakCipta::findOrFail($id);

        $paths = [
            $row->surat_permohonan,
            $row->surat_pernyataan,
            $row->surat_pengalihan,
            $row->tanda_terima,
            $row->scan_ktp,
            $row->hasil_ciptaan,
        ];

        foreach ($paths as $p) {
            if ($p) Storage::disk('public')->delete($p);
        }

        $row->delete();

        return back()->with('success', 'Data hak cipta berhasil dihapus.');
    }
}