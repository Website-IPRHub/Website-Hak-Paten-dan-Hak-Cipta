<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PemohonAuthController extends Controller
{
    public function showLogin()
    {
        return view('pemohon.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
        ]);

        // ✅ DUMMY MODE: username/password apa aja boleh (asal diisi)
        $pemohon = [
            'username' => $request->username,
            'fakultas' => 'Fakultas Teknik',
            'kategori' => 'Paten',
            'jenis'    => 'Paten Sederhana',
            'judul'    => 'Contoh Judul Paten',
        ];

        $request->session()->put('pemohon', $pemohon);
        $request->session()->regenerate();

        return redirect()->route('pemohon.dashboard');
    }

    public function dashboard(Request $request)
    {
        // ✅ Proteksi simpel: kalau belum "login", balik ke login
        if (!$request->session()->has('pemohon')) {
            return redirect()->route('pemohon.login.form');
        }

        $pemohon = $request->session()->get('pemohon');

        // ✅ step tracker sesuai blade (key harus sama kayak yang dipakai JS)
        $steps = [
            ['key' => 'terkirim', 'label' => 'TERKIRIM', 'updated_at' => '26 Jan 2026'],
            ['key' => 'diproses', 'label' => 'PROSES',   'updated_at' => '26 Jan 2026'],
            ['key' => 'revisi',   'label' => 'REVISI',  'updated_at' => '-'],
            ['key' => 'diterima', 'label' => 'APPROVE',          'updated_at' => '-'],
        ];

        // ✅ status aktif (buat highlight step)
        $activeStatus = 'diproses'; // bisa ganti: terkirim / diproses / revisi / diterima / ditolak

        return view('pemohon.dashboard', compact('pemohon','steps','activeStatus'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget('pemohon');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pemohon.login.form');
    }
}
