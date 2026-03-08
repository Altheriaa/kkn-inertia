<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\DosenDpl;

class DosenDplController extends Controller
{
    public function index() {

        $dosenDpls = DosenDpl::all();

        return Inertia::render('Admin/DosenDpl/Index', [
            'dosenDpls'=> $dosenDpls
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
