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
use App\Http\Controllers\Mahasiswa\PembayaranController;
use App\Http\Controllers\Mahasiswa\PendaftaranController;
use App\Http\Controllers\Mahasiswa\ProfileController;
use App\Http\Controllers\MidtransWebhookController;
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

// Midtrans handler notification
Route::post('/midtrans/notification', [MidtransWebhookController::class, 'handle']);

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

    // Pendaftaran / Biodata Mahasiswa
    Route::resource('/pendaftaran', PendaftaranController::class);

    // Pembayaran
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('mahasiswa.pembayaran');
    Route::post('/pembayaran/create-transaction', [PembayaranController::class, 'createTransaction']);
    Route::post('/pembayaran/{orderId}/cancel', [PembayaranController::class, 'cancelTransaction']);

    // Riwayat Transaksi
    Route::get('/riwayat-transaksi', [PembayaranController::class, 'riwayatTransaksi']);
    // Cetak Invoice
    Route::get('/riwayat-transaksi/invoice/{orderId}', [PembayaranController::class, 'cetakTransaksi']);
    // Cetak Formulir
    Route::get('/riwayat-transaksi/formulir/{orderId}', [PembayaranController::class, 'cetakPendaftaran']);
    
    // Profile Route
    Route::get('/profile', [ProfileController::class, 'index'])->name('mahasiswa.profile');
});


