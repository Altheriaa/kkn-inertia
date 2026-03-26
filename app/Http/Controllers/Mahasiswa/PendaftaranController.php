<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Models\JadwalKkn;
use App\Models\Mahasiswa;

class PendaftaranController extends Controller
{
    public function index(){
        //validasi apakah pendaftaran buka ato ga?
        $hariini = now()->format('Y-m-d');

        $cekJadwal = JadwalKkn::where('is_active', true)
            ->whereDate('tanggal_dibuka', '<=', $hariini)
            ->whereDate('tanggal_ditutup', '>=', $hariini)
            ->first();

        if (! $cekJadwal) {
            return back()->withErrors([
                'error' => 'Pendaftaran KKN belum dibuka.',
            ]);
        }

        // Ambil dari session
        $mahasiswaSession = Session::get('mahasiswa_data');

        if (!$mahasiswaSession) {
            return redirect()->route('login')->withErrors('Sesi habis.');
        }

        // Ambil mahasiswa lokal
        $mahasiswa = Mahasiswa::find($mahasiswaSession['id']);

        return Inertia::render('Mahasiswa/Pendaftaran', [
            'title' => 'Pendaftaran',
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function store(Request $request)
    {
        // validasi apakah mahasiswa udah daftar apa belum, kalo udah gabisa edit lagi
        $mahasiswaId = Session::get('mahasiswa_data')['id'];
        // 2. Cari mahasiswa itu di tabel lokal menggunakan ID-nya
        $mahasiswa = Mahasiswa::find($mahasiswaId);

        if ($mahasiswa->status_kkn === 'Sudah Daftar') {
            return back()->withErrors('Anda tidak bisa lagi mengubah data diri');
        }

        $validated = $request->validate([
            'no_hp' => 'nullable|string|max:14',
            'no_hp_wali' => 'nullable|string|max:14',
            'jenis_kelamin' => 'nullable|in:L,P',
            'ukuran_jacket_rompi' => 'nullable|in:S,M,L,XL,XXL,3XL',
            'punya_kendaraan' => 'nullable|in:Punya,Tidak',
            'tipe_kendaraan' => 'nullable|in:Tidak Ada,Mobil,Sepeda Motor',
            'punya_lisensi' => 'nullable|in:Tidak Ada,SIM A,SIM B,SIM C,Lainnya',
            'keahlian' => 'nullable|string|max:255',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_foto_ktp' => 'nullable'
        ]);

        $mahasiswaSession = Session::get('mahasiswa_data');

        if (!$mahasiswaSession) {
            return redirect()->route('login')->withErrors('Sesi habis.');
        }

        // upload file
        if ($request->hasFile('foto_ktp')) {
            $validated['foto_ktp'] = $request->file('foto_ktp')->store('foto_ktp', 'public');
        } elseif ($request->boolean('remove_foto_ktp')) {
            $validated['foto_ktp'] = null;
        } else {
            unset($validated['foto_ktp']);
        }

        // Update mahasiswa
        $mahasiswa = Mahasiswa::find($mahasiswaSession['id']);
        $mahasiswa->update($validated);

        return back()->with('success', 'Data berhasil disimpan.');
    }
}
