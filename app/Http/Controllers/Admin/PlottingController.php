<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DosenDpl;
use App\Models\LokasiKkn;
use App\Models\JadwalKkn;
use Illuminate\Validation\Rule;
use App\Models\KelompokKkn;
use App\Models\PendaftaranKkn;
use Barryvdh\DomPDF\Facade\Pdf;

class PlottingController extends Controller
{

    public function index(Request $request) {

        $listJadwal = JadwalKkn::orderBy('id_siakad', 'desc')->get();

        $search = $request->input('search');
        $status = $request->input('status');

        // Jika baru buka (tidak ada parameter status/search di URL), default ke jadwal terbaru
        if (!$request->has('status') && !$request->has('search') && $listJadwal->isNotEmpty()) {
            $status = (string) $listJadwal->first()->id;
        }

        $kelompoks = KelompokKkn::with('dosenDpl', 'lokasiKkn', 'jadwalKkn')->withCount('pendaftaranKkn')
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_kelompok', 'like', "%{$search}%")
                        ->orWhereHas('dosenDpl', function ($q) use ($search) {
                            $q->where('nama_dosen', 'like', "%{$search}%");
                        })
                        ->orWhereHas('lokasiKkn', function ($q) use ($search) {
                            $q->where('nama_desa', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query, $status) {
                $query->whereHas('jadwalKkn', function ($q) use ($status) {
                    $q->where('id', $status);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Plotting/Index', [
            'listJadwal' => $listJadwal,
            'kelompoks' => $kelompoks,
            'filters' => ['search' => $search, 'status' => $status],
        ]);
    }

    public function create(Request $request) {

        $jadwalId = $request->query('jadwal_kkn_id');
        
        // Jadwal KKN dropdown
        $jadwalKkns = JadwalKkn::where('is_active', true)->get();

        // Dpl dropdown
        $dosenDpls = DosenDpl::whereDoesntHave('kelompokKkn', function($query) use ($jadwalId) {
           if ($jadwalId) {
            $query->where('jadwal_kkn_id', $jadwalId);
        }
        })->get();

        // Lokasi KKN 
        $lokasiKkns = LokasiKkn::whereDoesntHave('kelompokKkn', function($query) use ($jadwalId) {
            if ($jadwalId) {
                $query->where('jadwal_kkn_id', $jadwalId);
            }
        })->get();

        return Inertia::render('Admin/Plotting/Create', [
            'jadwalKkns' => $jadwalKkns,
            'dosenDpls' => $dosenDpls,
            'lokasiKkns' => $lokasiKkns,
            'filters'    => $request->only(['jadwal_kkn_id']),
        ]);
    }

    public function store(Request $request)
    {
        $jadwalAktif = JadwalKkn::where('is_active', true)->first();

        if (!$jadwalAktif) {
            return back()->withErrors(['error' => 'Tidak ada jadwal KKN yang aktif.']);
        }

        $data = $request->validate([
            'jadwal_kkn_id' => 'required|exists:jadwal_kkn,id',

            'dpl_id' => [
                'required',
                'exists:dosen_dpl,id',
                Rule::unique('kelompok_kkn', 'dpl_id')
                    ->where('jadwal_kkn_id', $request->jadwal_kkn_id)
                    ->ignore($request->id), 
            ],
            'lokasi_kkn_id' => [
                'required',
                'exists:lokasi_kkn,id',
                Rule::unique('kelompok_kkn', 'lokasi_kkn_id')
                    ->where('jadwal_kkn_id', $request->jadwal_kkn_id)
                    ->ignore($request->id), 
            ],
            'nama_kelompok' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelompok_kkn', 'nama_kelompok')
                    ->where('jadwal_kkn_id', $request->jadwal_kkn_id)
                    ->ignore($request->id), 
            ],
            'jenis_kkn' => 'required|string|in:KKN-UNAYA Regular,KKN-UNAYA Non-Regular',
        ], [
            'lokasi_kkn_id.unique' => 'Desa tersebut sudah memiliki kelompok KKN.',
            'dpl_id.unique' => 'Dosen tersebut sudah memiliki kelompok KKN.',
            'nama_kelompok.unique' => 'Nama kelompok tersebut sudah ada.',
        ]);

        $data['jadwal_kkn_id'] = $jadwalAktif->id;
        KelompokKkn::create($data);

        return redirect()->route('plotting.index')->with('success', 'Kelompok KKN berhasil ditambahkan');
    }

    public function edit(Request $request, $id) {

        $kelompok = KelompokKkn::findOrFail($id);
        // Ambil jadwal_kkn_id dari query string, jika tidak ada pakai bawaan kelompok
        $jadwalId = $request->query('jadwal_kkn_id', $kelompok->jadwal_kkn_id);
        
        // Jadwal KKN dropdown (hanya yang aktif)
        $jadwalKkns = JadwalKkn::where('is_active', true)
            ->orWhere('id', $kelompok->jadwal_kkn_id) // Tetap sertakan jadwal kelompok jika tidak aktif
            ->get();

        // Dpl dropdown: yang belum punya kelompok di jadwal tersebut ATAU DPL kelompok ini sendiri
        $dosenDpls = DosenDpl::where(function($query) use ($jadwalId, $kelompok) {
            $query->whereDoesntHave('kelompokKkn', function($q) use ($jadwalId) {
                $q->where('jadwal_kkn_id', $jadwalId);
            })->orWhere('id', $kelompok->dpl_id);
        })->get();

        // Lokasi KKN dropdown: yang belum punya kelompok di jadwal tersebut ATAU lokasi kelompok ini sendiri
        $lokasiKkns = LokasiKkn::where(function($query) use ($jadwalId, $kelompok) {
            $query->whereDoesntHave('kelompokKkn', function($q) use ($jadwalId) {
                $q->where('jadwal_kkn_id', $jadwalId);
            })->orWhere('id', $kelompok->lokasi_kkn_id);
        })->get();

        return Inertia::render('Admin/Plotting/Edit', [
            'jadwalKkns' => $jadwalKkns,
            'dosenDpls' => $dosenDpls,
            'lokasiKkns' => $lokasiKkns,
            'kelompok' => $kelompok,
            'filters'    => ['jadwal_kkn_id' => $jadwalId],
        ]);
    }

    public function update(Request $request, $id)
    {
        $kelompok = KelompokKkn::findOrFail($id);

        $data = $request->validate([
            'jadwal_kkn_id' => 'required|exists:jadwal_kkn,id',
            'dpl_id' => [
                'required',
                'exists:dosen_dpl,id',
                Rule::unique('kelompok_kkn', 'dpl_id')
                    ->where('jadwal_kkn_id', $request->jadwal_kkn_id)
                    ->ignore($id),
            ],
            'lokasi_kkn_id' => [
                'required',
                'exists:lokasi_kkn,id',
                Rule::unique('kelompok_kkn', 'lokasi_kkn_id')
                    ->where('jadwal_kkn_id', $request->jadwal_kkn_id)
                    ->ignore($id),
            ],
            'nama_kelompok' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelompok_kkn', 'nama_kelompok')
                    ->where('jadwal_kkn_id', $request->jadwal_kkn_id)
                    ->ignore($id),
            ],
            'jenis_kkn' => 'required|string|in:KKN-UNAYA Regular,KKN-UNAYA Non-Regular',
        ], [
            'lokasi_kkn_id.unique' => 'Desa tersebut sudah memiliki kelompok KKN.',
            'dpl_id.unique' => 'Dosen tersebut sudah memiliki kelompok KKN.',
            'nama_kelompok.unique' => 'Nama kelompok tersebut sudah ada.',
        ]);

        $kelompok->update($data);

        return redirect()->route('plotting.index')->with('success', 'Kelompok KKN berhasil diperbarui');
    }

    public function destroy($id) {

       $kelompok = KelompokKkn::findOrFail($id);

       $kelompok->delete();

        return redirect()->route('plotting.index')->with('success', 'Kelompok KKN berhasil dihapus');
    }

    public function kelolaAnggota(Request $request, $id){
        
        $kelompok = KelompokKkn::with(['dosenDpl', 'lokasiKkn', 'jadwalKkn'])->findOrFail($id);

        $anggota = PendaftaranKkn::with('mahasiswa')
            ->where('kelompok_kkn_id', $id)
            ->get();
        
        $queryKandidat = PendaftaranKkn::with('mahasiswa')
            ->where('jadwal_kkn_id', $kelompok->jadwal_kkn_id)
            ->where('jenis_kkn', $kelompok->jenis_kkn)
            ->where('status_pendaftaran', 'valid')
            ->whereNull('kelompok_kkn_id');

        $search = $request->input('search');

        $kandidat = $queryKandidat->when($search, function ($query, $search) {
            $query->whereHas('mahasiswa', function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('prodi', 'like', "%$search%")
                    ->orWhere('fakultas', 'like', "%$search%")
                    ->orWhere('prodi', 'like', "%$search%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return Inertia::render('Admin/Plotting/KelolaAnggota', [
            'kelompok' => $kelompok,
            'kandidat' => $kandidat,
            'anggota' => $anggota
        ]);
    }
    
    // sync anggota ke 

    public function cetakLaporanKelompok($id)
    {

        $kelompok = KelompokKkn::with(['dosenDpl', 'lokasiKkn', 'jadwalKkn'])->findOrFail($id);

        $anggota = PendaftaranKkn::with('mahasiswa')
            ->where('kelompok_kkn_id', $id)
            ->get();

        $pdf = Pdf::loadView('pdf.Admin.LaporanPlotting', [
            'kelompok' => $kelompok,
            'anggota' => $anggota,
        ]);

        // 6. Set Ukuran Kertas & Stream
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Plotting-' . $kelompok->nama_kelompok . '.pdf');
    }
}
