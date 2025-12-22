<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = LogAktivitas::with('user')
            ->orderBy('timestamp', 'desc');

        // Role-based filtering
        if ($user->hasRole('PetugasOperasional')) {
            // Petugas hanya lihat miliknya
            $query->where('id_user', $user->id_user);
        } elseif ($user->hasRole('AdminGudang')) {
            // Admin monitor petugas (dan mungkin dirinya sendiri?)
            // Requirement: "Admin monitor petugas"
            // Kita asumsi Admin bisa lihat semua Log Petugas, dan Log dirinya sendiri.
            // Atau apakah Admin bisa lihat Log Kepala Divisi? Requirement: "Kepala Divisi akses penuh".
            // Implies Admin might NOT see Kepala Divisi logs.
            // Let's filter to show only Petugas and Admin logs, or just all if "Monitor Petugas" implies generic monitoring.
            // However, strictly: "Admin monitor petugas".
            // Let's filter logs where user role is PetugasOperasional or AdminGudang.
            // But role is in User table.
            $query->whereHas('user', function($q) {
                $q->whereIn('role', ['PetugasOperasional', 'AdminGudang']);
            });
        }
        // Kepala Divisi akses penuh (default, no filter added)

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('timestamp', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('timestamp', '<=', $request->tanggal_akhir);
        }

        $logs = $query->paginate(20);

        return view('pages.entities.log-aktivitas.index', compact('logs'));
    }
}

