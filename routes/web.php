<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DosenDplController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Mahasiswa\DashboardMahasiswaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JadwalKknController;
use App\Http\Controllers\Admin\LokasiKknController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('landingPage');
});

// login mahasiswa
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// login admin
Route::get('/login-admin', [LoginAdminController::class, 'index'])->name('login.admin');
Route::post('/auth/login/admin', [LoginAdminController::class, 'login'])->name('login.admin.post');
Route::get('/logout/admin', [LoginAdminController::class, 'logout'])->name('logout.admin');

// Admin Routing
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Sync Mahasiswa From Siakad
    Route::get('/sync-mahasiswa', [DashboardController::class, 'sync'])->name('admin.sync-mahasiswa');

    // Dosen / DPL
    Route::resource('dosen-dpl', DosenDplController::class);

    // Mahasiswa
    Route::resource('mahasiswa', MahasiswaController::class);

    // Jadwal KKN
    Route::get('/jadwal-kkn', [JadwalKknController::class, 'index'])->name('admin.jadwal-kkn');
    Route::post('/sinkron-jadwal', [JadwalKknController::class, 'sync'])->name('admin.sinkron.jadwal');

    // Lokasi KKN
    Route::resource('lokasi-kkn', LokasiKknController::class);
});

// Mahasiswa Routing
Route::middleware(['auth.mahasiswa'])->prefix('mahasiswa')->group(function () {

    // Dashboard Mahasiswa
    Route::get('/dashboard', [DashboardMahasiswaController::class, 'index'])->name('mahasiswa.dashboard');

});


