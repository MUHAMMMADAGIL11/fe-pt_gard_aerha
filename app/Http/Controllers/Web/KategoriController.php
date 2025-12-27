<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::withCount('barang')
            ->orderBy('nama_kategori')
            ->paginate(10);

        return view('pages.entities.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('pages.entities.kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Kategori dengan nama tersebut sudah ada.',
        ]);

        Kategori::create($validated);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(int $kategoriId)
    {
        $kategori = Kategori::findOrFail($kategoriId);

        return view('pages.entities.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, int $kategoriId)
    {
        $kategori = Kategori::findOrFail($kategoriId);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori,' . $kategoriId . ',id_kategori'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Kategori dengan nama tersebut sudah ada.',
        ]);

        $kategori->update($validated);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(int $kategoriId)
    {
        $kategori = Kategori::findOrFail($kategoriId);

        if ($kategori->barang()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Kategori ini masih memiliki ' . $kategori->barang()->count() . ' barang. Kosongkan atau pindahkan barang terlebih dahulu.');
        }

        $kategori->delete();

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}

