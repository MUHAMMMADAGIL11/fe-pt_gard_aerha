<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PermintaanBarang;
use App\Models\Barang;
use App\Models\Notifikasi;
use App\Models\User;
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
        $user = auth()->user();
        if (!$user || !$user->hasRole('PetugasOperasional')) {
            return redirect()
                ->route('permintaan-barang.index')
                ->with('error', 'Hanya Petugas Operasional yang dapat mengajukan permintaan.');
        }

        $validated = $request->validate([
            'id_barang' => ['required', 'integer', 'exists:barang,id_barang'],
            'jumlah_diminta' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'id_barang.required' => 'Barang wajib dipilih.',
            'jumlah_diminta.required' => 'Jumlah wajib diisi.',
            'jumlah_diminta.min' => 'Jumlah minimal 1.',
        ]);

        try {
            $permintaan = PermintaanBarang::create([
                'id_user' => $user->id_user,
                'id_barang' => $validated['id_barang'],
                'jumlah_diminta' => $validated['jumlah_diminta'],
                'keterangan' => $request->input('keterangan'),
                'status' => 'Menunggu Persetujuan',
            ]);

            $admins = User::where(function ($q) {
                    $q->where('role', 'AdminGudang')
                      ->orWhere(function ($qq) {
                          $qq->whereNull('role')
                             ->where(function ($qn) {
                                 $qn->where('username', 'like', '%admin%')
                                    ->orWhere('username', 'like', '%gudang%');
                             });
                      });
                })
                ->pluck('id_user');
            $barangNama = Barang::find($validated['id_barang'])->nama_barang ?? 'Barang';
            foreach ($admins as $adminId) {
                Notifikasi::create([
                    'id_user' => $adminId,
                    'pesan' => 'Permintaan baru: \"' . $barangNama . '\" sejumlah ' . $validated['jumlah_diminta'] . ' pcs dari ' . ($user->username ?? 'Petugas') . '.',
                    'is_read' => false,
                ]);
            }

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

        $permintaan->barang->refresh();
        if ($permintaan->barang->stok < $permintaan->barang->stok_minimum) {
            $targets = User::where(function ($q) {
                    $q->whereIn('role', ['AdminGudang', 'KepalaDivisi'])
                      ->orWhere(function ($qq) {
                          $qq->whereNull('role')
                             ->where(function ($qn) {
                                 $qn->where('username', 'like', '%admin%')
                                    ->orWhere('username', 'like', '%gudang%')
                                    ->orWhere('username', 'like', '%kepala%')
                                    ->orWhere('username', 'like', '%kadiv%')
                                    ->orWhere('username', 'like', '%divisi%');
                             });
                      });
                })
                ->pluck('id_user');
            $message = 'Stok barang "'.$permintaan->barang->nama_barang.'" di bawah minimum ('.$permintaan->barang->stok.' < '.$permintaan->barang->stok_minimum.').';
            foreach ($targets as $uid) {
                $exists = Notifikasi::where('id_user', $uid)
                    ->where('pesan', $message)
                    ->where('is_read', false)
                    ->exists();
                if (!$exists) {
                    Notifikasi::create([
                        'id_user' => $uid,
                        'pesan' => $message,
                        'is_read' => false,
                    ]);
                }
            }
        }

        ActivityLogger::log(
            'Permintaan Barang Disetujui',
            'Permintaan ID: ' . $permintaan->id_permintaan . ', Barang ID: ' . $permintaan->id_barang . ', Jumlah: ' . $permintaan->jumlah_diminta
        );

        Notifikasi::create([
            'id_user' => $permintaan->id_user,
            'pesan' => 'Permintaan barang \"' . ($permintaan->barang->nama_barang ?? 'Barang') . '\" sejumlah ' . $permintaan->jumlah_diminta . ' pcs disetujui.',
            'is_read' => false,
        ]);

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

        Notifikasi::create([
            'id_user' => $permintaan->id_user,
            'pesan' => 'Permintaan barang \"' . ($permintaan->barang->nama_barang ?? 'Barang') . '\" sejumlah ' . $permintaan->jumlah_diminta . ' pcs ditolak.',
            'is_read' => false,
        ]);

        return back()->with('success', 'Permintaan ditolak.');
    }
}
