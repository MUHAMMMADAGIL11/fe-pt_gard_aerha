<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function scanPage(Request $request)
    {
        if ($code = $request->query('code')) {
            $barang = Barang::where('kode_barang', $code)->first();
            
            if ($barang) {
                return redirect()->route('barang.show', $barang->id_barang);
            }
            
            return redirect()->route('scan.index')->with('error', 'Barang dengan kode "' . $code . '" tidak ditemukan.');
        }

        return view('pages.scan.index');
    }

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $kategoriId = $request->get('kategori');

        $barang = Barang::with('kategori')
            ->when($q !== '', function($builder) use ($q) {
                $builder->where(function($w) use ($q) {
                    $w->where('nama_barang', 'like', "%$q%")
                      ->orWhere('kode_barang', 'like', "%$q%");
                });
            })
            ->when($kategoriId, function($builder) use ($kategoriId) {
                $builder->where('id_kategori', $kategoriId);
            })
            ->orderBy('nama_barang')
            ->paginate(10)
            ->appends(['q' => $q, 'kategori' => $kategoriId]);

        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('pages.entities.barang.index', compact('barang', 'q', 'categories'));
    }

    public function show(Barang $barang)
    {
        $barang->load('kategori');
        return view('pages.entities.barang.show', compact('barang'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('AdminGudang')) {
            abort(403, 'Hanya Admin Gudang yang dapat menambah barang.');
        }
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('pages.entities.barang.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('AdminGudang')) {
            abort(403, 'Hanya Admin Gudang yang dapat menambah barang.');
        }
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
        if (!auth()->user()->hasRole('AdminGudang')) {
            abort(403, 'Hanya Admin Gudang yang dapat mengedit barang.');
        }
        $barang = Barang::findOrFail($barangId);
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('pages.entities.barang.edit', compact('barang', 'categories'));
    }

    public function update(Request $request, int $barangId)
    {
        if (!auth()->user()->hasRole('AdminGudang')) {
            abort(403, 'Hanya Admin Gudang yang dapat mengedit barang.');
        }
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

    public function printLabel(int $barangId)
    {
        $barang = Barang::with('kategori')->findOrFail($barangId);
        return view('pages.entities.barang.print-label', compact('barang'));
    }

    public function destroy(int $barangId)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('AdminGudang')) {
            return redirect()
                ->route('barang.index')
                ->with('error', 'Hanya Admin Gudang yang dapat menghapus barang.');
        }

        $barang = Barang::findOrFail($barangId);
        $barang->delete();

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
