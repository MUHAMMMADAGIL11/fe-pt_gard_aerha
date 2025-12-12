<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PermintaanBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Validator;

class PermintaanBarangController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $query = PermintaanBarang::with(['user', 'barang.kategori']);
            
            if ($user->hasRole('PetugasOperasional')) {
                $query->where('id_user', $user->id_user);
            }

            $permintaan = $query->orderBy('id_permintaan', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $permintaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $permintaan = PermintaanBarang::with(['user', 'barang.kategori'])->find($id);

            if (!$permintaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $permintaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('PetugasOperasional')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Petugas Operasional yang dapat mengajukan permintaan'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'id_barang' => 'required|integer|exists:barang,id_barang',
                'jumlah_diminta' => 'required|integer|min:1',
                'keterangan' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permintaan = PermintaanBarang::create([
                'id_user' => $user->id_user,
                'id_barang' => $request->id_barang,
                'jumlah_diminta' => $request->jumlah_diminta,
                'keterangan' => $request->keterangan,
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
            $barangNama = Barang::find($request->id_barang)->nama_barang ?? 'Barang';
            foreach ($admins as $adminId) {
                Notifikasi::create([
                    'id_user' => $adminId,
                    'pesan' => 'Permintaan baru: \"' . $barangNama . '\" sejumlah ' . $request->jumlah_diminta . ' pcs dari ' . ($user->username ?? 'Petugas') . '.',
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil diajukan',
                'data' => $permintaan->load(['user', 'barang.kategori'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('AdminGudang')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin Gudang yang dapat menyetujui permintaan'
                ], 403);
            }

            $permintaan = PermintaanBarang::with('barang')->find($id);

            if (!$permintaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak ditemukan'
                ], 404);
            }

            if ($permintaan->status !== 'Menunggu Persetujuan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan sudah diproses'
                ], 400);
            }

            if ($permintaan->barang->stok < $permintaan->jumlah_diminta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $permintaan->barang->stok
                ], 400);
            }

            $permintaan->update(['status' => 'Disetujui']);

            $permintaan->barang->decrement('stok', $permintaan->jumlah_diminta);

            Notifikasi::create([
                'id_user' => $permintaan->id_user,
                'pesan' => 'Permintaan barang \"' . ($permintaan->barang->nama_barang ?? 'Barang') . '\" sejumlah ' . $permintaan->jumlah_diminta . ' pcs disetujui.',
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil disetujui',
                'data' => $permintaan->load(['user', 'barang.kategori'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('AdminGudang')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin Gudang yang dapat menolak permintaan'
                ], 403);
            }

            $permintaan = PermintaanBarang::find($id);

            if (!$permintaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak ditemukan'
                ], 404);
            }

            if ($permintaan->status !== 'Menunggu Persetujuan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan sudah diproses'
                ], 400);
            }

            $permintaan->update(['status' => 'Ditolak']);

            Notifikasi::create([
                'id_user' => $permintaan->id_user,
                'pesan' => 'Permintaan barang \"' . ($permintaan->barang->nama_barang ?? 'Barang') . '\" sejumlah ' . $permintaan->jumlah_diminta . ' pcs ditolak.',
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan ditolak',
                'data' => $permintaan->load(['user', 'barang.kategori'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $permintaan = PermintaanBarang::find($id);

            if (!$permintaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Menunggu Persetujuan,Disetujui,Ditolak,Selesai',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!$user->hasRole('AdminGudang')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin Gudang yang dapat memperbarui status permintaan'
                ], 403);
            }

            $previousStatus = $permintaan->status;
            $permintaan->update(['status' => $request->status]);

            if ($request->status === 'Disetujui' && $previousStatus !== 'Disetujui') {
                if ($permintaan->barang->stok < $permintaan->jumlah_diminta) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $permintaan->barang->stok
                    ], 400);
                }
                $permintaan->barang->decrement('stok', $permintaan->jumlah_diminta);

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
            }

            return response()->json([
                'success' => true,
                'message' => 'Status permintaan berhasil diperbarui',
                'data' => $permintaan->load(['user', 'barang.kategori'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function selesaikanPermintaan(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('PetugasOperasional')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Petugas Operasional yang dapat menyelesaikan permintaan'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'id_permintaan' => 'required|integer|exists:permintaanbarang,id_permintaan',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permintaan = PermintaanBarang::find($request->id_permintaan);

            if (!$permintaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak ditemukan'
                ], 404);
            }

            if ($permintaan->status !== 'Disetujui') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya permintaan yang sudah disetujui yang dapat diselesaikan'
                ], 400);
            }
            $permintaan->update(['status' => 'Selesai']);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil diselesaikan',
                'data' => $permintaan->load(['user', 'barang.kategori'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
