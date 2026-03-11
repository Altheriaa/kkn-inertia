-- Active: 1770430734899@@127.0.0.1@3306@ft_db_inertia
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\DosenDpl;

class DosenDplController extends Controller
{
    public function index(Request $request) {

        $search = $request->input('search');

        $dosenDpls = DosenDpl::when($search, function ($query, $search) {
                $query->where('nuptk', 'like', "%{$search}%")
                      ->orWhere('nama_dosen', 'like', "%{$search}%")
                      ->orWhere('prodi', 'like', "%{$search}%")
                      ->orWhere('bidang_keahlian', 'like', "%{$search}%")
                      ->orWhere('no_hp', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/DosenDpl/Index', [
            'dosenDpls' => $dosenDpls,
            'filters' => ['search' => $search],
        ]);
    }

    public function create() {
        return Inertia::render('Admin/DosenDpl/Create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nuptk' => 'required|string|max:50|unique:dosen_dpl,nuptk',
            'nama_dosen' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'bidang_keahlian' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
        ]);

        DosenDpl::create($validated);

        return redirect('/admin/dosen-dpl')->with('success', 'Data Dosen DPL berhasil ditambahkan!');
    }

    public function edit($id) {
        $dosenDpl = DosenDpl::findOrFail($id);

        return Inertia::render('Admin/DosenDpl/Edit', [
            'dosenDpl' => $dosenDpl
        ]);
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'nuptk' => 'string|required|max:20|unique:dosen_dpl,nuptk,' . $id,
            'nama_dosen' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'bidang_keahlian' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
        ]);

        DosenDpl::findOrFail($id)->update($validated);

        return redirect('/admin/dosen-dpl')->with('success', 'Data Dosen DPL berhasil diupdate!');
    }

    public function destroy($id) {
        $dosenDpl = DosenDpl::findOrFail($id);

        $dosenDpl->delete();

        return redirect('/admin/dosen-dpl')->with('success', 'Data Dosen DPL berhasil dihapus!');
    }
}
