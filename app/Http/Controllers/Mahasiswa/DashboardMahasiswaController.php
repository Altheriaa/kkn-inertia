<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JadwalKkn;
use Illuminate\Support\Facades\Session;

class DashboardMahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswaSession = Session::get('mahasiswa_data');

        $siakadApiUrl = 'https://mini-siakad.cloud/api/jadwal-kkn';
        $secretKeySiakad = env('SYSTEM_API_KEY');

        // Jadwal Hasil Sinkron Lokal
        $jadwalKkn = JadwalKkn::where('is_active', true)->first();

        // panggil Api endpoinst jenis kkn 
        $siakadApiUrl = 'https://mini-siakad.cloud/api/kkn/jenis';
        $secretKeySiakad = env('SYSTEM_API_KEY');
        $jenisKknList = [];

        try {
            // UBAH BAGIAN INI: Tambahkan withHeaders()
            $response = Http::withHeaders([
                'X-SYSTEM-KEY' => $secretKeySiakad,
                'Accept' => 'application/json'
            ])->get($siakadApiUrl);
            // AKHIR PERUBAHAN

            if ($response->successful()) {
                $jenisKknList = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
        }

        return Inertia::render('Mahasiswa/Dashboard', [
            'title' => 'Dashboard',
            'jadwalKkn' => $jadwalKkn,
            'jenisKknList' => $jenisKknList,
            'mahasiswaSession' => $mahasiswaSession,
        ]);
    }
}
