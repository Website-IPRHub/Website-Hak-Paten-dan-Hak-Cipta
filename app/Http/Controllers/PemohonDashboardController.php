<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PatenVerif;
use App\Models\VerifikasiDokumen;
use Illuminate\Support\Facades\Storage;

class PemohonDashboardController extends Controller
{
    public function index(Request $request)
{
    $pemohon = Auth::guard('pemohon')->user();
    if (!$pemohon) return redirect()->route('pemohon.login.form');

    // kode unik pemohon = no_pendaftaran
    $kode = $pemohon->kode_unik
        ?? $pemohon->no_pendaftaran
        ?? $pemohon->kode
        ?? null;

    if (!$kode) {
        return redirect()->route('pemohon.login.form')
            ->with('error', 'Kode unik pemohon tidak ditemukan di akun.');
    }

        // ✅ ambil pengajuan TERBARU
    $paten = PatenVerif::where('no_pendaftaran', $kode)
        ->orderByDesc('id')
        ->first();

    $cipta = DB::table('hak_cipta_verifs')
        ->where('no_pendaftaran', $kode)
        ->orderByDesc('id')
        ->first();


    $source = null;
    $type   = null;

    if ($paten && $cipta) {
        $patenAt = Carbon::parse($paten->created_at);
        $ciptaAt = Carbon::parse($cipta->created_at);

        if ($patenAt->greaterThan($ciptaAt)) {
            $source = $paten; $type = 'paten';
        } else {
            $source = $cipta; $type = 'cipta';
        }
    } elseif ($paten) {
        $source = $paten; $type = 'paten';
    } elseif ($cipta) {
        $source = $cipta; $type = 'cipta';
    } else {
        return redirect()->route('pemohon.login.form')
            ->with('error', 'Data pengajuan tidak ditemukan untuk kode ini.');
    }

    $refId = $source->id;

    // ✅ status_verifikasi: ambil row TERBARU
    $sv = DB::table('status_verifikasi')
        ->where('ref_type', $type)
        ->where('ref_id', $refId)
        ->orderByDesc('id')
        ->first();

    $status = strtolower($sv->status ?? 'terkirim');
    $activeStatus = $status;

    // ✅ ambil waktu update beneran (pakai time)
    $updatedAt = $sv?->updated_at
        ? Carbon::parse($sv->updated_at)
        : (isset($source->created_at) ? Carbon::parse($source->created_at) : Carbon::now());

    $updatedStr = $updatedAt->timezone('Asia/Jakarta')->format('d M Y H:i');

    $rank = ['terkirim'=>1, 'proses'=>2, 'revisi'=>3, 'approve'=>4];
    $currentRank = $rank[$status] ?? 1;

    $baseSteps = [
        ['key' => 'terkirim', 'label' => 'TERKIRIM'],
        ['key' => 'proses',   'label' => 'PROSES'],
        ['key' => 'revisi',   'label' => 'REVISI'],
        ['key' => 'approve',  'label' => 'APPROVE'],
    ];

    $steps = array_map(function ($s) use ($rank, $currentRank, $updatedStr) {
        $stepRank = $rank[$s['key']] ?? 1;

        if ($stepRank < $currentRank) $cls = 'is-done';
        elseif ($stepRank === $currentRank) $cls = 'is-run';
        else $cls = 'is-todo';

        return [
            'key' => $s['key'],
            'label' => $s['label'],
            'updated_at' => ($stepRank <= $currentRank) ? $updatedStr : '-',
            'cls' => $cls,
        ];
    }, $baseSteps);

    // revisi docs
    $revisiDocs = collect();
    if ($status === 'revisi') {
        $revisiDocs = VerifikasiDokumen::where([
                'ref_type' => $type,
                'ref_id'   => $refId,
            ])
            ->where('status', 'revisi')
            ->orderBy('doc_key')
            ->get();
    }

    // ✅ DEFAULT harus ada (biar view gak error)
   // ✅ DEFAULT harus ada (biar view gak error)
$revActiveByDoc = collect();
$revHistory = collect();

// ✅ ACTIVE revisi cuma saat status = revisi (tetap seperti kamu)
if ($status === 'revisi') {
    $revActiveByDoc = DB::table('revisions as r')
        ->where('r.type', $type)
        ->where('r.ref_id', $refId)
        ->where('r.from_role', 'admin')
        ->where('r.state', 'requested')
        ->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
            ->from('revisions as s')
            ->whereColumn('s.type', 'r.type')
            ->whereColumn('s.ref_id', 'r.ref_id')
            ->whereColumn('s.doc_key', 'r.doc_key')
            ->where('s.from_role', 'pemohon')
            ->where('s.state', 'submitted')
            ->where(function ($qq) {
                $qq->whereNotNull('s.pemohon_file_path')
                    ->orWhereNotNull('s.pemohon_text');
            })
            ->whereColumn('s.id', '>', 'r.id');
        })
        ->orderByDesc('r.id')
        ->get()
        ->groupBy('doc_key')
        ->map(fn($rows) => $rows->first())
        ->values()
        ->map(function($req) use ($source){
            $isTextDoc = ($req->doc_key ?? null) === 'deskripsi_singkat_prototipe';

            $req->pemohon_uploaded = 0;
            $req->admin_note       = $req->note ?? null;
            $req->admin_file_path  = $req->file_path ?? null;

            if ($isTextDoc) {
                $req->pemohon_text = trim((string) data_get($source, 'deskripsi_singkat_prototipe', ''));
            } else {
                $req->pemohon_text = null;
            }

            return $req;
        });
}

