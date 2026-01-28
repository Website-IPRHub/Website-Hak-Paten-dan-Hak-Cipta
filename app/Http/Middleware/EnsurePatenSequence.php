<?php

namespace App\Http\Middleware;

use App\Models\Paten;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EnsurePatenSequence
{
    public function handle(Request $request, Closure $next)
    {
        $patenId = session('paten_id');

        if (!$patenId) {
            return redirect()->route('hakpaten')
                ->with('error', 'Mulai dari step pertama dulu.');
        }

        $paten = Paten::find($patenId);

        if (!$paten) {
            session()->forget('paten_id');
            return redirect()->route('hakpaten')
                ->with('error', 'Data paten tidak ditemukan. Mulai ulang.');
        }

        // 🔥 SHARE KE SEMUA VIEW
        View::share('paten', $paten);

        $steps = [
            'draftpaten'           => 'draft_paten',
            'formulirpermohonan'   => 'form_permohonan',
            'kepemilikaninvensi'   => 'surat_kepemilikan',
            'pengalihanhak'        => 'surat_pengalihan',
            'scanktp'              => 'scan_ktp',
            'tandaterima'          => 'tanda_terima',
            'uploadgambarprototipe'=> null,
            'deskripsiproduk'      => null,
        ];

        $current = $request->route()?->getName();
        if (!$current || !array_key_exists($current, $steps)) {
            return $next($request);
        }

        foreach ($steps as $routeName => $column) {
            if ($column === null) continue;

            $done = trim((string)($paten->{$column} ?? '')) !== '';

            if (!$done) {
                if ($current !== $routeName) {
                    return redirect()->route($routeName)
                        ->with('error', 'Selesaikan step sebelumnya dulu.');
                }
                break;
            }
        }

        return $next($request);
    }
}
