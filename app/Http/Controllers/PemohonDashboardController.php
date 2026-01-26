<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PemohonDashboardController extends Controller
{
    public function index()
    {
        // dummy data (nanti ganti dari DB)
        $pemohon = [
            'username' => 'EC00202600002',
            'fakultas' => 'Fakultas Ekonomi dan Bisnis',
            'kategori' => 'Hak Cipta',
            'jenis'    => 'Rekaman Video',
            'judul'    => 'Judul Ciptaan Contoh',
        ];

        // status aktif (contoh: diterima)
        $activeStatus = 'diterima';

        // timeline steps (urutan)
        $steps = [
            ['key' => 'terkirim',  'label' => 'TERKIRIM',  'updated_at' => '2026-01-20 00:56:02'],
            ['key' => 'diproses',  'label' => 'DIPROSES',  'updated_at' => '2026-01-20 00:56:02'],
            ['key' => 'revisi',    'label' => 'REVISI',    'updated_at' => '2026-01-20 00:56:02'],
            ['key' => 'diterima',  'label' => 'DITERIMA',  'updated_at' => '2026-01-20 00:56:02'],
            ['key' => 'ditolak',   'label' => 'DITOLAK',   'updated_at' => '2026-01-20 00:56:02'],
        ];

        return view('pemohon.dashboard', compact('pemohon', 'steps', 'activeStatus'));
    }
}
