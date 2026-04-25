<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pemohon extends Authenticatable
{
    protected $table = 'pemohons';

    protected $fillable = [
        'kode_unik',
        'password',
        'google_drive_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'google_drive_token' => 'array',
    ];
}