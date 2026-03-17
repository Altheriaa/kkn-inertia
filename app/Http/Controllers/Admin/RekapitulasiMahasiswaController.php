<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalKkn;
use App\Models\PendaftaranKkn;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapitulasiMahasiswaController extends Controller
{
    public function index(Request $request) {

        $listJadwal = JadwalKkn::orderBy('id_siakad', 'desc')->get();

        $search = $request->input('search');
        $statusJadwal = $request->input('statusJadwal');
        $statusJenisKkn = $request->input('statusJenisKkn');

        // Jika baru buka (tidak ada parameter status/search di URL), default ke jadwal terbaru
        if (!$request->has('statusJadwal') && !$request->has('statusJenisKkn') && !$request->has('search') && $listJadwal->isNotEmpty()) {
            $statusJadwal = (string) $listJadwal->first()->id;
        }

        $siakadApiUrl = 'https://mini-siakad.cloud/api/kkn/jenis';
        $secretKeySiakad = env('SYSTEM_API_KEY');

        $listKkn = collect([]);

        try {
            $response = Http::withHeaders([
                'X-SYSTEM-KEY' => $secretKeySiakad,
                'Accept' => 'application/json'
            ])->get($siakadApiUrl);

            if ($response->successful()) {
                $listKkn = collect($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
        }

        $query = PendaftaranKkn::with(['mahasiswa', 'jadwalKkn'])
            ->where('status_pendaftaran', 'valid');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                });
            });
        }

        if ($statusJadwal) {
            $query->where('jadwal_kkn_id', $statusJadwal);
        }

        if ($statusJenisKkn) {
            $query->where('jenis_kkn_id', $statusJenisKkn);
        }

        // Clone query for counting before pagination
        $countQuery = clone $query;
        
        $pendaftarans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Calculate counts by faculty
        $facultyCounts = $countQuery->join('mahasiswa', 'pendaftaran_kkn.mahasiswa_id', '=', 'mahasiswa.id')
            ->selectRaw('mahasiswa.fakultas, count(*) as total')
            ->groupBy('mahasiswa.fakultas')
            ->pluck('total', 'fakultas');

        $selectedJenisKkn = $listKkn->firstWhere('id', $statusJenisKkn)['nama_jenis'] ?? 'Semua Jenis KKN';

        return Inertia::render('Admin/RekapitulasiMahasiswa', [
            'listJadwal' => $listJadwal,
            'listKkn' => $listKkn,
            'pendaftarans' => $pendaftarans,
            'filters' => [
                'search' => $search, 
                'statusJadwal' => $statusJadwal, 
                'statusJenisKkn' => $statusJenisKkn
            ],
            'countTeknik' => $facultyCounts['Teknik'] ?? 0,
            'countPertanian' => $facultyCounts['Pertanian'] ?? 0,
            'countPerikanan' => $facultyCounts['Perikanan'] ?? 0,
            'countKedokteran' => $facultyCounts['Kedokteran'] ?? 0,
            'countHukum' => $facultyCounts['Hukum'] ?? 0,
            'countKesmas' => $facultyCounts['Ilmu Ilmu Kesehatan'] ?? 0,
            'countFkip' => $facultyCounts['Keguruan dan Ilmu Pendidikan'] ?? 0,
            'countEkonomi' => $facultyCounts['Ekonomi'] ?? 0,
            'countTotal' => $countQuery->count(),
            'selectedJenisKkn' => $selectedJenisKkn,
        ]);
    }

    public function cetakRekapitulasi(Request $request)
    {
        $listJadwal = JadwalKkn::orderBy('id_siakad', 'desc')->get();
        
        $statusJadwal = $request->input('statusJadwal');
        $statusJenisKkn = $request->input('statusJenisKkn');

        // Jika tidak ada di request, coba cari default (seperti di index)
        if (!$statusJadwal && !$statusJenisKkn && $listJadwal->isNotEmpty()) {
            $statusJadwal = (string) $listJadwal->first()->id;
        }

        $siakadApiUrl = 'https://mini-siakad.cloud/api/kkn/jenis';
        $secretKeySiakad = env('SYSTEM_API_KEY');
        $listKkn = collect([]);

        try {
            $response = Http::withHeaders([
                'X-SYSTEM-KEY' => $secretKeySiakad,
                'Accept' => 'application/json'
            ])->get($siakadApiUrl);

            if ($response->successful()) {
                $listKkn = collect($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
        }

        $query = PendaftaranKkn::with('mahasiswa')
            ->where('status_pendaftaran', 'valid');

        if ($statusJadwal) {
            $query->where('jadwal_kkn_id', $statusJadwal);
        }

        if ($statusJenisKkn) {
            $query->where('jenis_kkn_id', $statusJenisKkn);
        }

        $pendaftarans = $query->get();

        $namaPeriode = $listJadwal->where('id', $statusJadwal)->first()->nama_periode ?? '-';
        $selectedJenisKkn = $listKkn->firstWhere('id', $statusJenisKkn)['nama_jenis'] ?? 'Semua Jenis KKN';

        $countTeknik = $pendaftarans->where('mahasiswa.fakultas', 'Teknik')->count();
        $countPertanian = $pendaftarans->where('mahasiswa.fakultas', 'Pertanian')->count();
        $countPerikanan = $pendaftarans->where('mahasiswa.fakultas', 'Perikanan')->count();
        $countKesmas = $pendaftarans->where('mahasiswa.fakultas', 'Ilmu Ilmu Kesehatan')->count();
        $countFkip = $pendaftarans->where('mahasiswa.fakultas', 'Keguruan dan Ilmu Pendidikan')->count();
        $countEkonomi = $pendaftarans->where('mahasiswa.fakultas', 'Ekonomi')->count();
        $countKedokteran = $pendaftarans->where('mahasiswa.fakultas', 'Kedokteran')->count();
        $countHukum = $pendaftarans->where('mahasiswa.fakultas', 'Hukum')->count();

        $pdf = Pdf::loadView('pdf.Admin.RekapitulasiMahasiswa', [
            'pendaftarans' => $pendaftarans,
            'countTeknik' => $countTeknik,
            'countPertanian' => $countPertanian,
            'countPerikanan' => $countPerikanan,
            'countKesmas' => $countKesmas,
            'countFkip' => $countFkip,
            'countEkonomi' => $countEkonomi,
            'countKedokteran' => $countKedokteran,
            'countHukum' => $countHukum,
            'selectedJenisKkn' => $selectedJenisKkn,
            'namaPeriode' => $namaPeriode,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Jumlah-Peserta-KKN.pdf');
    }
}
