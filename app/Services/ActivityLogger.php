<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Catat aktivitas ke tabel logaktivitas.
     *
     * @param string $aktivitas Deskripsi singkat aktivitas
     * @param string|null $detail Detail tambahan (opsional)
     * @param int|null $userId Override user id (opsional)
     */
    public static function log(string $aktivitas, ?string $detail = null, ?int $userId = null): void
    {
        try {
            $user = Auth::user();
            $uid = $userId ?? ($user?->id_user);

            LogAktivitas::create([
                'id_user' => $uid,
                'aktivitas' => $aktivitas,
                'detail' => $detail,
                'timestamp' => now(),
            ]);
        } catch (\Throwable $e) {
            // Jangan ganggu alur utama; logging bersifat best-effort
        }
    }
}

