<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paten;
use App\Models\HakCipta;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // wajib login admin
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }

        $name = $request->session()->get('admin_name', 'Admin');

        // ambil tab dari URL: ?tab=stats / cipta / paten / status
        $tab = $request->query('tab', 'stats');

        // =========================
        // ✅ STATISTIK REAL (DARI DB)
        // =========================
        $totalPaten = Paten::count();
        $totalCipta = HakCipta::count();
        $totalAll   = $totalPaten + $totalCipta;

        // chart paten berdasarkan jenis_paten
        $patenJenis = Paten::select('jenis_paten', DB::raw('count(*) as total'))
            ->groupBy('jenis_paten')
            ->pluck('total', 'jenis_paten')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        // chart cipta berdasarkan jenis_cipta
        $ciptaJenis = HakCipta::select('jenis_cipta', DB::raw('count(*) as total'))
            ->groupBy('jenis_cipta')
            ->pluck('total', 'jenis_cipta')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        // =========================
        // data default kosong dulu
        // =========================
        $dataPaten  = collect();
        $dataCipta  = collect();
        $dataStatus = collect();

        // query hanya jika dibutuhkan
        if ($tab === 'paten') {
            $dataPaten = Paten::orderBy('id', 'desc')->get();
        }

        if ($tab === 'cipta') {
            $dataCipta = HakCipta::orderBy('id', 'desc')->get();
        }

        // gabungkan paten + cipta untuk tab status
        if ($tab === 'status') {
            $paten = Paten::select('id', 'no_pendaftaran', 'status', 'judul_paten')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($r) {
                    $r->type  = 'paten';
                    $r->judul = $r->judul_paten; // supaya blade status bisa pakai $row->judul
                    return $r;
                });

            $cipta = HakCipta::select('id', 'no_pendaftaran', 'status', 'judul_cipta')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($r) {
                    $r->type  = 'cipta';
                    $r->judul = $r->judul_cipta;
                    return $r;
                });

            $dataStatus = $paten->concat($cipta)->sortByDesc('id')->values();
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

    public function updateStatusPaten(Request $request, $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }

        $request->validate([
            'status' => 'required|in:terkirim,proses,revisi,diterima,ditolak',
        ]);

        $row = Paten::findOrFail($id);
        $row->status = $request->input('status');
        $row->save();

        // balik ke tab paten biar enak
        return redirect()->route('admin.dashboard', ['tab' => 'paten'])
            ->with('success', 'Status paten berhasil diupdate.');
    }

    public function updateStatusCipta(Request $request, $id)
    {
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login.form');
        }

        $request->validate([
            'status' => 'required|in:terkirim,proses,revisi,diterima,ditolak',
        ]);

        $row = HakCipta::findOrFail($id);
        $row->status = $request->input('status');
        $row->save();

        // balik ke tab cipta biar enak
        return redirect()->route('admin.dashboard', ['tab' => 'cipta'])
            ->with('success', 'Status hak cipta berhasil diupdate.');
    }
}
