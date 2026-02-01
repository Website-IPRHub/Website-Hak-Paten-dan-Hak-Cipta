<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatenVerif extends Model
{
    protected $table = 'paten_verifs';

    protected $fillable = [
        // utama
        'no_pendaftaran',
        'jenis_paten',
        'judul_paten',

        // inventors json
        'inventors',
         'skema_tkt_template_path',

        // ringkasan inventor pertama (opsional)
        'nama_pencipta',
        'nip_nim',
        'fakultas',
        'no_hp',
        'email',

        // data tambahan
        'prototipe',
        'nilai_perolehan',
        'sumber_dana',
        'skema_penelitian',

        // draft & dokumen (path file)
        'draft_paten',
        'form_permohonan',
        'surat_kepemilikan',
        'surat_pengalihan',
        'scan_ktp',
        'tanda_terima',
        'gambar_prototipe',
        'deskripsi_singkat_prototipe',
        'link_ciptaan',

        // verifikasi
        'status_verif',
        'catatan_verif',
    ];

    protected $casts = [
        'inventors' => 'array',
    ];
}
