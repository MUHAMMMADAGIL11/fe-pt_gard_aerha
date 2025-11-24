<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PermintaanBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;

class PermintaanBarangController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = PermintaanBarang::with(['user', 'barang.kategori'])
            ->orderBy('id_permintaan', 'desc');

        if ($user->hasRole('PetugasOperasional')) {
            $query->where('id_user', $user->id_user);
        }

        $permintaan = $query->paginate(15);

        return view('pages.entities.permintaan-barang.index', compact('permintaan'));
    }

    public function create()
    {
        $barang = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->get();

        return view('pages.entities.permintaan-barang.create', compact('barang'));
    }

    public function store(Request $request)
    {
        // Pastikan hanya Petugas Operasional yang dapat mengajukan
        $user = auth()->user();
        if (!$user || !$user->hasRole('PetugasOperasional')) {
            return redirect()
                ->route('permintaan-barang.index')
                ->with('error', 'Hanya Petugas Operasional yang dapat mengajukan permintaan.');
        }

        $validated = $request->validate([
            'id_barang' => ['required', 'integer', 'exists:barang,id_barang'],
            'jumlah_diminta' => ['required', 'integer', 'min:1'],
        ], [
            'id_barang.required' => 'Barang wajib dipilih.',
            'jumlah_diminta.required' => 'Jumlah wajib diisi.',
            'jumlah_diminta.min' => 'Jumlah minimal 1.',
        ]);

        try {
            PermintaanBarang::create([
                'id_user' => $user->id_user,
                'id_barang' => $validated['id_barang'],
                'jumlah_diminta' => $validated['jumlah_diminta'],
                'status' => 'Menunggu Persetujuan',
            ]);

            ActivityLogger::log(
                'Permintaan Barang Dibuat',
                'Barang ID: ' . $validated['id_barang'] . ', Jumlah: ' . $validated['jumlah_diminta']
            );

            return redirect()
                ->route('permintaan-barang.index')
                ->with('success', 'Permintaan berhasil diajukan.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan permintaan: ' . $e->getMessage());
        }
    }

    public function approve(int $permintaanId)
    {
        $permintaan = PermintaanBarang::with('barang')->findOrFail($permintaanId);

        if ($permintaan->status !== 'Menunggu Persetujuan') {
            return back()->with('error', 'Permintaan sudah diproses.');
        }

        if ($permintaan->barang->stok < $permintaan->jumlah_diminta) {
            return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $permintaan->barang->stok);
        }

        DB::transaction(function () use ($permintaan) {
            $permintaan->update(['status' => 'Disetujui']);
            $permintaan->barang->decrement('stok', $permintaan->jumlah_diminta);
        });

        ActivityLogger::log(
            'Permintaan Barang Disetujui',
            'Permintaan ID: ' . $permintaan->id_permintaan . ', Barang ID: ' . $permintaan->id_barang . ', Jumlah: ' . $permintaan->jumlah_diminta
        );

        return back()->with('success', 'Permintaan berhasil disetujui.');
    }

    public function reject(int $permintaanId)
    {
        $permintaan = PermintaanBarang::findOrFail($permintaanId);

        if ($permintaan->status !== 'Menunggu Persetujuan') {
            return back()->with('error', 'Permintaan sudah diproses.');
        }

        $permintaan->update(['status' => 'Ditolak']);

        ActivityLogger::log(
            'Permintaan Barang Ditolak',
            'Permintaan ID: ' . $permintaan->id_permintaan . ', Barang ID: ' . $permintaan->id_barang . ', Jumlah: ' . $permintaan->jumlah_diminta
        );

        return back()->with('success', 'Permintaan ditolak.');
    }
}

