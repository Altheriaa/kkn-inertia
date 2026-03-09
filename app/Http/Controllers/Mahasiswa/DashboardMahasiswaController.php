<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardMahasiswaController extends Controller
{
    public function index() {
        
        return Inertia::render('Mahasiswa/Dashboard');
    }
}
