<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiMasuk;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;

class TransaksiMasukController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::where('jenis_transaksi', 'MASUK')
            ->with(['user', 'barang.kategori', 'transaksiMasuk'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_transaksi', 'desc')
            ->paginate(15);

        return view('pages.entities.transaksi-masuk.index', compact('transaksi'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole(['AdminGudang', 'KepalaDivisi'])) {
            abort(403, 'Anda tidak memiliki wewenang untuk menambahkan stok barang.');
        }

        $barang = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->get();

        return view('pages.entities.transaksi-masuk.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => ['required', 'integer', 'exists:barang,id_barang'],
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'supplier' => ['nullable', 'string', 'max:100'],
        ], [
            'id_barang.required' => 'Barang wajib dipilih.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $transaksi = Transaksi::create([
                'id_user' => auth()->user()->id_user,
                'id_barang' => $validated['id_barang'],
                'jenis_transaksi' => 'MASUK',
                'tanggal' => $validated['tanggal'],
                'jumlah' => $validated['jumlah'],
            ]);

            TransaksiMasuk::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'supplier' => $validated['supplier'] ?? null,
            ]);

            $barang = Barang::find($validated['id_barang']);
            $barang->increment('stok', $validated['jumlah']);
        });

        ActivityLogger::log(
            'Transaksi Masuk',
            'Barang ID: ' . $validated['id_barang'] . ', Jumlah: ' . $validated['jumlah'] . ', Tanggal: ' . $validated['tanggal']
        );

        return redirect()
            ->route('transaksi-masuk.index')
            ->with('success', 'Transaksi masuk berhasil dicatat.');
    }
}

