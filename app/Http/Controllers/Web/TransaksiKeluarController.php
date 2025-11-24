<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiKeluar;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;

class TransaksiKeluarController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::where('jenis_transaksi', 'KELUAR')
            ->with(['user', 'barang.kategori', 'transaksiKeluar'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_transaksi', 'desc')
            ->paginate(15);

        return view('pages.entities.transaksi-keluar.index', compact('transaksi'));
    }

    public function create()
    {
        $barang = Barang::with('kategori')
            ->where('stok', '>', 0)
            ->orderBy('nama_barang')
            ->get();

        return view('pages.entities.transaksi-keluar.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => ['required', 'integer', 'exists:barang,id_barang'],
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tujuan' => ['nullable', 'string', 'max:100'],
        ], [
            'id_barang.required' => 'Barang wajib dipilih.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);

        $barang = Barang::find($validated['id_barang']);

        if ($barang->stok < $validated['jumlah']) {
            return back()
                ->withInput()
                ->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $barang->stok);
        }

        DB::transaction(function () use ($validated, $request) {
            $transaksi = Transaksi::create([
                'id_user' => auth()->user()->id_user,
                'id_barang' => $validated['id_barang'],
                'jenis_transaksi' => 'KELUAR',
                'tanggal' => $validated['tanggal'],
                'jumlah' => $validated['jumlah'],
            ]);

            TransaksiKeluar::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'tujuan' => $validated['tujuan'] ?? null,
            ]);

            $barang = Barang::find($validated['id_barang']);
            $barang->decrement('stok', $validated['jumlah']);
        });

        ActivityLogger::log(
            'Transaksi Keluar',
            'Barang ID: ' . $validated['id_barang'] . ', Jumlah: ' . $validated['jumlah'] . ', Tanggal: ' . $validated['tanggal']
        );

        return redirect()
            ->route('transaksi-keluar.index')
            ->with('success', 'Transaksi keluar berhasil dicatat.');
    }
}

