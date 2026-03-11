<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiKkn extends Model
{
    protected $table = 'lokasi_kkn';
    protected $fillable = [
        'kabupaten_kota',
        'kecamatan',
        'nama_desa',
    ];
}
