<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\LokasiKkn;
use GuzzleHttp\Promise\Create;

class LokasiKknController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $lokasiKkns = LokasiKkn::when($search, function ($query, $search) {
                $query->where('kabupaten_kota', 'like', "%{$search}%")
                      ->orWhere('kecamatan', 'like', "%{$search}%")
                      ->orWhere('nama_desa', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/LokasiKkn/Index', [
            'lokasiKkns' => $lokasiKkns,
            'filters' => ['search' => $search],
        ]);
    }

    public function create() {
        return Inertia::render('Admin/LokasiKkn/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kabupaten_kota' => 'string|required|max:50',
            'kecamatan' => 'string|required|max:50',
            'nama_desa' => 'string|required|max:50|unique:lokasi_kkn,nama_desa',
        ], [
            'nama_desa.unique' => 'Desa sudah ada.'
        ]);

        LokasiKkn::create($data);

        return redirect('/admin/lokasi-kkn')->with('success', 'Data Lokasi KKN berhasil ditambahkan!');
    }

    public function edit($id) {

        $lokasiKkn = LokasiKkn::findOrFail($id);

        return Inertia::render('Admin/LokasiKkn/Edit', [
            'lokasiKkn' => $lokasiKkn,
        ]);
    }

    public function update(Request $request, $id)
    {
        $lokasiKkn = LokasiKkn::findOrFail($id);

        $data = $request->validate([
            'kabupaten_kota' => 'string|required|max:50',
            'kecamatan' => 'string|required|max:50',
            'nama_desa' => 'string|required|max:50|unique:lokasi_kkn,nama_desa,' . $lokasiKkn->id,
        ]);

        $lokasiKkn->update($data);

        return redirect('/admin/lokasi-kkn')->with('success', 'Data Lokasi KKN berhasil diupdate!');
    }

    public function destroy($id)
    {
        $lokasiKkn = LokasiKkn::findOrFail($id);
        $lokasiKkn->delete();
        return redirect('/admin/lokasi-kkn')->with('success', 'Data Lokasi KKN berhasil dihapus!');
    }
}
