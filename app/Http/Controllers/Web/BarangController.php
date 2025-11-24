<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar barang pada halaman web.
     */
    public function index()
    {
        $barang = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->paginate(10);

        return view('pages.entities.barang.index', compact('barang'));
    }

    /**
     * Form tambah barang.
     */
    public function create()
    {
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('pages.entities.barang.create', compact('categories'));
    }

    /**
     * Simpan barang baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kategori' => ['required', 'integer', 'exists:kategori,id_kategori'],
            'kode_barang' => ['required', 'string', 'max:50', 'unique:barang,kode_barang'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
        ]);

        Barang::create($validated);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Form edit barang.
     */
    public function edit(int $barangId)
    {
        $barang = Barang::findOrFail($barangId);
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('pages.entities.barang.edit', compact('barang', 'categories'));
    }

    /**
     * Update barang yang ada.
     */
    public function update(Request $request, int $barangId)
    {
        $barang = Barang::findOrFail($barangId);

        $validated = $request->validate([
            'id_kategori' => ['required', 'integer', 'exists:kategori,id_kategori'],
            'kode_barang' => ['required', 'string', 'max:50', 'unique:barang,kode_barang,' . $barang->id_barang . ',id_barang'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
        ]);

        $barang->update($validated);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Hapus barang setelah konfirmasi.
     */
    public function destroy(int $barangId)
    {
        // Hanya Admin Gudang atau Kepala Divisi yang boleh menghapus
        $user = auth()->user();
        if (!$user || !$user->hasRole(['AdminGudang', 'KepalaDivisi'])) {
            return redirect()
                ->route('barang.index')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus barang.');
        }

        $barang = Barang::findOrFail($barangId);
        $barang->delete();

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}

