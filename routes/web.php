<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\LoginController;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
});

// login mahasiswa
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// login admin
Route::get('/login-admin', [LoginAdminController::class, 'index'])->name('login.admin');
Route::post('/auth/login/admin', [LoginAdminController::class, 'login'])->name('login.admin.post');
Route::get('/logout/admin', [LoginAdminController::class, 'logout'])->name('logout.admin');


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');
});
