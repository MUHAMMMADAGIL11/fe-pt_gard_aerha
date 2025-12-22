<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\PermintaanBarang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $kategoriCount = Kategori::count();
        $lowStockCount = Barang::whereColumn('stok', '<', 'stok_minimum')->count();
        $lowStockItems = Barang::whereColumn('stok', '<', 'stok_minimum')->take(5)->get();
        $mauHabisCount = $lowStockCount;
        $totalStok = Barang::sum('stok');
        $permintaanPending = PermintaanBarang::where('status', 'Menunggu Persetujuan')->count();
        $recentItems = Barang::with('kategori')->orderByDesc('id_barang')->take(5)->get();
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

        // Data for Chart (Last 6 Months)
        $chartData = [
            'labels' => [],
            'masuk' => [],
            'keluar' => []
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $chartData['labels'][] = $monthName;

            $masuk = Transaksi::where('jenis_transaksi', 'MASUK')
                ->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');

            $keluar = Transaksi::where('jenis_transaksi', 'KELUAR')
                ->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');

            $chartData['masuk'][] = $masuk;
            $chartData['keluar'][] = $keluar;
        }

        $topBarangLaku = Transaksi::select('id_barang', DB::raw('SUM(jumlah) as total_keluar'))
            ->where('jenis_transaksi', 'KELUAR')
            ->with('barang')
            ->groupBy('id_barang')
            ->orderByDesc('total_keluar')
            ->take(5)
            ->get();

        return view('pages.dashboard.index', compact(
            'totalBarang',
            'kategoriCount',
            'lowStockCount',
            'lowStockItems',
            'mauHabisCount',
            'totalStok',
            'permintaanPending',
            'recentItems',
            'transaksiMasukTerbaru',
            'transaksiKeluarTerbaru',
            'topBarangLaku',
            'chartData'
        ));
    }
}

