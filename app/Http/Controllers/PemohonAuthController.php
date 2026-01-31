<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemohon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\VerifikasiDokumen;

class PemohonAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        return view('pemohon.login');
    }

    // INI YANG DIPANGGIL DARI HALAMAN HASIL SUBMIT (TOMBOL LOGIN)
   public function claim($kode)
{
    $isPaten = DB::table('paten_verifs')->where('no_pendaftaran', $kode)->exists();
    $isCipta = DB::table('hak_cipta_verifs')->where('no_pendaftaran', $kode)->exists();

    if (!$isPaten && !$isCipta) {
        return redirect()->route('pemohon.login.form')->with('error', 'Kode tidak valid.');
    }

    session(['prefill_kode' => $kode]);

    $pemohon = Pemohon::where('kode_unik', $kode)->first();

    // kalau belum ada -> create
    if (!$pemohon) {
        $pemohon = Pemohon::create([
            'kode_unik' => $kode,
            'password'  => Hash::make($kode),
        ]);

        session()->flash('prefill_password', $kode);

        return redirect()->route('pemohon.login.form')
            ->with('success', 'Akun berhasil dibuat. Username & password = kode pengajuan. Silakan login');
    }

    // ✅ kalau akun SUDAH ADA tapi passwordnya BUKAN kode (akun lama/random),
    // set password = kode SEKALI supaya konsisten sama aturan kamu
    if (!Hash::check($kode, $pemohon->password)) {
        $pemohon->password = Hash::make($kode);
        $pemohon->save();
    }

    // biar password muncul juga
    session()->flash('prefill_password', $kode);

    return redirect()->route('pemohon.login.form')
        ->with('success', 'Akun berhasil dibuat. Username & password = kode pengajuan. Silakan login');
}


    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // cek akun pemohon (tabel pemohons)
        $pemohon = Pemohon::where('kode_unik', $request->username)->first();

        if (!$pemohon) {
            return back()->withErrors([
                'username' => 'Akun belum dibuat. Klik Login dari halaman hasil pengajuan.'
            ])->withInput();
        }

        if (Auth::guard('pemohon')->attempt([
            'kode_unik' => $request->username,
            'password'  => $request->password,
        ])) {
            $request->session()->regenerate();
            return redirect()->route('pemohon.dashboard');
        }

        return back()->withErrors([
            'password' => 'Password salah'
        ])->withInput();
    }

   public function dashboard()
    {
        $pemohon = Auth::guard('pemohon')->user();
        if (!$pemohon) return redirect()->route('pemohon.login.form');

        // ambil paten_verifs berdasarkan kode unik pemohon
        $paten = DB::table('paten_verifs')
            ->where('no_pendaftaran', $pemohon->kode_unik)
            ->first();

        if (!$paten) {
            return redirect()->route('pemohon.login.form')
                ->with('error', 'Data pengajuan tidak ditemukan.');
        }

        // ambil status_verifikasi dulu
        $sv = DB::table('status_verifikasi')
            ->where('ref_type', 'paten')
            ->where('ref_id', $paten->id)
            ->first();

        // ✅ status diseragamkan lowercase (4 status)
        $status = strtolower($sv->status ?? 'terkirim'); // terkirim|proses|revisi|approve
        $activeStatus = $status;

        $updatedAt  = $sv?->updated_at ? Carbon::parse($sv->updated_at) : Carbon::now();
        $updatedStr = $updatedAt->format('d M Y');

        // timeline steps
        $steps = [
            ['key' => 'terkirim', 'label' => 'TERKIRIM', 'updated_at' => in_array($status, ['terkirim','proses','revisi','approve']) ? $updatedStr : '-'],
            ['key' => 'proses',   'label' => 'PROSES',   'updated_at' => in_array($status, ['proses','revisi','approve']) ? $updatedStr : '-'],
            ['key' => 'revisi',   'label' => 'REVISI',   'updated_at' => $status === 'revisi' ? $updatedStr : '-'],
            ['key' => 'approve',  'label' => 'APPROVE',  'updated_at' => $status === 'approve' ? $updatedStr : '-'],
        ];

        // ambil riwayat revisions (untuk box riwayat)
        $revisions = DB::table('revisions')
            ->where('type', 'paten')
            ->where('ref_id', $paten->id)
            ->orderByDesc('created_at')
            ->get();

        // ambil revisi per dokumen + grouping revisions per doc_key (hanya kalau status revisi)
        $revisiDocs = collect();
        $revRowsByDoc = collect();

        if ($status === 'revisi') {
            $revisiDocs = VerifikasiDokumen::where([
                    'ref_type' => 'paten',
                    'ref_id'   => $paten->id,
                ])
                ->where('status', 'revisi')
                ->orderBy('doc_key')
                ->get();

            $revRowsByDoc = DB::table('revisions')
                ->where('type', 'paten')
                ->where('ref_id', $paten->id)
                ->orderByDesc('created_at')
                ->get()
                ->groupBy('doc_key');
        }

        return view('pemohon.dashboard', compact(
            'pemohon',
            'paten',
            'sv',
            'steps',
            'activeStatus',
            'status',
            'revisions',
            'revisiDocs',
            'revRowsByDoc'
        ));
    }

    public function logout(Request $request)
    {
        Auth::guard('pemohon')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pemohon.login.form');
    }

    public function showPreChangePassword()
    {
        if (!session()->has('prefill_kode')) {
            return redirect()->route('pemohon.login.form')
                ->with('error', 'Silakan klik Login dari halaman hasil submit dulu.');
        }
        return view('pemohon.gantipassword');
    }

    public function storePreChangePassword(Request $request)
    {
        $request->validate([
            'password' => ['required','min:8','confirmed'],
        ]);

        $kode = session('prefill_kode');
        if (!$kode) {
            return redirect()->route('pemohon.login.form')
                ->with('error', 'Session habis. Klik Login dari halaman hasil submit lagi.');
        }

        $pemohon = Pemohon::where('kode_unik', $kode)->first();
        if (!$pemohon) {
            return redirect()->route('pemohon.login.form')
                ->with('error', 'Akun tidak ditemukan.');
        }

        $pemohon->password = Hash::make($request->password);
        $pemohon->save();

        // supaya balik ke login, password baru langsung keisi juga:
        session()->flash('prefill_password', $request->password);

        return redirect()->route('pemohon.login.form')
            ->with('success', 'Password berhasil diubah. Silakan login.');
    }

    public function uploadRevisi(Request $request, int $id)
    {
        $pemohon = Auth::guard('pemohon')->user();
        if (!$pemohon) return redirect()->route('pemohon.login.form');

        $request->validate([
            'file' => ['required','file','max:10240'],
        ]);

        $rev = DB::table('revisions')->where('id',$id)->first();
        if (!$rev) return back()->withErrors(['file' => 'Data revisi tidak ditemukan.']);

        // upload file pemohon
        $path = $request->file('file')->store('revisi/pemohon', 'public');

        DB::table('revisions')->where('id',$id)->update([
            'pemohon_file_path' => $path,   // ✅ INI YANG BENER
            'state' => 'submitted',
            'is_read_admin' => 0,           // notif admin
            'is_read_pemohon' => 1,
            'updated_at' => now(),
        ]);

        DB::table('status_verifikasi')->updateOrInsert(
            ['ref_type' => $rev->type, 'ref_id' => $rev->ref_id],
            ['status' => 'revisi', 'updated_at' => now(), 'created_at' => now()]
        );

        return back()->with('success', 'Revisi berhasil diupload. Menunggu pengecekan admin.');
    }
}
