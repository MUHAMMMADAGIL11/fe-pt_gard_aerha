<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\PermintaanBarang;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    /**
     * Halaman dashboard utama setelah login.
     */
    public function index()
    {
        $totalBarang = Barang::count();
        $kategoriCount = Kategori::count();
        $lowStockCount = Barang::whereColumn('stok', '<', 'stok_minimum')->count();
        $totalStok = Barang::sum('stok');
        // Konsisten dengan status yang digunakan di sistem web: 'Menunggu Persetujuan'
        $permintaanPending = PermintaanBarang::where('status', 'Menunggu Persetujuan')->count();
        $recentItems = Barang::with('kategori')
            ->orderByDesc('id_barang')
            ->take(5)
            ->get();
        $transaksiMasukTerbaru = Transaksi::with('barang')
            ->where('jenis_transaksi', 'MASUK')
            ->orderByDesc('tanggal')
            ->take(5)
            ->get();
        $transaksiKeluarTerbaru = Transaksi::with('barang')
            ->where('jenis_transaksi', 'KELUAR')
            ->orderByDesc('tanggal')
            ->take(5)
            ->get();

        return view('pages.dashboard.index', compact(
            'totalBarang',
            'kategoriCount',
            'lowStockCount',
            'totalStok',
            'permintaanPending',
            'recentItems',
            'transaksiMasukTerbaru',
            'transaksiKeluarTerbaru'
        ));
    }
}

