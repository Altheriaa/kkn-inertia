<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = "mahasiswa";

    public $incrementing = false;

    protected $fillable = [
        'id',
        'nim',
        'nama',
        'fakultas',
        'prodi',
        'email',
        'jumlah_sks',
        'no_hp',
        'no_hp_wali',
        'jenis_kelamin',
        'ukuran_jacket_rompi',
        'punya_kendaraan',
        'tipe_kendaraan',
        'punya_lisensi',
        'keahlian',
        'status_kkn',
        'foto_ktp'
    ];

    // Satu mahasiswa cuma bisa punya satu payment
    public function payment()
    {
        return $this->hasOne(Payment::class, 'mahasiswa_id');
    }
    
    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranKkn::class, 'mahasiswa_id');
    }
}
