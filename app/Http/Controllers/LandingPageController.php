<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKkn;
use App\Models\LokasiKkn;
use Inertia\Inertia;

class LandingPageController extends Controller
{
    public function index() {

        $lokasiKkns = LokasiKkn::orderBy('kabupaten_kota', 'asc')->get();
        $jadwalKkn = JadwalKkn::where('is_active', true)->orderBy('created_at', 'desc')->first();

        return Inertia::render('Landing', [
            'lokasiKkns' => $lokasiKkns,
            'jadwalKkn' => $jadwalKkn,
        ]);
    }
}
