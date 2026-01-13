<?php

namespace App\Http\Middleware;

use App\Models\Paten;
use Closure;
use Illuminate\Http\Request;

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

        // alur dokumen
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
        if (!$current) return $next($request);

        if (!array_key_exists($current, $steps)) {
            return $next($request);
        }

        $firstIncompleteRoute = null;

        foreach ($steps as $routeName => $requiredColumn) {
            if ($requiredColumn === null) { // buat yang opsional
                continue;
            }

            $val = (string) ($paten->{$requiredColumn} ?? '');
            $val = trim($val);

            // harus ada string path (minimal ada "/")
            $done = $val !== '';

            if (!$done) {
                $firstIncompleteRoute = $routeName;
                break;
            }
        }

        // Kalau ada step yang belum selesai dan user buka step lain (yang bukan step itu), akan redirect
        if ($firstIncompleteRoute && $current !== $firstIncompleteRoute) {
            return redirect()->route($firstIncompleteRoute)
                ->with('error', 'Selesaikan step sebelumnya dulu.');
        }

        return $next($request);
    }
}
