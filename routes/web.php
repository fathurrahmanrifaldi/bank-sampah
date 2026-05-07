<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Nasabah;

// Halaman utama → redirect ke login
Route::get('/', fn() => redirect()->route('login'));

// Auth routes (login / logout) — registrasi dinonaktifkan
Auth::routes(['register' => false]);

// Redirect setelah login berdasarkan role
Route::get('/home', [AuthController::class, 'redirectAfterLogin'])
    ->name('home')
    ->middleware('auth');

/* ═══════════════════════════════════════════
   ROUTE ADMIN
═══════════════════════════════════════════ */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])
        ->name('dashboard');

    // Nasabah CRUD
    Route::resource('nasabah', Admin\NasabahController::class)
        ->except(['show']);

    // Kategori Sampah CRUD
    // create & edit share satu view (form.blade.php)
    Route::resource('kategori', Admin\KategoriController::class)
        ->except(['show']);

    // Transaksi
    Route::resource('transaksi', Admin\TransaksiController::class)
        ->only(['index', 'create', 'store', 'show']);

    // Laporan
    Route::get('/laporan', [Admin\LaporanController::class, 'index'])
        ->name('laporan.index');

    // Penilaian Nasabah Terbaik
    Route::get('/penilaian',        [Admin\PenilaianController::class, 'index'])
        ->name('penilaian.index');
    Route::post('/penilaian/hitung', [Admin\PenilaianController::class, 'hitung'])
        ->name('penilaian.hitung');
});

/* ═══════════════════════════════════════════
   ROUTE NASABAH
═══════════════════════════════════════════ */
Route::prefix('nasabah-portal')
    ->name('nasabah.')
    ->middleware(['auth', 'role:nasabah'])
    ->group(function () {

    Route::get('/dashboard', [Nasabah\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/riwayat',   [Nasabah\RiwayatController::class, 'index'])
        ->name('riwayat');
});