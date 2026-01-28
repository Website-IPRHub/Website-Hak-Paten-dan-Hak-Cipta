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
use App\Models\PatenVerif;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Process\Process;


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
        $sub  = $request->query('sub', 'all'); // all | revisi

        // =========================
        // STATISTIK
        // =========================
        $totalPaten = DB::table('paten_verifs')->count();
        $totalCipta = HakCipta::count();
        $totalAll   = $totalPaten + $totalCipta;

        $patenJenis = DB::table('paten_verifs')
            ->select('jenis_paten', DB::raw('count(*) as total'))
            ->groupBy('jenis_paten')
            ->pluck('total', 'jenis_paten')
            ->map(fn ($v) => (int)$v)
            ->toArray();

        $ciptaJenis = HakCipta::select('jenis_cipta', DB::raw('count(*) as total'))
            ->groupBy('jenis_cipta')
            ->pluck('total', 'jenis_cipta')
            ->map(fn ($v) => (int)$v)
            ->toArray();

        // default collections biar blade aman
        $dataPaten   = collect();
        $dataCipta   = collect();
        $dataStatus  = collect();
        $revisiItems = collect();
        $notifCount  = 0;

        // keys dokumen per tipe
        $keysByType = [
            'paten' => [
                'draft_paten',
                'form_permohonan',
                'surat_kepemilikan',
                'surat_pengalihan',
                'scan_ktp',
                'tanda_terima',
                'gambar_prototipe',
            ],
            'cipta' => [
                'surat_permohonan',
                'surat_pernyataan',
                'surat_pengalihan',
                'tanda_terima',
                'scan_ktp',
                'hasil_ciptaan',
            ],
        ];

        // =========================
        // TAB PATEN
        // =========================
        if ($tab === 'paten') {
            $dataPaten = DB::table('paten_verifs as p')
                ->leftJoin('status_verifikasi as sv', function ($join) {
                    $join->on('sv.ref_id', '=', 'p.id')
                        ->where('sv.ref_type', '=', 'paten');
                })
                ->select([
                    'p.*',
                    DB::raw("COALESCE(sv.status,'terkirim') as status"),
                    'sv.sertifikat_path',
                    'sv.emailed_at',
                ])
                ->orderByDesc('p.id')
                ->get();

            $dataPaten = $this->attachDocsToRows($dataPaten, 'paten', $keysByType['paten']);
        }

        // =========================
        // TAB CIPTA
        // =========================
        if ($tab === 'cipta') {
            $dataCipta = HakCipta::orderByDesc('id')->get();
            $dataCipta = $this->attachDocsToRows($dataCipta, 'cipta', $keysByType['cipta']);
        }

        // =========================
        // TAB STATUS (gabung paten + cipta)
        // - INI HARUS SELALU TERISI saat tab=status (baik sub=all maupun sub=revisi)
        // =========================
        if ($tab === 'status') {

            // ---- PATEN
            $paten = DB::table('paten_verifs as p')
                ->leftJoin('status_verifikasi as sv', function ($join) {
                    $join->on('sv.ref_id', '=', 'p.id')
                        ->where('sv.ref_type', '=', 'paten');
                })
                ->select([
                    'p.id',
                    'p.no_pendaftaran',
                    DB::raw('p.judul_paten as judul'),
                    DB::raw('p.jenis_paten as jenis'),
                    'p.email',

                    'p.draft_paten',
                    'p.form_permohonan',
                    'p.surat_kepemilikan',
                    'p.surat_pengalihan',
                    'p.scan_ktp',
                    'p.tanda_terima',
                    'p.gambar_prototipe',

                    DB::raw("'paten' as type"),
                    DB::raw("COALESCE(sv.status,'terkirim') as status"),
                    'sv.sertifikat_path',
                    'sv.emailed_at',
                ])
                ->orderByDesc('p.id')
                ->get();

            // ---- CIPTA
            $cipta = HakCipta::query()
                ->leftJoin('status_verifikasi as sv', function ($join) {
                    $join->on('sv.ref_id', '=', 'hak_cipta.id')
                        ->where('sv.ref_type', '=', 'cipta');
                })
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
                    DB::raw("COALESCE(sv.status,'terkirim') as status"),
                    'sv.sertifikat_path',
                    'sv.emailed_at',
                ])
                ->orderByDesc('hak_cipta.id')
                ->get()
                ->map(function ($r) {
                    if (strtolower((string)$r->jenis) === 'lainnya') {
                        $r->jenis = $r->jenis_lainnya ?: 'Lainnya';
                    }
                    return $r;
                });

            $dataStatus = $paten->concat($cipta)->values();

            // inject docs untuk semua row status
            $dataStatus = $dataStatus->map(function ($r) use ($keysByType) {
                $docKeys = $keysByType[$r->type] ?? [];

                $existing = VerifikasiDokumen::where([
                    'ref_type' => $r->type,
                    'ref_id'   => $r->id,
                ])->get()->keyBy('doc_key');

                $docsArr = [];
                foreach ($docKeys as $k) {
                    $docsArr[$k] = $existing->get($k) ?? (object)[
                        'doc_key' => $k,
                        'status'  => 'pending',
                        'note'    => null,
                        'admin_attachment_path' => null,
                        // 🔥 ini PENTING: di table verifikasi_dokumen kamu belum ada kolom ini,
                        // jadi kita set null supaya blade aman.
                        'pemohon_file_path' => null,
                    ];
                }

                $r->docs = $docsArr;
                return $r;
            });

            // =========================
            // ✅ REVISI MASUK (UPLOAD PEMOHON)
            // sumbernya dari TABLE revisions:
            // - state = submitted
            // - pemohon_file_path NOT NULL
            // - is_read_admin = 0 (belum dibaca admin)
            // =========================
            $incoming = DB::table('revisions')
                ->whereIn('type', ['paten', 'cipta'])
                ->where('state', 'submitted')
                ->whereNotNull('pemohon_file_path')
                ->where('is_read_admin', 0)
                ->orderByDesc('updated_at')
                ->get();

            // map pengajuan (paten/cipta) supaya gampang dicocokin
            $mapStatus = $dataStatus->keyBy(fn($r) => $r->type . ':' . $r->id);

            // group incoming per pengajuan (type+ref_id)
            $revisiItems = $incoming
                ->groupBy(fn($rv) => $rv->type . ':' . $rv->ref_id)
                ->map(function ($rows, $key) use ($mapStatus) {
                    $base = $mapStatus->get($key);
                    if (!$base) return null;

                    $base->revisi_masuk = $rows->map(function ($rv) {
                        return (object)[
                            'id' => $rv->id,
                            'doc_key' => $rv->doc_key,
                            'note' => $rv->note,
                            'admin_file_path' => $rv->file_path ?? null,         // ✅ file admin (kolom file_path)
                            'pemohon_file_path' => $rv->pemohon_file_path ?? null, // ✅ file pemohon (kolom pemohon_file_path)
                            'updated_at' => $rv->updated_at,
                        ];
                    })->values();

                    return $base;
                })
                ->filter()
                ->values();

            $notifCount = $incoming->count();
        }

        return view('admin.dashboard', compact(
            'name',
            'tab',
            'sub',
            'dataPaten',
            'dataCipta',
            'dataStatus',
            'revisiItems',
            'notifCount',
            'totalAll',
            'totalPaten',
            'totalCipta',
            'patenJenis',
            'ciptaJenis',
        ));
    }

    private function attachDocsToRows(\Illuminate\Support\Collection $rows, string $type, array $docKeys): \Illuminate\Support\Collection
    {
        if ($rows->count() === 0) return $rows;

        // ambil id (works for stdClass & model)
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

            // ✅ stdClass & model aman
            $r->docs = $docsArr;

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

    // ========= helper WA =========
    private function normalizeWaNumber(?string $raw): ?string
    {
        if (!$raw) return null;

        // buang spasi, tanda +, strip, dll → jadi hanya angka
        $num = preg_replace('/\D+/', '', $raw);

        if (!$num) return null;

        // kalau mulai 0 → ganti jadi 62
        if (str_starts_with($num, '0')) {
            $num = '62' . substr($num, 1);
        }

        // kalau mulai 8 (user ngetik 812...) → tambahin 62
        if (str_starts_with($num, '8')) {
            $num = '62' . $num;
        }

        // basic validation panjang
        if (strlen($num) < 10 || strlen($num) > 16) return null;

        return $num;
    }

    private function makeWaLink(?string $phone, string $message): ?string
    {
        $phone = $this->normalizeWaNumber($phone);
        if (!$phone) return null;

        // wa.me butuh URL encoded message
        $text = rawurlencode($message);

        return "https://wa.me/{$phone}?text={$text}";
    }

    private function getPemohonPhone($row): ?string
    {
        // dukung dua kemungkinan kolom
        return $row->nomor_hp ?? $row->no_hp ?? null;
    }

// ========= STATUS VERIFIKASI (tabel status_verifikasi) =========
   public function updateStatusVerifikasi(Request $request, string $type, int $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return $request->expectsJson()
                ? response()->json(['ok'=>false,'message'=>'Unauthorized'], 401)
                : redirect()->route('admin.login.form');
        }

        if (!in_array($type, ['paten', 'cipta'], true)) abort(404);

        // ✅ status yg dikirim dari form admin harus lowercase
        $request->validate([
            'status' => 'required|in:terkirim,proses,revisi,approve',
        ]);

        $newStatus = strtolower($request->input('status'));

        // ✅ ambil old dulu (biar created_at aman)
       $old = DB::table('status_verifikasi')
        ->where('ref_type', $type)
        ->where('ref_id', $id)
        ->select('status','created_at','tanda_terima_pdf')
        ->first();

        $oldStatus = $old->status ?? null;
        $createdAt = $old->created_at ?? now();

        // ✅ simpan ke status_verifikasi (1x aja)
        DB::table('status_verifikasi')->updateOrInsert(
            ['ref_type' => $type, 'ref_id' => $id],
            [
                'status'     => $newStatus,
                'updated_at' => now(),
                'created_at' => $createdAt,
            ]
        );

        if ($newStatus === 'approve') {
            $path = $this->generateTandaTerimaPdf($type, $id);

            DB::table('status_verifikasi')
                ->where(['ref_type' => $type, 'ref_id' => $id])
                ->update([
                    'tanda_terima_pdf' => $path,
                    'updated_at' => now(),
                ]);
        }
        // ✅ ambil row pengajuan untuk email/wa
        if ($type === 'paten') {
            $row = PatenVerif::findOrFail($id);
            $kategori = 'PATEN';
            $judul = $row->judul_paten ?? '-';
        } else {
            $row = HakCipta::findOrFail($id);
            $kategori = 'HAK CIPTA';
            $judul = $row->judul_cipta ?? '-';
        }

        $no = $row->no_pendaftaran ?? '-';

        // ✅ kalau status sudah approve tapi PDF belum ada → generate sekarang juga
        if ($newStatus === 'approve' && empty($old?->tanda_terima_pdf)) {
            $path = $this->generateTandaTerimaPdf($type, $id);

            DB::table('status_verifikasi')
                ->where(['ref_type' => $type, 'ref_id' => $id])
                ->update([
                    'tanda_terima_pdf' => $path,
                    'tanda_terima_generated_at' => now(),
                    'updated_at' => now(),
                ]);

            // refresh old supaya bawahnya kebaca sudah ada pdf
            $old->tanda_terima_pdf = $path;
        }

        // ✅ kalau status sama, stop
        if ($newStatus === $oldStatus) {
            $msg = 'Status tidak berubah.';
            return $request->expectsJson()
                ? response()->json(['ok'=>true,'message'=>$msg,'status'=>$newStatus,'no_change'=>true])
                : redirect()->route('admin.dashboard', ['tab'=>'status'])->with('success', $msg);
        }

        // email status tertentu (silakan atur)
        $autoStatuses = ['terkirim','proses'];

        $emails = $this->parseEmails($row->email);
        $emailSent = false;

        if (in_array($newStatus, $autoStatuses, true) && count($emails) > 0) {
            try {
                foreach ($emails as $to) {
                    Mail::to($to)->send(new StatusUpdateMail($kategori, $judul, $no, $newStatus));
                }
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error('Gagal kirim email status update', [
                    'ref_type'=>$type,'ref_id'=>$id,'status'=>$newStatus,'err'=>$e->getMessage()
                ]);
            }
        }

        $waLink = null;
        if (in_array($newStatus, $autoStatuses, true)) {
            $phone = $this->getPemohonPhone($row);

            $waMsg =
                "Halo,\n".
                "Status pengajuan {$kategori} Anda telah diperbarui.\n".
                "No Pendaftaran: {$no}\n".
                "Judul: {$judul}\n".
                "Status: ".strtoupper($newStatus)."\n\n".
                "Terima kasih.";

            $waLink = $this->makeWaLink($phone, $waMsg);
        }

        $msg = 'Status berhasil diupdate.'
            . (count($emails) ? ($emailSent ? ' | Email terkirim.' : ' | Email gagal/skip.') : ' | Email kosong.')
            . ($waLink ? ' | WA siap (klik).' : ' | WA tidak valid.');

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $msg,
                'data' => [
                    'type' => $type,
                    'id' => $id,
                    'status' => $newStatus,
                    'kategori' => $kategori,
                    'judul' => $judul,
                    'no' => $no,
                ],
                'wa_link' => $waLink,
            ]);
        }
        if (in_array($newStatus, ['proses','approve'])) {

            $sv = DB::table('status_verifikasi')
                ->where(['ref_type'=>$type,'ref_id'=>$id])
                ->first();

            if (empty($sv->tanda_terima_pdf)) {
                $pdfPath = $this->generateTandaTerimaPdf($type, $id);

                DB::table('status_verifikasi')
                    ->where(['ref_type'=>$type,'ref_id'=>$id])
                    ->update([
                        'tanda_terima_pdf' => $pdfPath,
                        'tanda_terima_generated_at' => now(),
                    ]);
            }
        }

        if ($newStatus === 'approve') {
            $path = $this->generateTandaTerimaPdf($type, $id);

            DB::table('status_verifikasi')
                ->where(['ref_type' => $type, 'ref_id' => $id])
                ->update([
                    'tanda_terima_pdf' => $path,
                    'updated_at' => now(),
                ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'status'])
            ->with('success', $msg)
            ->with('wa_link', $waLink)
            ->with('wa_label', 'Kirim WA (Status Update)');
    }

    // ========= upload sertifikat DJKI + kirim email diterima =========
    public function uploadSertifikatVerifikasi(Request $request, string $type, int $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return $request->expectsJson()
                ? response()->json(['ok'=>false,'message'=>'Unauthorized'], 401)
                : redirect()->route('admin.login.form');
        }

        if (!in_array($type, ['paten', 'cipta'])) abort(404);

        $request->validate([
            'sertifikat' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('sertifikat')->store('sertifikat_djki', 'public');

        $old = DB::table('status_verifikasi')->where(['ref_type'=>$type,'ref_id'=>$id])->first();
        DB::table('status_verifikasi')->updateOrInsert(
            ['ref_type' => $type, 'ref_id' => $id],
            [
                'sertifikat_path' => $path,
                'updated_at' => now(),
                'created_at' => $old ? $old->created_at : now(),
            ]
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

        $meta = $this->getRowByType($type, $id);
        $row = $meta['row'];
        $kategori = $meta['kategori'];
        $judul = $meta['judul'];
        $no = $meta['no'];

        $emails = $this->parseEmails($meta['email']);

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

        $meta = $this->getRowByType($type, $id);
        $row = $meta['row'];
        $kategori = $meta['kategori'];
        $judul = $meta['judul'];

        $emails = $this->parseEmails($meta['email']);
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
            $old = DB::table('status_verifikasi')->where(['ref_type'=>$type,'ref_id'=>$id])->first();

            DB::table('status_verifikasi')->updateOrInsert(
                ['ref_type'=>$type,'ref_id'=>$id],
                [
                    'status' => 'revisi',
                    'updated_at' => now(),
                    'created_at' => $old ? $old->created_at : now(),
                ]
            );
        }

        if ($request->expectsJson()) {
            // ambil doc yang baru diupdate biar UI bisa update
            $doc = VerifikasiDokumen::where([
                'ref_type' => $type,
                'ref_id' => $id,
                'doc_key' => $docKey,
            ])->first();

            // cek apakah masih ada revisi di pengajuan ini (buat toggle tombol kirim revisi)
            $hasRevisi = VerifikasiDokumen::where(['ref_type'=>$type,'ref_id'=>$id])
                ->where('status','revisi')->exists();

            return response()->json([
                'ok' => true,
                'message' => 'Verifikasi dokumen berhasil disimpan.',
                'doc' => [
                    'doc_key' => $docKey,
                    'status' => $doc->status ?? 'pending',
                    'note' => $doc->note,
                    'admin_attachment_path' => $doc->admin_attachment_path,
                    'admin_attachment_url' => $doc->admin_attachment_path ? asset('storage/'.$doc->admin_attachment_path) : null,
                ],
                'has_revisi' => $hasRevisi,
            ]);
        }

        return redirect()->back()->with('success', 'Verifikasi dokumen berhasil disimpan.');
    }

    public function sendRevisiPaten(Request $request, $id)
    {
        $request->validate([
        'note' => 'required|string',
        'file' => 'nullable|mimes:pdf,doc,docx|max:5120'
        ]);

        $paten = DB::table('paten_verifs')->where('id',$id)->first();
        if(!$paten) abort(404);

        $path = null;
        if($request->hasFile('file')){
        $path = $request->file('file')->store('revisi/paten/admin','public');
        }

        DB::table('paten_verifs')->where('id',$id)->update(['status_verif'=>'revisi']);

        DB::table('revisions')->insert([
        'type'=>'paten',
        'ref_id'=>$id,
        'from_role'=>'admin',
        'note'=>$request->note,
        'file_path'=>$path,
        'is_read_admin'=>true,
        'is_read_pemohon'=>false,
        'created_at'=>now(),
        'updated_at'=>now(),
        ]);

        // optional: tetap kirim email
        // Mail::to(...)->send(...)

        return back()->with('success','Revisi berhasil dikirim.');
    }

    // ========= kirim email revisi (sekali kirim list dokumen revisi) =========
   public function sendRevisiEmail(Request $request, string $type, int $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }
        if (!in_array($type, ['paten', 'cipta'], true)) abort(404);

        // ambil meta pengajuan
        $meta = $this->getRowByType($type, $id);
        $row = $meta['row'];
        $kategori = $meta['kategori'];
        $judul = $meta['judul'];
        $no = $meta['no'];

        $emails = $this->parseEmails($meta['email']);
        if (count($emails) === 0) {
            if ($request->expectsJson()) {
                return response()->json(['ok'=>false,'message'=>'Gagal kirim revisi: email pemohon kosong/tidak valid.'], 422);
            }
            return redirect()->back()->with('success', 'Gagal kirim revisi: email pemohon kosong/tidak valid.');
        }

        // ambil dokumen yang statusnya revisi
        $docs = VerifikasiDokumen::where(['ref_type'=>$type,'ref_id'=>$id])
            ->where('status','revisi')
            ->orderBy('doc_key')
            ->get();

        if ($docs->count() === 0) {
            if ($request->expectsJson()) {
                return response()->json(['ok'=>false,'message'=>'Tidak ada dokumen revisi untuk dikirim.'], 422);
            }
            return redirect()->back()->with('success', 'Tidak ada dokumen revisi untuk dikirim.');
        }

        // label dokumen
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

        // build item email (dengan file admin)
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

        // ✅ 1) set status_verifikasi = revisi
        $oldSv = DB::table('status_verifikasi')->where(['ref_type'=>$type,'ref_id'=>$id])->first();
        DB::table('status_verifikasi')->updateOrInsert(
            ['ref_type'=>$type,'ref_id'=>$id],
            [
                'status' => 'revisi',
                'updated_at' => now(),
                'created_at' => $oldSv?->created_at ?? now(),
            ]
        );

        // ✅ 2) UPSERT ke revisions per dokumen (doc_key)
        // agar pemohon bisa upload revisi per dokumen + admin dapat notif
        foreach ($docs as $d) {
            DB::table('revisions')->updateOrInsert(
                [
                    'type' => $type,
                    'ref_id' => $id,
                    'doc_key' => $d->doc_key,      // ⚠️ butuh kolom doc_key di table revisions
                    'from_role' => 'admin',
                    'state' => 'requested',
                ],
                [
                    'note' => $d->note,
                    'file_path' => $d->admin_attachment_path,
                    'is_read_admin' => true,
                    'is_read_pemohon' => false,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // kirim email revisi
        try {
            foreach ($emails as $to) {
                Mail::to($to)->send(new RevisiMail($kategori, $judul, $no, $items));
            }

            // optional WA
            $phone = $this->getPemohonPhone($row);
            $waMsg =
                "Halo,\n".
                "Pengajuan {$kategori} Anda membutuhkan REVISI.\n".
                "No Pendaftaran: {$no}\n".
                "Judul: {$judul}\n\n".
                "Detail revisi sudah kami kirim melalui EMAIL.\n".
                "Silakan cek email Anda dan unggah ulang dokumen sesuai catatan.\n\n".
                "Terima kasih.";
            $waLink = $this->makeWaLink($phone, $waMsg);

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Email revisi terkirim.',
                    'emails' => $emails,
                    'wa_link' => $waLink,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Email revisi terkirim ke: '.implode(', ', $emails))
                ->with('wa_link', $waLink)
                ->with('wa_label', 'Kirim WA (Announce Revisi)');

        } catch (\Throwable $e) {
            Log::error('Gagal kirim email revisi', [
                'ref_type'=>$type,'ref_id'=>$id,'emails'=>$emails,'err'=>$e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['ok'=>false,'message'=>'Gagal kirim email revisi.'], 500);
            }

            return redirect()->back()->with('success', 'Gagal kirim email revisi. Cek storage/logs/laravel.log');
        }
    }

    public function destroyPaten($id)
    {
        $row = PatenVerif::findOrFail($id);

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

    public function setRevisi(Request $request, string $type, int $id)
    {
        $request->validate([
            'note' => ['required','string'],
            'file' => ['nullable','file','max:10240'],
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store("revisi/{$type}/admin", 'public');
        }

        // status_verifikasi => revisi (lowercase)
        $old = DB::table('status_verifikasi')->where(['ref_type'=>$type,'ref_id'=>$id])->first();
        DB::table('status_verifikasi')->updateOrInsert(
            ['ref_type'=>$type,'ref_id'=>$id],
            ['status'=>'revisi','updated_at'=>now(),'created_at'=>$old->created_at ?? now()]
        );

        // revisions table (sesuai kolommu)
        DB::table('revisions')->insert([
            'type' => $type,
            'ref_id' => $id,
            'from_role' => 'admin',
            'note' => $request->note,
            'file_path' => $path,
            'is_read_admin' => true,
            'is_read_pemohon' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Revisi berhasil dikirim.');
    }

    private function getRowByType(string $type, int $id)
    {
        if ($type === 'paten') {
            $row = PatenVerif::findOrFail($id);
            return [
                'row' => $row,
                'kategori' => 'PATEN',
                'judul' => $row->judul_paten ?? '-',
                'email' => $row->email,
                'no' => $row->no_pendaftaran ?? '-',
            ];
        }

        $row = HakCipta::findOrFail($id);
        return [
            'row' => $row,
            'kategori' => 'HAK CIPTA',
            'judul' => $row->judul_cipta ?? '-',
            'email' => $row->email,
            'no' => $row->no_pendaftaran ?? '-',
        ];
    }

    public function markRevisionRead(Request $request, int $id)
    {
    if (!$request->session()->get('admin_logged_in')) {
        return redirect()->route('admin.login.form');
    }

    DB::table('revisions')->where('id', $id)->update([
        'is_read_admin' => true,
        'updated_at' => now(),
    ]);

    return back()->with('success', 'Notifikasi dibaca.');
    }

    private function generateTandaTerimaPdf(string $type, int $id): string
{
    $meta = $this->getRowByType($type, $id);

    // 1) template docx (taruh di: storage/app/templates/tanda_terima.docx)
    $template = storage_path('app/templates/tanda_terima.docx');
    if (!file_exists($template)) {
        throw new \Exception("Template tanda terima tidak ditemukan: {$template}");
    }

    // 2) isi docx dari template
    $doc = new TemplateProcessor($template);

    // NOTE: placeholder di DOCX harus ${jenis} dan ${judul}
    $doc->setValue('jenis', strtoupper($meta['kategori']));
    $doc->setValue('judul', $meta['judul']);

    // simpan docx sementara
    $tmpDir = storage_path('app/tmp');
    if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);

    $tmpDocx = $tmpDir . DIRECTORY_SEPARATOR . "tanda_terima_{$type}_{$id}.docx";
    $doc->saveAs($tmpDocx);

    // 3) convert ke PDF pakai LibreOffice
    $soffice = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';
    // kalau x86, pakai ini:
    // $soffice = 'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe';

    if (!file_exists($soffice)) {
        throw new \Exception("LibreOffice (soffice.exe) tidak ditemukan: {$soffice}");
    }

    $outDir = storage_path('app/public/tanda_terima');
    if (!is_dir($outDir)) mkdir($outDir, 0777, true);

    $process = new Process([
        $soffice,
        '--headless',
        '--nologo',
        '--nofirststartwizard',
        '--convert-to', 'pdf',
        '--outdir', $outDir,
        $tmpDocx,
    ]);

    $process->setTimeout(60);
    $process->run();

    if (!$process->isSuccessful()) {
        throw new \Exception("Gagal convert DOCX ke PDF: ".$process->getErrorOutput());
    }

    // 4) hasil pdf dari LibreOffice biasanya: tanda_terima_type_id.pdf (nama docx sama)
    $generatedPdf = $outDir . DIRECTORY_SEPARATOR . "tanda_terima_{$type}_{$id}.pdf";

    if (!file_exists($generatedPdf)) {
        // kadang LO bikin nama beda, fallback cari pdf terbaru
        $pdfs = glob($outDir . DIRECTORY_SEPARATOR . "*.pdf");
        rsort($pdfs);
        if (!empty($pdfs)) $generatedPdf = $pdfs[0];
    }

    if (!file_exists($generatedPdf)) {
        throw new \Exception("PDF tidak ditemukan setelah convert.");
    }

    // bersihin docx tmp
    @unlink($tmpDocx);

    // return path untuk DB (relative ke disk public)
    return "tanda_terima/" . basename($generatedPdf);
}
}
