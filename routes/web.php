<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\BarangController as BarangWebController;
use App\Http\Controllers\Web\KategoriController;
use App\Http\Controllers\Web\TransaksiMasukController;
use App\Http\Controllers\Web\TransaksiKeluarController;
use App\Http\Controllers\Web\PermintaanBarangController;
use App\Http\Controllers\Web\LaporanController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\LogAktivitasController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data - Barang
    Route::resource('barang', BarangWebController::class)
        ->except(['show'])
        ->parameters(['barang' => 'barang']);

    // Master Data - Kategori (Admin Gudang only)
    Route::resource('kategori', KategoriController::class)
        ->parameters(['kategori' => 'kategori']);

    // Transaksi - Barang Masuk & Keluar (Admin Gudang only)
    Route::resource('transaksi-masuk', TransaksiMasukController::class)
        ->only(['index', 'create', 'store'])
        ->parameters(['transaksi-masuk' => 'transaksi-masuk']);

    Route::resource('transaksi-keluar', TransaksiKeluarController::class)
        ->only(['index', 'create', 'store'])
        ->parameters(['transaksi-keluar' => 'transaksi-keluar']);

    // Permintaan Barang
    Route::resource('permintaan-barang', PermintaanBarangController::class)
        ->only(['index', 'create', 'store'])
        ->parameters(['permintaan-barang' => 'permintaan-barang']);

    Route::post('permintaan-barang/{permintaan_barang}/approve', [PermintaanBarangController::class, 'approve'])
        ->name('permintaan-barang.approve');
    Route::post('permintaan-barang/{permintaan_barang}/reject', [PermintaanBarangController::class, 'reject'])
        ->name('permintaan-barang.reject');

    // Notifikasi (web guard)
    Route::get('notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::patch('notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');

    // Laporan (Admin Gudang & Kepala Divisi)
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

    // User Management (Kepala Divisi full, Admin hanya tambah Petugas)
    Route::resource('user', UserController::class)
        ->parameters(['user' => 'user']);

    // Log Aktivitas
    Route::get('log-aktivitas', [LogAktivitasController::class, 'index'])->name('log-aktivitas.index');
});
