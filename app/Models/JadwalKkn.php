<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKkn extends Model
{
    protected $table = 'jadwal_kkn';

    protected $fillable = [
        'id_siakad',
        'tahun_akademik_id',
        'nama_periode',
        'semester',
        'tahun_ajaran',
        'tanggal_dibuka',
        'tanggal_ditutup',
        'is_active',
    ];

    // Format Periode
    public function getNamaPeriodeAttribute($value)
    {
        // $value adalah nilai asli dari database: "2025 1 - Ganjil"
        if (!$value) return $value;

        $parts = explode(' ', $value);
        
        if (count($parts) >= 4) {
            $tahunMulai = (int) $parts[0]; // 2025
            $tahunSelesai = $tahunMulai + 1; // 2026
            $semester = $parts[3]; // Ganjil
            
            return "{$tahunMulai}/{$tahunSelesai} {$semester}";
        }

        return $value;
    }

    public function kelompokKkn()
    {
        return $this->hasMany(KelompokKkn::class, 'jadwal_kkn_id');
    }

    public function pendaftaranKkn()
    {
        return $this->hasMany(PendaftaranKkn::class, 'jadwal_kkn_id');
    }
}
