<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $pdf = Pdf::loadView('pages.entities.laporan.pdf', compact('transaksi', 'tanggalMulai', 'tanggalAkhir', 'jenis', 'totalMasuk', 'totalKeluar'));
        return $pdf->download('laporan-inventori.pdf');
    }

    public function exportCsv(Request $request)
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
        $filename = "laporan-inventori-" . date('Y-m-d-H-i-s') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Tanggal', 'Jenis', 'Kode Barang', 'Nama Barang', 'Kategori', 'Jumlah', 'Petugas'];

        $callback = function() use($transaksi, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transaksi as $item) {
                fputcsv($file, [
                    $item->tanggal,
                    $item->jenis_transaksi,
                    $item->barang->kode_barang ?? '-',
                    $item->barang->nama_barang ?? '-',
                    $item->barang->kategori->nama_kategori ?? '-',
                    $item->jumlah,
                    $item->user->username ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

