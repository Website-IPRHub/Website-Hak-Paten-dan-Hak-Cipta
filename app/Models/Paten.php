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
        'nama_pencipta',
        'nip_nim',
        'no_hp',
        'fakultas',
        'email',
        'prototipe',
        'nilai_perolehan',
        'sumber_dana',
        'skema_penelitian',
        'draft_paten',
        'form_permohonan',
        'surat_kepemilikan',
        'surat_pengalihan',
        'scan_ktp',
        'tanda_terima',
        'gambar_prototipe',
        'deskripsi_singkat_prototipe'
    ];
}