// ✅ RIWAYAT harus tetap ada di REVISI & APPROVE
if (in_array($status, ['revisi', 'approve'])) {

    // ambil SEMUA row yang relevan biar bisa nyari note admin terakhir
    $all = DB::table('revisions')
        ->where('type', $type)
        ->where('ref_id', $refId)
        ->whereIn('state', ['requested','submitted','closed'])
        ->orderBy('id') // penting: ASC supaya "last admin note" kebentuk urut
        ->get();

    $lastAdminNoteByDoc = [];
    $lastAdminFileByDoc = [];

    foreach ($all as $r) {
        $doc = $r->doc_key ?? null;
        if (!$doc) continue;

        // simpan note/file admin terakhir
        if (($r->from_role ?? null) === 'admin' && in_array(($r->state ?? null), ['requested','closed'], true)) {
            if (!empty(trim((string)($r->note ?? '')))) {
                $lastAdminNoteByDoc[$doc] = $r->note;
            }
            if (!empty($r->file_path)) {
                $lastAdminFileByDoc[$doc] = $r->file_path;
            }
        }
    }

    // history yang ditampilkan: submitted + closed (sesuai logic kamu)
   $revHistory = $all
    ->filter(fn($r) => in_array(($r->state ?? null), ['submitted','closed'], true))
    ->sortByDesc('id')
    ->values()
    ->map(function($r) use ($all, $lastAdminNoteByDoc, $lastAdminFileByDoc){

        $doc = $r->doc_key ?? null;

        $r->pemohon_uploaded = (!empty($r->pemohon_file_path) || !empty($r->pemohon_text)) ? 1 : 0;
        $r->pemohon_text = $r->pemohon_text ?? null;

        // ✅ kalau row pemohon note kosong -> ambil ADMIN NOTE yang posisinya TEPAT SEBELUM row ini
        $noteRaw = trim((string)($r->note ?? ''));
        if ($noteRaw === '' && ($r->from_role ?? null) === 'pemohon' && $doc) {

            $prevAdmin = $all->where('doc_key', $doc)
                ->where('id', '<', $r->id)
                ->filter(fn($x) => ($x->from_role ?? null) === 'admin' && in_array(($x->state ?? null), ['requested','closed'], true))
                ->sortByDesc('id')
                ->first();

            $noteRaw = trim((string)(($prevAdmin->note ?? '') ?: ''));
            if ($noteRaw === '') {
                // fallback terakhir kalau memang gak nemu (jarang)
                $noteRaw = trim((string)($lastAdminNoteByDoc[$doc] ?? ''));
            }
        }

        $r->admin_note = ($noteRaw !== '') ? $noteRaw : null;

        // ✅ file admin: kalau row ini gak punya file_path, ambil ADMIN FILE sebelum row ini
        $r->admin_file_path = $r->file_path ?? ($doc ? ($lastAdminFileByDoc[$doc] ?? null) : null);

        return $r;
    });
}

    // helper pick
    $pick = function ($obj, array $keys, $default = '-') {
        foreach ($keys as $k) {
            if (is_object($obj) && isset($obj->$k) && $obj->$k !== null && $obj->$k !== '') return $obj->$k;
            if (is_array($obj) && isset($obj[$k]) && $obj[$k] !== null && $obj[$k] !== '') return $obj[$k];
        }
        return $default;
    };

    // ✅ pengajuan (untuk view)
    $pengajuan = (object) [
        'kode'     => $kode,
        'kategori' => $type === 'paten' ? 'Paten' : 'Hak Cipta',

        'judul'    => $type === 'paten'
            ? $pick($source, ['judul_paten'])
            : $pick($source, ['judul_cipta', 'judul', 'judul_pengajuan']),

        'jenis'    => $type === 'paten'
            ? $pick($source, ['jenis_paten'])
            : $pick($source, ['jenis_cipta', 'jenis', 'jenis_lainnya']),

        'email'    => $pick($source, ['email'], $pick($pemohon, ['email'])),
        'no_hp'    => $pick($source, ['no_hp'], $pick($pemohon, ['no_hp', 'hp', 'nomor_hp'])),
        'id'       => $refId,
        'type'     => $type,
    ];

    // inventors
    $inventorsArr = [];
    $inventorList = '-';

    if ($type === 'paten') {
        $inventorsRaw = $source->inventors ?? null;
        $inventorsArr = is_string($inventorsRaw) ? (json_decode($inventorsRaw, true) ?? []) : ($inventorsRaw ?? []);
        $inventorList = collect($inventorsArr)
            ->map(fn($i) => trim(($i['nama'] ?? '-').' ('.($i['status'] ?? '-').')'))
            ->filter()->implode(', ');
    } else {
        $inventorsRaw = $source->inventors ?? $source->pencipta ?? $source->inventor ?? null;
        $inventorsArr = is_string($inventorsRaw) ? (json_decode($inventorsRaw, true) ?? []) : ($inventorsRaw ?? []);
        $inventorList = collect($inventorsArr)
            ->map(fn($i) => trim(($i['nama'] ?? '-').' ('.($i['status'] ?? ($i['kategori'] ?? '-')).')'))
            ->filter()->implode(', ');
        if ($inventorList === '') $inventorList = '-';
    }

    $akun = (object) [
        'nama'     => $type === 'paten'
            ? $pick($source, ['nama_pencipta', 'nama'])
            : $pick($source, ['nama_pencipta', 'nama_pemohon', 'nama']),

        'kode'     => $kode,
        'fakultas' => $pick($source, ['fakultas']),
        'inventor_list' => $inventorList,
        'inventors_arr' => $inventorsArr,

        'kategori' => $pengajuan->kategori ?? '-',
        'jenis'    => $pengajuan->jenis ?? '-',
        'judul'    => $pengajuan->judul ?? '-',
        'email'    => $pengajuan->email ?? '-',
        'no_hp'    => $pengajuan->no_hp ?? '-',
    ];

    // =============================
    // DETAIL DOKUMEN TERKIRIM
    // =============================
    $submittedDocs = collect();

    if (in_array($status, ['terkirim', 'proses', 'revisi', 'approve'])) {

        // mapping label beda paten & cipta
       $labelsPaten = [
        'draft_paten'       => 'Draft Paten',
        'form_permohonan'   => 'Form Permohonan',
        'surat_kepemilikan' => 'Surat Kepemilikan',
        'surat_pengalihan'  => 'Surat Pengalihan',
        'scan_ktp'          => 'Scan KTP',
        'tanda_terima'      => 'Tanda Terima',
        'gambar_prototipe'  => 'Gambar Prototipe',
        ];

        $labelsCipta = [
        'surat_permohonan' => 'Surat Permohonan',
        'surat_pernyataan' => 'Surat Pernyataan',
        'surat_pengalihan' => 'Surat Pengalihan',
        'tanda_terima'     => 'Tanda Terima',
        'scan_ktp'         => 'Scan KTP',
        'hasil_ciptaan'    => 'Hasil Ciptaan',
        ];

        $labels = $type === 'paten' ? $labelsPaten : $labelsCipta;

        $submittedDocs = VerifikasiDokumen::where([
            'ref_type' => $type,
            'ref_id'   => $refId,
        ])
        ->whereIn('doc_key', array_keys($labels))
        ->get()
        ->map(function($d) use ($labels) {
            return (object)[
                'doc_key' => $d->doc_key,
                'label'   => $labels[$d->doc_key] ?? $d->doc_key,
                'file'    => $d->pemohon_file_path ?? null,
                'status'  => $d->status ?? 'pending',
                'note'    => $d->note ?? null,
            ];
        })
        ->filter(fn($x) => !empty($x->file))
        ->values();
    }

    // ✅ INI WAJIB: RETURN VIEW SELALU
    return view('pemohon.dashboard', compact(
        'pemohon',
        'pengajuan',
        'akun',
        'sv',
        'status',
        'steps',
        'activeStatus',
        'revisiDocs',
        'source',
        'revActiveByDoc',
        'revHistory',
        'submittedDocs'
    ));
}

