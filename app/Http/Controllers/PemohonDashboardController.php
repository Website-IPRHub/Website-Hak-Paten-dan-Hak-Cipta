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
        $kode = $pemohon->kode_unik;

        // cari pengajuan paten dulu
        $paten = PatenVerif::where('no_pendaftaran', $kode)->first();

        // kalau bukan paten, coba cipta
        $cipta = null;
        if (!$paten) {
            $cipta = HakCipta::where('no_pendaftaran', $kode)->first();
        }

        if (!$paten && !$cipta) {
            return redirect()->route('pemohon.login.form')
                ->with('error', 'Data pengajuan tidak ditemukan untuk kode ini.');
        }

        // tentukan tipe & id ref untuk status_verifikasi
        $type = $paten ? 'paten' : 'cipta';
        $refId = $paten ? $paten->id : $cipta->id;

        // ambil status dari tabel status_verifikasi (yang admin ubah)
        $sv = DB::table('status_verifikasi')
            ->where('ref_type', $type)
            ->where('ref_id', $refId)
            ->first();

        $status = strtolower($sv->status ?? 'terkirim'); // terkirim|proses|revisi|diterima|ditolak
        $activeStatus = $status;

        // ambil last updated sesuai updated_at status_verifikasi (biar tanggalnya bener)
        $updatedAt = $sv?->updated_at ? Carbon::parse($sv->updated_at) : Carbon::now();
        $updatedStr = $updatedAt->format('d M Y');

        // timeline steps (yang dipakai warna & aktif)
        $steps = [
            ['key' => 'terkirim', 'label' => 'TERKIRIM', 'updated_at' => in_array($status, ['Terkirim','Proses','Revisi','Approve']) ? $updatedStr : '-'],
            ['key' => 'proses',   'label' => 'PROSES',   'updated_at' => in_array($status, ['proses','Revisi','Approve']) ? $updatedStr : '-'],
            ['key' => 'revisi',   'label' => 'REVISI',   'updated_at' => $status === 'Revisi' ? $updatedStr : '-'],
            ['key' => 'diterima', 'label' => 'APPROVE',  'updated_at' => $status === 'Approve' ? $updatedStr : '-'],
        ];

        // ambil detail revisi dokumen (catatan + file admin) dari verifikasi_dokumen
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

        // payload ringkas untuk view (biar blade enak)
        $pengajuan = (object) [
            'kode'     => $kode,
            'kategori' => $type === 'paten' ? 'Paten' : 'Hak Cipta',
            'judul'    => $paten ? ($paten->judul_paten ?? '-') : ($cipta->judul_cipta ?? '-'),
            'jenis'    => $paten ? ($paten->jenis_paten ?? '-') : ($cipta->jenis_cipta ?? '-'),
            'email'    => $paten ? ($paten->email ?? '-') : ($cipta->email ?? '-'),
            'no_hp'    => $paten ? ($paten->no_hp ?? '-') : ($cipta->no_hp ?? '-'),
            'id'       => $refId,
            'type'     => $type,
        ];

        return view('pemohon.dashboard', compact(
            'pemohon',
            'pengajuan',
            'sv',
            'steps',
            'activeStatus',
            'revisiDocs'
        ));
    }
    public function downloadTandaTerima(Request $request)
    {
        $pemohon = Auth::guard('pemohon')->user();
        if (!$pemohon) return redirect()->route('pemohon.login.form');

        $kode = $pemohon->kode_unik;

        // cari pengajuan paten dulu
        $paten = PatenVerif::where('no_pendaftaran', $kode)->first();

        // kalau bukan paten, coba cipta
        $cipta = null;
        if (!$paten) {
            $cipta = HakCipta::where('no_pendaftaran', $kode)->first();
        }

        if (!$paten && !$cipta) {
            abort(404, 'Data pengajuan tidak ditemukan.');
        }

        $type  = $paten ? 'paten' : 'cipta';
        $refId = $paten ? $paten->id : $cipta->id;

        $sv = DB::table('status_verifikasi')
            ->where('ref_type', $type)
            ->where('ref_id', $refId)
            ->first();

        // wajib approve
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
