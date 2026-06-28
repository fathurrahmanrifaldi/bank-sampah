<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Nasabah;

// Halaman utama → redirect ke login
Route::get('/', fn() => redirect()->route('login'));

// Auth routes dengan email verification diaktifkan
Auth::routes(['verify' => true]);

// Google OAuth Routes
Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

// Route Lengkapi Profil Google
Route::get('/nasabah-portal/complete-profile', [App\Http\Controllers\Nasabah\ProfileController::class, 'completeProfileForm'])->name('nasabah.complete-profile');
Route::post('/nasabah-portal/complete-profile', [App\Http\Controllers\Nasabah\ProfileController::class, 'completeProfileStore'])->name('nasabah.complete-profile.store');

// Redirect setelah login berdasarkan role
Route::get('/home', [AuthController::class, 'redirectAfterLogin'])
    ->name('home')
    ->middleware('auth');

/* ═══════════════════════════════════════════
   ROUTE ADMIN
═══════════════════════════════════════════ */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])
        ->name('dashboard');

    // Nasabah CRUD (tanpa approval — sudah digantikan email verification)
    Route::resource('nasabah', Admin\NasabahController::class)
        ->except(['show']);

    // Kategori Sampah CRUD
    Route::resource('kategori', Admin\KategoriController::class)
        ->except(['show']);

    // Transaksi
    Route::resource('transaksi', Admin\TransaksiController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::post('transaksi/import', [Admin\TransaksiController::class, 'importExcel'])
        ->name('transaksi.import');

    // Penjualan ke Pengepul
    Route::resource('penjualan-pengepul', Admin\PenjualanPengepulController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Penilaian Nasabah Terbaik
    Route::get('/penilaian',        [Admin\PenilaianController::class, 'index'])
        ->name('penilaian.index');
    Route::post('/penilaian/hitung', [Admin\PenilaianController::class, 'hitung'])
        ->name('penilaian.hitung');

    // Penarikan Dana
    Route::get('/penarikan-dana', [Admin\PenarikanDanaController::class, 'index'])
        ->name('penarikan-dana.index');
    Route::post('/penarikan-dana/{id}/approve', [Admin\PenarikanDanaController::class, 'approve'])
        ->name('penarikan-dana.approve');
    Route::post('/penarikan-dana/{id}/reject',  [Admin\PenarikanDanaController::class, 'reject'])
        ->name('penarikan-dana.reject');
});

/* ═══════════════════════════════════════════
   ROUTE SUPER ADMIN (Ketua RW)
═══════════════════════════════════════════ */
Route::prefix('super-admin')
    ->name('super-admin.')
    ->middleware(['auth', 'verified', 'role:super_admin'])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [SuperAdmin\DashboardController::class, 'index'])
        ->name('dashboard');

    // Kelola Admin (Petugas Lapangan)
    Route::resource('kelola-admin', SuperAdmin\KelolaAdminController::class)
        ->except(['show'])
        ->parameters(['kelola-admin' => 'kelolaAdmin']);
    Route::post('/kelola-admin/{kelolaAdmin}/toggle-status', [SuperAdmin\KelolaAdminController::class, 'toggleStatus'])
        ->name('kelola-admin.toggle-status');

    // Kategori Sampah (Super Admin juga bisa CRUD)
    Route::resource('kategori', SuperAdmin\KategoriController::class)
        ->except(['show']);

    // Laporan
    Route::get('/laporan', [SuperAdmin\LaporanController::class, 'index'])
        ->name('laporan.index');

    // Profil Super Admin
    Route::get('/profil', [SuperAdmin\ProfileController::class, 'edit'])
        ->name('profil.edit');
    Route::put('/profil', [SuperAdmin\ProfileController::class, 'updateProfil'])
        ->name('profil.update');
    Route::put('/profil/password', [SuperAdmin\ProfileController::class, 'updatePassword'])
        ->name('profil.password');
});

/* ═══════════════════════════════════════════
   ROUTE NASABAH
   Middleware 'verified' memastikan hanya nasabah yang sudah verifikasi email
   yang bisa akses dashboard. Nasabah yang belum verifikasi diarahkan ke /email/verify
═══════════════════════════════════════════ */
Route::prefix('nasabah-portal')
    ->name('nasabah.')
    ->middleware(['auth', 'verified', 'role:nasabah'])
    ->group(function () {

    Route::get('/dashboard', [Nasabah\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/riwayat',   [Nasabah\RiwayatController::class, 'index'])
        ->name('riwayat');

    // Penarikan Dana
    Route::get('/penarikan',          [Nasabah\PenarikanDanaController::class, 'index'])->name('penarikan.index');
    Route::get('/penarikan/create',   [Nasabah\PenarikanDanaController::class, 'create'])->name('penarikan.create');
    Route::post('/penarikan',         [Nasabah\PenarikanDanaController::class, 'store'])->name('penarikan.store');

    // Profil Nasabah (Edit)
    Route::get('/profil', [Nasabah\ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [Nasabah\ProfileController::class, 'update'])->name('profil.update');
});