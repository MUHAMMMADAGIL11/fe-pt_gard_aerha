<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        try {
            $barang = Barang::with('kategori')
                ->orderBy('nama_barang', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $barang = Barang::with('kategori')->find($id);

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ter-authenticate. Silakan login terlebih dahulu.'
                ], 401);
            }
            if (!$user->hasRole('AdminGudang')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin Gudang yang dapat menambah barang',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'id_kategori' => 'required|integer|exists:kategori,id_kategori',
                'kode_barang' => 'required|string|max:50|unique:barang,kode_barang',
                'nama_barang' => 'required|string',
                'stok' => 'nullable|integer|min:0',
                'stok_minimum' => 'nullable|integer|min:0',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $barang = Barang::create([
                'id_kategori' => $request->id_kategori,
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'stok' => $request->stok ?? 0,
                'stok_minimum' => $request->stok_minimum ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan',
                'data' => $barang
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user->hasRole('AdminGudang')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin Gudang yang dapat mengedit barang'
                ], 403);
            }

            $barang = Barang::find($id);
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'id_kategori' => 'sometimes|integer|exists:kategori,id_kategori',
                'kode_barang' => 'sometimes|string|max:50|unique:barang,kode_barang,' . $id . ',id_barang',
                'nama_barang' => 'sometimes|string',
                'stok' => 'sometimes|integer|min:0',
                'stok_minimum' => 'sometimes|integer|min:0',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $barang->update($request->only([
                'id_kategori',
                'kode_barang',
                'nama_barang',
                'stok',
                'stok_minimum'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diperbarui',
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user->hasRole('AdminGudang')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin Gudang yang dapat menghapus barang'
                ], 403);
            }

            $barang = Barang::find($id);
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            $barang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStok(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user->hasRole('AdminGudang')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin Gudang yang dapat mengupdate stok'
                ], 403);
            }

            $barang = Barang::find($id);
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'stok' => 'required|integer|min:0',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $barang->update(['stok' => $request->stok]);

            $barang->refresh();
            if ($barang->stok < $barang->stok_minimum) {
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
                $message = 'Stok barang "'.$barang->nama_barang.'" di bawah minimum ('.$barang->stok.' < '.$barang->stok_minimum.').';
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

            return response()->json([
                'success' => true,
                'message' => 'Stok barang berhasil diperbarui',
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cekMinimum($id)
    {
        try {
            $barang = Barang::find($id);
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            $isBelowMinimum = $barang->stok < $barang->stok_minimum;

            return response()->json([
                'success' => true,
                'data' => [
                    'barang' => $barang,
                    'is_below_minimum' => $isBelowMinimum,
                    'selisih' => $isBelowMinimum ? ($barang->stok_minimum - $barang->stok) : 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek stok minimum',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function lihatStokBarang(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user->hasRole('PetugasOperasional')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Petugas Operasional yang dapat melihat stok barang'
                ], 403);
            }

            $barang = Barang::with('kategori')
                ->select('id_barang', 'kode_barang', 'nama_barang', 'stok', 'stok_minimum')
                ->orderBy('nama_barang', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data stok barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

