<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HakCiptaVerif extends Model
{
    protected $table = 'hak_cipta_verifs';

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
        'status',
        'inventors',

        'surat_permohonan_drive_url',
        'surat_pernyataan_drive_url',
        'surat_pengalihan_drive_url',
        'tanda_terima_drive_url',
        'scan_ktp_drive_url',
        'hasil_ciptaan_drive_url',
    ];

    protected $casts = [
        'inventors' => 'array',
    ];
}
