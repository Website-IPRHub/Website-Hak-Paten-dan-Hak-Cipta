<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paten;
use App\Models\HakCipta;

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

        //default kosong dulu
        $dataPaten = collect();
        $dataCipta = collect();
        $dataStatus = collect();

        //query hanya jika dibutuhkan
        if ($tab === 'paten') {
            $dataPaten = Paten::orderBy('id', 'desc')->get();
        }

        if ($tab === 'cipta') {
            $dataCipta = HakCipta::orderBy('id', 'desc')->get();
        }

        // gabungkan paten + cipta untuk tab status
        if ($tab === 'status') {
            $paten = Paten::select('id', 'no_pendaftaran', 'status')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($r) {
                    $r->type = 'paten';
                    return $r;
                });

            $cipta = HakCipta::select('id', 'no_pendaftaran', 'status')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($r) {
                    $r->type = 'cipta';
                    return $r;
                });

            $dataStatus = $paten->concat($cipta)->sortByDesc('id')->values();
        }

        return view('admin.dashboard', compact('name', 'tab', 'dataPaten', 'dataCipta', 'dataStatus'));
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
