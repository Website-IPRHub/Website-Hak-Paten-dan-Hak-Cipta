<?php

namespace App\Http\Middleware;

use App\Models\HakCipta;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;


class EnsureCiptaSequence
{
    public function handle(Request $request, Closure $next)
    {
        
        $ciptaId = session('cipta_id');
        if (!$ciptaId) {
            return redirect()->route('hakcipta')
                ->with('error', 'Mulai dari step pertama dulu.');
        }

        $cipta = HakCipta::find($ciptaId);
        if (!$cipta) {
            session()->forget('cipta_id');
            return redirect()->route('hakcipta')
                ->with('error', 'Data cipta tidak ditemukan. Mulai ulang.');
        }

        // route name -> kolom DB
        $steps = [
            'hakcipta.permohonanpendaftaran' => 'surat_permohonan',
            'hakcipta.suratpernyataan'       => 'surat_pernyataan',
            'hakcipta.pengalihanhak'         => 'surat_pengalihan',
            'hakcipta.scanktp'               => 'scan_ktp',
            'hakcipta.tandaterima'           => 'tanda_terima',
            'hakcipta.hasilciptaan'          => 'hasil_ciptaan',
            'hakcipta.linkciptaan'           => null, // opsional / terakhir
        ];

        $current = $request->route()?->getName();
        if (!$current) return $next($request);

        // kalau route bukan bagian step, skip
        if (!array_key_exists($current, $steps)) {
            return $next($request);
        }

        // cari step pertama yang belum beres
        $firstIncompleteRoute = null;

        foreach ($steps as $routeName => $requiredColumn) {
            if ($requiredColumn === null) continue;

            $val = trim((string)($cipta->{$requiredColumn} ?? ''));
            $done = $val !== '';

            if (!$done) {
                $firstIncompleteRoute = $routeName;
                break;
            }
        }

        // kalau ada yg belum beres & user buka step lain -> balikin ke yg pertama belum beres
        if ($firstIncompleteRoute && $current !== $firstIncompleteRoute) {
            return redirect()->route($firstIncompleteRoute)
                ->with('error', 'Selesaikan step sebelumnya terlebih dahulu.');
        }

        $cipta = HakCipta::find($ciptaId);

        View::share('cipta', $cipta);


        return $next($request);
    }
}
