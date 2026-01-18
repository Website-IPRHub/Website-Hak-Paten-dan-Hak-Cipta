<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiDokumen extends Model
{
    protected $table = 'verifikasi_dokumen';

    protected $fillable = [
        'ref_type',
        'ref_id',
        'doc_key',
        'status',
        'note',
        'admin_attachment_path',
        'requested_at',
    ];
}
