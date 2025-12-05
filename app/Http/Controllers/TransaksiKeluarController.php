<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiKeluar;
use App\Models\Barang;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiKeluarController extends Controller
{
    public function index(Request $request)
    {
        try {
            $transaksi = Transaksi::where('jenis_transaksi', 'KELUAR')
                ->with(['user', 'barang.kategori', 'transaksiKeluar'])
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $transaksi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data transaksi keluar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole('AdminGudang')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin Gudang yang dapat mencatat transaksi keluar'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'id_barang' => 'required|integer|exists:barang,id_barang',
                'tanggal' => 'required|date',
                'jumlah' => 'required|integer|min:1',
                'tujuan' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $barang = Barang::find($request->id_barang);
            if ($barang->stok < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $barang->stok
                ], 400);
            }

            $transaksi = Transaksi::create([
                'id_user' => $user->id_user,
                'id_barang' => $request->id_barang,
                'jenis_transaksi' => 'KELUAR',
                'tanggal' => $request->tanggal,
                'jumlah' => $request->jumlah,
            ]);

            $transaksiKeluar = TransaksiKeluar::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'tujuan' => $request->tujuan,
            ]);

            $barang->decrement('stok', $request->jumlah);

            $barang->refresh();
            if ($barang->stok < $barang->stok_minimum) {
                $targets = User::whereIn('role', ['AdminGudang', 'KepalaDivisi'])->pluck('id_user');
                foreach ($targets as $uid) {
                    Notifikasi::create([
                        'id_user' => $uid,
                        'pesan' => 'Stok barang "'.$barang->nama_barang.'" di bawah minimum ('.$barang->stok.' < '.$barang->stok_minimum.').',
                        'is_read' => false,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi keluar berhasil dicatat',
                'data' => $transaksi->load(['user', 'barang.kategori', 'transaksiKeluar'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat transaksi keluar',
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
                    'message' => 'Hanya Admin Gudang yang dapat memperbarui transaksi keluar'
                ], 403);
            }

            $transaksi = Transaksi::where('id_transaksi', $id)
                ->where('jenis_transaksi', 'KELUAR')
                ->first();

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi keluar tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'nullable|string',
                'tujuan' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('tujuan') && $transaksi->transaksiKeluar) {
                $transaksi->transaksiKeluar->update([
                    'tujuan' => $request->tujuan
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi keluar berhasil diperbarui',
                'data' => $transaksi->load(['user', 'barang.kategori', 'transaksiKeluar'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi keluar',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

