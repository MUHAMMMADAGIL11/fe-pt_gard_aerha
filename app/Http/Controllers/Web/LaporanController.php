<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Barang;
use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', now()->format('Y-m-d'));
        $jenis = $request->get('jenis', 'semua');

        $query = Transaksi::with(['user', 'barang.kategori'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_transaksi', 'desc');

        if ($jenis !== 'semua') {
            $query->where('jenis_transaksi', $jenis);
        }

        $transaksi = $query->get();

        $totalMasuk = Transaksi::where('jenis_transaksi', 'MASUK')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->sum('jumlah');

        $totalKeluar = Transaksi::where('jenis_transaksi', 'KELUAR')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->sum('jumlah');

        return view('pages.entities.laporan.index', compact('transaksi', 'tanggalMulai', 'tanggalAkhir', 'jenis', 'totalMasuk', 'totalKeluar'));
    }

    public function exportPdf(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', now()->format('Y-m-d'));
        $jenis = $request->get('jenis', 'semua');

        $query = Transaksi::with(['user', 'barang.kategori'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal', 'desc');

        if ($jenis !== 'semua') {
            $query->where('jenis_transaksi', $jenis);
        }

        $transaksi = $query->get();

        $totalMasuk = Transaksi::where('jenis_transaksi', 'MASUK')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->sum('jumlah');

        $totalKeluar = Transaksi::where('jenis_transaksi', 'KELUAR')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->sum('jumlah');

        // TODO: Install barryvdh/laravel-dompdf package for PDF export
        // For now, return view instead
        return view('pages.entities.laporan.pdf', compact('transaksi', 'tanggalMulai', 'tanggalAkhir', 'jenis', 'totalMasuk', 'totalKeluar'));
        
        // Uncomment after installing: composer require barryvdh/laravel-dompdf
        // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.entities.laporan.pdf', compact('transaksi', 'tanggalMulai', 'tanggalAkhir', 'jenis', 'totalMasuk', 'totalKeluar'));
        // return $pdf->download('laporan-inventori-' . date('Y-m-d') . '.pdf');
    }
}

