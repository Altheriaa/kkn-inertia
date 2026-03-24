<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MahasiswaController extends Controller
{
    public function index(Request $request) {

        $search = $request->input('search');
        $status = $request->input('status');

        $mahasiswas = Mahasiswa::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('prodi', 'like', "%{$search}%")
                    ->orWhere('fakultas', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status_kkn', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Mahasiswa/Index', [
            'title' => 'Mahasiswa',
            'mahasiswas' => $mahasiswas,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function show($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        return Inertia::render('Admin/Mahasiswa/Show', [
            'title' => 'Detail Mahasiswa',
            'mahasiswa' => $mahasiswa
        ]);
    }
}
