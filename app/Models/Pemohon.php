<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pemohon extends Authenticatable
{
    protected $table = 'pemohons';

    protected $fillable = [
        'kode_unik',
        'password',
    ];

    protected $hidden = ['password','remember_token'];
}
