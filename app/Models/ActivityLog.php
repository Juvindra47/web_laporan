<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_LABELS = [
        'login' => 'Login',
        'login_gagal' => 'Login gagal',
        'logout' => 'Logout',
        'buat' => 'Tambah data',
        'ubah' => 'Ubah data',
        'hapus' => 'Hapus data',
        'ekspor' => 'Cetak / ekspor',
    ];

    public const ACTION_BADGES = [
        'login' => 'success',
        'login_gagal' => 'danger',
        'logout' => 'secondary',
        'buat' => 'primary',
        'ubah' => 'warning',
        'hapus' => 'danger',
        'ekspor' => 'info',
    ];

    protected $fillable = ['user_id', 'action', 'description', 'subject_type', 'subject_id', 'ip_address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTION_LABELS[$this->action] ?? $this->action;
    }

    public function getActionBadgeAttribute(): string
    {
        return self::ACTION_BADGES[$this->action] ?? 'secondary';
    }
}