public function editRevisi(Request $request)
{
    
    $pemohon = Auth::guard('pemohon')->user();
    if (!$pemohon) {
        return redirect()->route('pemohon.login.form');
    }

    $type = $request->query('type');
    $ref  = (int) $request->query('ref');
    $doc  = $request->query('doc');

    if (!in_array($type, ['cipta', 'paten'], true) || !$ref || !$doc) {
        abort(404, 'Parameter revisi tidak valid.');
    }

   if ($type === 'cipta') {
    $row = DB::table('hak_cipta_verifs')->where('id', $ref)->first();
    if (!$row) abort(404);
    
    $sessionKeySpecific = "hakcipta.form.$ref";
    $sessionKeyGlobal   = "hakcipta.form";

    // Ambil data dari saku pendaftaran awal (Global)
    $globalData = session($sessionKeyGlobal, []);
    
    // 🔥 Ambil data revisi yang sudah ada (kalau pernah save sebelumnya)
    $existingSpecific = session($sessionKeySpecific, []);

    // 🔥 GABUNGKAN: Prioritas data revisi, tapi kalau kosong ambil dari global
    // Ini gunanya biar 'berupa', 'tempat', 'uraian' dari pendaftaran awal terbawa ke halaman edit
    $finalPayload = array_merge($globalData, $existingSpecific);

    // Simpan ke saku spesifik revisi
    session()->put($sessionKeySpecific, $finalPayload);
    session(['edit_ref_id' => $ref]);

    return redirect()->route('dup.hakcipta.isiform.formpendaftaran', ['ref' => $ref]);
}

    // --- LOGIC UNTUK HAK PATEN (Perbaikan di Sini) ---
   // --- LOGIC UNTUK HAK PATEN (DIBENARKAN) ---
    // --- LOGIC UNTUK HAK PATEN (DIPERBAIKI TOTAL) ---
   // --- LOGIC UNTUK HAK PATEN (PASTI MUNCUL) ---
    if ($type === 'paten') {
        $row = DB::table('paten_verifs')->where('id', $ref)->first();
        if (!$row) abort(404);
        
        $inventors = !empty($row->inventors) ? (is_string($row->inventors) ? json_decode($row->inventors, true) : (array)$row->inventors) : [];

        $sessionKeySpecific = "hakpaten.isiform.$ref";
        $sessionKeyGlobal   = "hakpaten.isiform";

        // 1. Ambil data dari saku spesifik (hasil edit/revisi sebelumnya)
        $existingSession = session($sessionKeySpecific);
        
        if (!$existingSession) {
            // 2. KALO KOSONG (Pas pertama kali klik EDIT), ambil dari saku pendaftaran awal
            $existingSession = session($sessionKeyGlobal, []);
        }

        // 3. Data dari Database
        $dbData = [
            'jenis_paten'     => $row->jenis_paten ?? '',
            'judul_invensi'   => $row->judul_paten ?? '',
            'deskripsi_singkat_prototipe'  => $row->deskripsi_singkat_prototipe ?? '',
            'jumlah_inventor' => count($inventors) ?: 1,
            'inventor' => [
                'nama'            => collect($inventors)->pluck('nama')->all(),
                'nip_nim'         => collect($inventors)->pluck('nip_nim')->all(),
                'alamat'          => collect($inventors)->pluck('alamat')->all(),
                'kode_pos'        => collect($inventors)->pluck('kode_pos')->all(),
                'pekerjaan'       => collect($inventors)->pluck('pekerjaan')->all(),
                'kewarganegaraan' => collect($inventors)->pluck('kewarganegaraan')->all(),
                'fakultas'        => collect($inventors)->pluck('fakultas')->all(),
                'no_hp'           => collect($inventors)->pluck('no_hp')->all(),
                'email'           => collect($inventors)->pluck('email')->all(),
                'status'          => collect($inventors)->pluck('status')->all(),
                'nidn'            => collect($inventors)->pluck('nidn')->all(),
            ],
        ];

        // 4. GABUNGKAN: Data dari session (Uraian, Klaim, dll) mengisi bagian yang kosong di DB
        $finalData = array_merge($dbData, $existingSession);

        // 5. Simpan ke saku spesifik ID biar kedepannya nempel terus di ID ini
        session([$sessionKeySpecific => $finalData]);
        session(['edit_ref_id' => $ref]);
        if ($doc === 'deskripsi_singkat_prototipe') {
            $dbData = [
                'deskripsi_singkat_prototipe' => $row->deskripsi_singkat_prototipe ?? '',
            ];

            $finalData = array_merge($dbData, $existingSession);
            session([$sessionKeySpecific => $finalData]);
            session(['edit_ref_id' => $ref]);

            return redirect()->route('pemohon.paten.edit_deskripsi', ['ref' => $ref]);
        }
        return redirect()->route('dup.hakpaten.isiformulir.isiform', ['ref' => $ref]);
    }
    abort(404);
}

    public function editDeskripsiSingkat(Request $request)
{
    $pemohon = Auth::guard('pemohon')->user();
    if (!$pemohon) {
        return redirect()->route('pemohon.login.form');
    }

    $ref = (int) $request->query('ref');
    if (!$ref) {
        abort(404, 'Ref pengajuan tidak valid.');
    }

    $row = DB::table('paten_verifs')->where('id', $ref)->first();
    if (!$row) {
        abort(404, 'Data paten tidak ditemukan.');
    }

    // pakai session yang sama dengan flow paten lama
    $sessionKeySpecific = "hakpaten.isiform.$ref";
    $sessionKeyGlobal   = "hakpaten.isiform";

    $existingSession = session($sessionKeySpecific);
    if (!$existingSession) {
        $existingSession = session($sessionKeyGlobal, []);
    }

    $deskripsi = $existingSession['deskripsi_singkat_prototipe']
        ?? $row->deskripsi_singkat_prototipe
        ?? '';

    // simpan lagi biar session spesifik tetap kebentuk
    $existingSession['deskripsi_singkat_prototipe'] = $deskripsi;
    session([$sessionKeySpecific => $existingSession]);
    session(['edit_ref_id' => $ref]);

    return view('pemohon.edit_deskripsi_singkat', [
        'ref' => $ref,
        'deskripsi' => $deskripsi,
        'judul' => $row->judul_paten ?? '-',
    ]);
}

