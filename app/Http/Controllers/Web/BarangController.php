<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $barang = Barang::with('kategori')
            ->when($q !== '', function($builder) use ($q) {
                $builder->where(function($w) use ($q) {
                    $w->where('nama_barang', 'like', "%$q%")
                      ->orWhere('kode_barang', 'like', "%$q%");
                });
            })
            ->orderBy('nama_barang')
            ->paginate(10)
            ->appends(['q' => $q]);

        return view('pages.entities.barang.index', compact('barang', 'q'));
    }

    public function create()
    {
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('pages.entities.barang.create', compact('categories'));
    }

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

    public function edit(int $barangId)
    {
        $barang = Barang::findOrFail($barangId);
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('pages.entities.barang.edit', compact('barang', 'categories'));
    }

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

    public function destroy(int $barangId)
    {
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

