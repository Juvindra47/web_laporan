<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Mencatat aktivitas pengguna (login, logout, tambah/ubah/hapus data, cetak).
 * Gagal mencatat tidak boleh menggagalkan aksi utama, jadi dibungkus try/catch.
 */
class ActivityLogger
{
    public static function log(string $action, string $description, ?Model $subject = null, ?User $user = null): void
    {
        try {
            $user ??= auth()->user();

            ActivityLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'description' => Str::limit($description, 480),
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'ip_address' => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
