<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKkn;
use App\Models\KelompokKkn;
use Barryvdh\DomPDF\Facade\Pdf;

class HasilPlottingController extends Controller
{
    public function index()
    {
        $mahasiswaSession = Session::get('mahasiswa_data');

        if (!$mahasiswaSession) {
            return redirect()->route('login')->withErrors('Sesi habis.');
        }

        // Ambil mahasiswa lokal
        $mahasiswa = Mahasiswa::find($mahasiswaSession['id']);

        $kelompok = PendaftaranKkn::with(['mahasiswa', 'kelompokKkn.dosenDpl', 'kelompokKkn.lokasiKkn', 'jadwalKkn'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status_pendaftaran', 'valid')
            ->whereNotNull('kelompok_kkn_id')
            ->first();

        $pendaftaranKkn = 0;
        if ($kelompok) {
            $pendaftaranKkn = PendaftaranKkn::where('kelompok_kkn_id', $kelompok->kelompok_kkn_id)->count();
        }

        return Inertia::render('Mahasiswa/Plotting/Index', [
            'kelompok' => $kelompok,
            'mahasiswa' => $mahasiswa,
            'pendaftaranKkn' => $pendaftaranKkn,
        ]);
    }

    public function cetakLaporanKelompok($id)
    {
        $mahasiswaSession = Session::get('mahasiswa_data');
        if (!$mahasiswaSession) {
            return redirect()->route('login')->withErrors('Sesi habis, silakan login kembali.');
        }
        $pendaftaran = PendaftaranKkn::findOrFail($id);

        if ($pendaftaran->mahasiswa_id != $mahasiswaSession['id']) {
            return redirect()->route('mahasiswa.plotting')->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        if (!$pendaftaran->kelompok_kkn_id) {
            return back()->with('error', 'Anda belum terdaftar di kelompok manapun.');
        }

        $kelompok = KelompokKkn::with(['dosenDpl', 'lokasiKkn', 'jadwalKkn'])
            ->findOrFail($pendaftaran->kelompok_kkn_id);

        $anggota = PendaftaranKkn::with('mahasiswa')
            ->where('kelompok_kkn_id', $kelompok->id)
            ->get();

        $pdf = Pdf::loadView('pdf.Mahasiswa.LaporanPlotting', [
            'kelompok' => $kelompok,
            'anggota' => $anggota,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Plotting-' . $kelompok->nama_kelompok . '.pdf');
    }
}
