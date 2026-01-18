<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HakCipta extends Model
{
    protected $table = 'hak_cipta';

    protected $fillable = [
        'no_pendaftaran',
        'jenis_cipta',
        'judul_cipta',
        'nama_pencipta',
        'nip_nim',
        'fakultas',
        'email',
        'no_hp',
        'nilai_perolehan',
        'skema_penelitian',
        'sumber_dana',
        'surat_permohonan',
        'surat_pernyataan',
        'surat_pengalihan',
        'tanda_terima',
        'scan_ktp',
        'hasil_ciptaan',
        'link_ciptaan',
        'status', // aman kalau kolom ini ada
    ];
}
