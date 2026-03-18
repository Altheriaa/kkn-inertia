<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use App\Models\Mahasiswa;

class ProfileController extends Controller
{
    public function index()
    {
        $mahasiswaSession = Session::get('mahasiswa_data');

        if (!$mahasiswaSession) {
            return redirect()->route('login')->withErrors('Sesi habis.');
        }

        // Ambil mahasiswa lokal
        $mahasiswa = Mahasiswa::find($mahasiswaSession['id']);
        return Inertia::render('Mahasiswa/Profile', 
            [
                'title' => 'Profile',
                'mahasiswa' => $mahasiswa
            ]
        );
    }
}
