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

    // satu lokasi kkn hanya bisa dihuni oleh satu kelompok (pertimbangan karna bisa milih tahun)
    public function kelompokKkn()
    {
        return $this->hasOne(KelompokKkn::class, 'lokasi_kkn_id');
    }
}
