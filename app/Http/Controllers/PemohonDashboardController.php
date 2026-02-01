<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PatenVerif;
use App\Models\HakCipta;
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
            ->orderByDesc('created_at')
            ->first();

        $cipta = DB::table('hak_cipta_verifs')
            ->where('no_pendaftaran', $kode)
            ->orderByDesc('created_at')
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


        // ✅ status_verifikasi: ambil yang sesuai pengajuan terbaru
            $sv = DB::table('status_verifikasi')
                ->where('ref_type', $type)
                ->where('ref_id', $refId)
                ->first();

        // ambil status_verifikasi TANPA filter created_at
            $svRow = DB::table('status_verifikasi')
                ->where('ref_type', $type)
                ->where('ref_id', $refId)
                ->first();

           // ✅ status_verifikasi: cukup BACA (jangan update database tiap buka dashboard)
            $sv = DB::table('status_verifikasi')
                ->where('ref_type', $type)
                ->where('ref_id', $refId)
                ->orderByDesc('id') // ambil row status terbaru
                ->first();

            $status = strtolower($sv->status ?? 'terkirim');
            $activeStatus = $status;

            $updatedAt = $sv?->updated_at
                ? Carbon::parse($sv->updated_at)
                : (isset($source->created_at) ? Carbon::parse($source->created_at) : Carbon::now());

            $updatedStr = $updatedAt->format('d M Y');

            // refresh
            $sv = DB::table('status_verifikasi')
                ->where('ref_type', $type)
                ->where('ref_id', $refId)
                ->first();


        $status = strtolower($sv->status ?? 'terkirim');
        $activeStatus = $status;

        $updatedAt  = $sv?->updated_at ? Carbon::parse($sv->updated_at) : Carbon::now();
        $updatedStr = $updatedAt->format('d M Y');

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

            if ($stepRank < $currentRank) {
                $cls = 'is-done';
            } elseif ($stepRank === $currentRank) {
                $cls = 'is-run';
            } else {
                $cls = 'is-todo';
            }

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

        $revRowsByDoc = [];

        if ($status === 'revisi') {
            $revRowsByDoc = DB::table('revisions')
                ->where('type', $type)
                ->where('ref_id', $refId)
                ->where('from_role', 'admin')
                ->whereIn('state', ['requested', 'submitted']) // requested = minta revisi, submitted = sudah diupload pemohon
                ->orderByDesc('id')
                ->get()
                ->groupBy('doc_key')
                ->map(fn($rows) => [$rows->first()]) // ambil row terbaru per doc_key
                ->toArray();
        }

        // helper pick
        $pick = function ($obj, array $keys, $default = '-') {
            foreach ($keys as $k) {
                if (is_object($obj) && isset($obj->$k) && $obj->$k !== null && $obj->$k !== '') {
                    return $obj->$k;
                }
                if (is_array($obj) && isset($obj[$k]) && $obj[$k] !== null && $obj[$k] !== '') {
                    return $obj[$k];
                }
            }
            return $default;
        };

        // ✅ pengajuan (INI WAJIB ADA, biar $akun bisa pakai $pengajuan)
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

        // ✅ inventors array
 // ✅ inventors array
        $inventorsArr = [];
        $inventorList = '-';

        if ($type === 'paten') {
            $inventorsRaw = $source->inventors ?? null;
            $inventorsArr = is_string($inventorsRaw) ? (json_decode($inventorsRaw, true) ?? []) : ($inventorsRaw ?? []);

            $inventorsArr = collect($inventorsArr)->map(function ($i) {
                $nama   = trim((string)($i['nama'] ?? ''));
                $status = trim((string)($i['status'] ?? ''));

                $nohp   = $i['no_hp'] ?? ($i['no hp'] ?? ($i['hp'] ?? null));
                $nohp   = trim((string)($nohp ?? ''));

                $email  = trim((string)($i['email'] ?? ''));

                $fak = $i['fakultas'] ?? ($i['fakultass'] ?? null);
                $fak = trim((string)($fak ?? ''));

                return [
                    'nama'     => $nama ?: '-',
                    'status'   => $status ?: '-',
                    'email'    => $email ?: '-',
                    'no_hp'    => $nohp ?: '-',
                    'fakultas' => $fak ?: '-',
                ];
            })->values()->all();

            if (count($inventorsArr) > 0) {
                $inventorList = collect($inventorsArr)
                    ->map(fn($i) => trim($i['nama'].' ('.$i['status'].')'))
                    ->filter()
                    ->implode(', ');
            }

        } else {
            // ✅ HAK CIPTA: ambil inventors dari DB kalau ada (JSON)
            $inventorsRaw =
                $source->inventors
                ?? $source->pencipta
                ?? $source->inventor
                ?? null;

            $inventorsArr = is_string($inventorsRaw)
                ? (json_decode($inventorsRaw, true) ?? [])
                : ($inventorsRaw ?? []);

            $inventorsArr = collect($inventorsArr)->map(function ($i) use ($pick, $source, $pemohon) {
                $nama   = trim((string)($i['nama'] ?? ''));
                $status = trim((string)($i['status'] ?? $i['kategori'] ?? $i['peran'] ?? '')); // ambil mahasiswa/dosen

                $nohp   = $i['no_hp'] ?? ($i['no hp'] ?? ($i['hp'] ?? null));
                $email  = trim((string)($i['email'] ?? ''));
                $fak    = $i['fakultas'] ?? null;

                return [
                    'nama'     => $nama ?: '-',
                    'status'   => $status ?: '-',   // ✅ bukan "Pencipta"
                    'email'    => $email ?: '-',
                    'no_hp'    => trim((string)($nohp ?? '-')),
                    'fakultas' => trim((string)($fak ?? '-')),
                ];
            })->values()->all();

            // fallback kalau gak ada JSON inventors
            if (count($inventorsArr) === 0) {
                $nama  = $pick($source, ['nama_pencipta', 'nama', 'nama_pemohon'], $pick($pemohon, ['nama']));
                $email = $pick($source, ['email'], $pick($pemohon, ['email']));
                $nohp  = $pick($source, ['no_hp', 'hp', 'nomor_hp'], $pick($pemohon, ['no_hp', 'hp', 'nomor_hp']));
                $fak   = $pick($source, ['fakultas'], $pick($pemohon, ['fakultas']));
                $statusPemohon = $pick($source, ['status', 'kategori'], $pick($pemohon, ['kategori'])); // mahasiswa/dosen

                $inventorsArr = [[
                    'nama'     => $nama ?: '-',
                    'status'   => $statusPemohon ?: '-',  // ✅ ambil dari data, bukan Pencipta
                    'email'    => $email ?: '-',
                    'no_hp'    => $nohp ?: '-',
                    'fakultas' => $fak ?: '-',
                ]];
            }

            $inventorList = collect($inventorsArr)
                ->map(fn($i) => trim($i['nama'].' ('.$i['status'].')'))
                ->filter()
                ->implode(', ');
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

        // ✅ INI YANG KAMU KELEWAT: RETURN VIEW
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
            'revRowsByDoc' // kalau blade kamu butuh $source
        ));
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
        ->where('created_at', '>=', $source->created_at)
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

        return response()->download(
            storage_path('app/public/' . $sv->tanda_terima_pdf)
        );
    }
}
