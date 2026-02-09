<?php

// app/Models/Revision.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
  protected $fillable = [
    'type','ref_type','ref_id','doc_key',
    'state','from_role','note',
    'file_path','admin_file_name',
    'pemohon_file_path','pemohon_file_name','pemohon_uploaded_at',
    'is_read_admin','is_read_pemohon',
  ];

  protected $casts = [
    'pemohon_uploaded_at' => 'datetime',
    'is_read_admin' => 'boolean',
    'is_read_pemohon' => 'boolean',
  ];
}