public function updateDeskripsiSingkat(Request $request)
{
    $pemohon = Auth::guard('pemohon')->user();
    if (!$pemohon) {
        return redirect()->route('pemohon.login.form');
    }

    $data = $request->validate([
        'ref' => 'required|integer',
        'deskripsi_singkat_prototipe' => 'nullable|string|max:5000',
    ]);

    $ref = (int) $data['ref'];
    $text = trim((string) ($data['deskripsi_singkat_prototipe'] ?? ''));

    $row = DB::table('paten_verifs')->where('id', $ref)->first();
    if (!$row) {
        abort(404, 'Data paten tidak ditemukan.');
    }

    DB::beginTransaction();

    try {
        DB::table('paten_verifs')
            ->where('id', $ref)
            ->update([
                'deskripsi_singkat_prototipe' => $text,
                'updated_at' => now(),
            ]);

        // update session lama yang sama
        $sessionKeySpecific = "hakpaten.isiform.$ref";
        $sessionKeyGlobal   = "hakpaten.isiform";

        $existingSession = session($sessionKeySpecific);
        if (!$existingSession) {
            $existingSession = session($sessionKeyGlobal, []);
        }

        $existingSession['deskripsi_singkat_prototipe'] = $text;
        session([$sessionKeySpecific => $existingSession]);
        session(['edit_ref_id' => $ref]);

        // cari revisi aktif admin untuk deskripsi singkat
        $activeReq = DB::table('revisions')
            ->where('type', 'paten')
            ->where('ref_id', $ref)
            ->where('doc_key', 'deskripsi_singkat_prototipe')
            ->where('from_role', 'admin')
            ->where('state', 'requested')
            ->orderByDesc('id')
            ->first();

        if ($activeReq) {
            // tutup request admin
            DB::table('revisions')
                ->where('id', $activeReq->id)
                ->update([
                    'state' => 'closed',
                    'is_read_admin' => 1,
                    'is_read_pemohon' => 1,
                    'updated_at' => now(),
                ]);

            // insert riwayat submitted dari pemohon
            DB::table('revisions')->insert([
                'type' => 'paten',
                'ref_id' => $ref,
                'doc_key' => 'deskripsi_singkat_prototipe',
                'from_role' => 'pemohon',
                'state' => 'submitted',
                'note' => null,
                'file_path' => null,
                'pemohon_file_path' => null,
                'pemohon_file_name' => null,
                'pemohon_uploaded_at' => now(),
                'pemohon_text' => $text,
                'is_read_admin' => 0,
                'is_read_pemohon' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        return redirect()
            ->route('pemohon.dashboard')
            ->with('success', 'Deskripsi Singkat Prototipe berhasil diperbarui.');
    } catch (\Throwable $e) {
        DB::rollBack();
        throw $e;
    }
}
    public function downloadTandaTerima(Request $request)
    {
        $pemohon = Auth::guard('pemohon')->user();
        if (!$pemohon) return redirect()->route('pemohon.login.form');

        $kode = $pemohon->kode_unik
            ?? $pemohon->no_pendaftaran
            ?? $pemohon->kode
            ?? null;

        if (!$kode) abort(403, 'Kode unik pemohon tidak ditemukan.');

        // ✅ konsisten ambil TERBARU
        $paten = PatenVerif::where('no_pendaftaran', $kode)->latest('id')->first();
        $cipta = DB::table('hak_cipta_verifs')->where('no_pendaftaran', $kode)->orderByDesc('id')->first();

        if (!$paten) {
            $cipta = DB::table('hak_cipta_verifs')->where('no_pendaftaran', $kode)->orderByDesc('id')->first();
        }

        if (!$paten && !$cipta) {
            abort(404, 'Data pengajuan tidak ditemukan.');
        }

        $type  = $paten ? 'paten' : 'cipta';
        $refId = $paten ? $paten->id : $cipta->id;
        $source = $paten ?: $cipta;

        $sv = DB::table('status_verifikasi')
            ->where('ref_type', $type)
            ->where('ref_id', $refId)
            ->orderByDesc('id')
            ->first();


        $status = strtolower($sv->status ?? 'terkirim');
        if ($status !== 'approve') {
            abort(403, 'Tanda terima hanya bisa didownload setelah status APPROVE.');
        }

        if (!$sv || empty($sv->tanda_terima_pdf)) {
            abort(404, 'File tanda terima belum tersedia (kolom tanda_terima_pdf masih kosong).');
        }

        if (!Storage::disk('public')->exists($sv->tanda_terima_pdf)) {
            abort(404, 'File tanda terima tidak ditemukan di storage/public.');
        }

        $noPendaftaran = trim((string)($source->no_pendaftaran ?? ''));
        $safeNo = preg_replace('/[^A-Za-z0-9_-]/', '', $noPendaftaran);

        $downloadName = $type === 'paten'
            ? "Tanda Terima Paten {$safeNo}.pdf"
            : "Tanda Terima Hak Cipta {$safeNo}.pdf";

        return response()->download(
            storage_path('app/public/' . $sv->tanda_terima_pdf),
            $downloadName
        );
    }

public function downloadDokumenAwal(Request $request, string $type, int $ref, string $key)
{
    $pemohon = Auth::guard('pemohon')->user();
    if (!$pemohon) abort(403);

    if (!in_array($type, ['paten','cipta'], true)) abort(404);

    // jangan izinin download untuk field link (bukan file)
    if ($key === 'link_ciptaan') abort(404);

    $path = null;
    $downloadName = null;

    // 1) coba dari verifikasi_dokumens dulu
    $doc = VerifikasiDokumen::where([
        'ref_type' => $type,
        'ref_id'   => $ref,
        'doc_key'  => $key,
    ])->orderByDesc('id')->first();

    if ($doc && !empty($doc->pemohon_file_path)) {
        $path = $doc->pemohon_file_path;
        $downloadName = $doc->pemohon_file_name ?: basename($path);
    } else {
        // 2) fallback: ambil dari tabel sumber (hak_cipta_verifs / paten_verifs)
        $source = $type === 'cipta'
            ? DB::table('hak_cipta_verifs')->where('id', $ref)->first()
            : PatenVerif::where('id', $ref)->first();

        if (!$source) abort(404, 'Data pengajuan tidak ditemukan.');

        $path = data_get($source, $key);
        if (!$path) abort(404, 'File tidak ditemukan.');

        $downloadName = basename($path);
    }

    // normalisasi path kalau ada "storage/"
    $path = ltrim((string)$path, '/');
    $path = preg_replace('#^storage/#', '', $path);

    if (!Storage::disk('public')->exists($path)) abort(404, 'File tidak ada di storage.');

    // amanin nama file
    $downloadName = preg_replace('/[^a-zA-Z0-9.\-_ ()]/', '_', (string)$downloadName);

    $full = Storage::disk('public')->path($path);

    // paksa attachment biar download (PDF gak kebuka inline)
    return response()->download($full, $downloadName, [
        'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
    ]);
}
public function uploadRevisi(Request $request, int $revisionId)
{
    $pemohon = Auth::guard('pemohon')->user();
    if (!$pemohon) return redirect()->route('pemohon.login.form');

    $request->validate([
        'file' => 'required|file|max:5120',
    ]);

    $reqRow = DB::table('revisions')->where('id', $revisionId)->first();
    if (!$reqRow) abort(404, 'Revisi tidak ditemukan.');

    if (($reqRow->from_role ?? null) !== 'admin' || ($reqRow->state ?? null) !== 'requested') {
        return back()->with('success', 'Revisi ini sudah ditutup / tidak valid untuk upload.');
    }

    DB::beginTransaction();
    try {
        $file = $request->file('file');
        $original = $file->getClientOriginalName();
        $safeName = preg_replace('/[^a-zA-Z0-9.\-_ ()]/', '_', $original);

        $dir = "revisi/{$reqRow->type}/{$reqRow->ref_id}/{$reqRow->doc_key}";

        $finalName = $safeName;
        $counter = 1;
        $base = pathinfo($finalName, PATHINFO_FILENAME);
        $ext  = pathinfo($finalName, PATHINFO_EXTENSION);

        while (Storage::disk('public')->exists($dir.'/'.$finalName)) {
            $finalName = "{$base}_{$counter}" . ($ext ? ".{$ext}" : "");
            $counter++;
        }

        $path = $file->storeAs($dir, $finalName, 'public');
        $fullPathForDb = 'storage/' . $path; // ✅ Path lengkap untuk tabel pendaftaran
// 🚀 LOGIC REPLACING (SINKRONISASI KE TABEL UTAMA)
$tableName = ($reqRow->type === 'paten') ? 'paten_verifs' : 'hak_cipta_verifs';

// ✅ TENTUKAN NAMA KOLOM ASLI DI DATABASE
$targetColumn = $reqRow->doc_key;

// 🔥 KUNCINYA DI SINI TIK:
// Kalau doc_key adalah 'skema_tkt', belokkan ke kolom 'skema_tkt_template_path'
if ($reqRow->type === 'paten' && $reqRow->doc_key === 'skema_tkt') {
    $targetColumn = 'skema_tkt_template_path';
}

// Menimpa kolom file lama di tabel pendaftaran dengan file baru hasil revisi
DB::table($tableName)->where('id', $reqRow->ref_id)->update([
    $targetColumn => $fullPathForDb, // ✅ Sekarang pake targetColumn yang pinter
    'updated_at'  => now(),
]);
        // ========================================================

        // 1) tutup request admin
        DB::table('revisions')->where('id', $revisionId)->update([
            'state'          => 'closed',
            'is_read_admin'  => 1,
            'is_read_pemohon'=> 1,
            'updated_at'     => now(),
        ]);

        // 2) insert submitted pemohon (History)
        DB::table('revisions')->insert([
            'type'               => $reqRow->type,
            'ref_id'             => $reqRow->ref_id,
            'doc_key'            => $reqRow->doc_key,
            'from_role'          => 'pemohon',
            'state'              => 'submitted',
            'note'               => null,
            'file_path'          => null,
            'pemohon_file_path'  => $path,
            'pemohon_file_name'  => $finalName,
            'pemohon_uploaded_at'=> now(),
            'is_read_admin'      => 0,
            'is_read_pemohon'    => 1,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        DB::commit();
        return back()->with('success', 'File revisi berhasil diupload dan dokumen pendaftaran telah diperbarui.');
    } catch (\Throwable $e) {
        DB::rollBack();
        throw $e;
    }
}
}