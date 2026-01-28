<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paten extends Model
{
    protected $table = 'paten';

    protected $fillable = [
        'no_pendaftaran',
        'jenis_paten',
        'judul_paten',

        // kalau tabel kamu punya kolom JSON ini
        'inventors',
         'skema_tkt_template_path',

        'nama_pencipta',
        'nip_nim',
        'no_hp',
        'fakultas',
        'email',
        'prototipe',
        'nilai_perolehan',
        'sumber_dana',
        'skema_penelitian',
        'status' => 'required|in:terkirim,proses,revisi,diterima,ditolak',

        'draft_paten',
        'form_permohonan',
        'surat_kepemilikan',
        'surat_pengalihan',
        'scan_ktp',
        'tanda_terima',
        'gambar_prototipe',
        'deskripsi_singkat_prototipe',
    ];

    protected $casts = [
        'inventors' => 'array',
    ];
}
