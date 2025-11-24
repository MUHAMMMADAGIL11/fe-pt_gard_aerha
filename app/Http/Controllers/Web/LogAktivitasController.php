<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')
            ->orderBy('timestamp', 'desc');

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

