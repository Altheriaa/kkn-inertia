<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Models\Mahasiswa;

class DashboardController extends Controller
{
    public function index() {
        return Inertia::render("Admin/Dashboard");
    }

    public function sync() {

        $siakadApiUrl = 'https://mini-siakad.cloud/api/sync-mahasiswa';

        // try {
            $response = Http::withHeaders([
                'X-SYSTEM-KEY' => env('SYSTEM_API_KEY'),
                'Accept' => 'application/json'
            ])->get($siakadApiUrl);

            if ($response->failed()) {
                throw new \Exception('API gagal');
            }

            $users = $response->json('users');

            // dd($users);

            foreach ($users as $user) {
                Mahasiswa::updateOrCreate(
                    ['id' => $user['id']],
                    [
                        'nim' => $user['nim'],
                        'nama' => $user['name'],
                        'email' => $user['email'],
                        'jumlah_sks' => data_get($user, 'mahasiswa.jumlah_sks', 0),
                        'fakultas' => data_get($user, 'mahasiswa.prodi.fakultas.nama_fakultas'),
                        'prodi' => data_get($user, 'mahasiswa.prodi.nama_prodi'),
                    ]
                );
            }

            return redirect()->route('admin.dashboard');
    }
}