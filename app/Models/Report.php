<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Report extends Model
{
    use SoftDeletes;

    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_MENUNGGU_PETUGAS = 'menunggu_petugas';
    public const STATUS_SEDANG_DIKERJAKAN = 'sedang_dikerjakan';
    public const STATUS_MENUNGGU_VERIFIKASI_HASIL = 'menunggu_verifikasi_hasil';
    public const STATUS_SELESAI = 'selesai';

    public const STATUS_LABELS = [
        self::STATUS_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
        self::STATUS_DITOLAK => 'Ditolak',
        self::STATUS_MENUNGGU_PETUGAS => 'Menunggu Petugas',
        self::STATUS_SEDANG_DIKERJAKAN => 'Sedang Dikerjakan',
        self::STATUS_MENUNGGU_VERIFIKASI_HASIL => 'Menunggu Verifikasi Hasil',
        self::STATUS_SELESAI => 'Selesai',
    ];

    // Kelas warna badge Bootstrap (text-bg-*) untuk tiap status
    public const STATUS_BADGES = [
        self::STATUS_MENUNGGU_VERIFIKASI => 'warning',
        self::STATUS_DITOLAK => 'danger',
        self::STATUS_MENUNGGU_PETUGAS => 'info',
        self::STATUS_SEDANG_DIKERJAKAN => 'primary',
        self::STATUS_MENUNGGU_VERIFIKASI_HASIL => 'dark',
        self::STATUS_SELESAI => 'success',
    ];

    /**
     * Aturan alur status: status sekarang => [status tujuan => role yang berwenang].
     * Semua perubahan status HARUS lewat App\Services\ReportWorkflow.
     */
    public const TRANSITIONS = [
        self::STATUS_MENUNGGU_VERIFIKASI => [
            self::STATUS_DITOLAK => User::ROLE_ADMIN,
            self::STATUS_MENUNGGU_PETUGAS => User::ROLE_ADMIN,
        ],
        self::STATUS_MENUNGGU_PETUGAS => [
            self::STATUS_SEDANG_DIKERJAKAN => User::ROLE_PETUGAS,
        ],
        self::STATUS_SEDANG_DIKERJAKAN => [
            self::STATUS_MENUNGGU_VERIFIKASI_HASIL => User::ROLE_PETUGAS,
        ],
        self::STATUS_MENUNGGU_VERIFIKASI_HASIL => [
            self::STATUS_SELESAI => User::ROLE_ADMIN,
            self::STATUS_SEDANG_DIKERJAKAN => User::ROLE_ADMIN, // hasil belum sesuai, dikembalikan ke petugas
        ],
    ];

    protected $fillable = [
        'ticket_number', 'user_id', 'category_id', 'room_id',
        'title', 'description', 'photo', 'status', 'rejection_reason',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class)->withTrashed();
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function repairLogs(): HasMany
    {
        return $this->hasMany(RepairLog::class)->latest('created_at');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class)->latest('created_at')->latest('id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }

    public function getPhotoUrlAttribute(): string
    {
        return asset('storage/' . $this->photo);
    }

    public function canTransitionTo(string $newStatus, User $user): bool
    {
        $allowedRole = self::TRANSITIONS[$this->status][$newStatus] ?? null;

        return $allowedRole !== null && $allowedRole === $user->role;
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'SMK-' . now()->format('Ymd') . '-';

        do {
            $number = $prefix . strtoupper(Str::random(5));
        } while (self::withTrashed()->where('ticket_number', $number)->exists());

        return $number;
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function ($q, $s) {
                $q->where(function ($w) use ($s) {
                    $w->where('ticket_number', 'like', "%{$s}%")->orWhere('title', 'like', "%{$s}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['room_id'] ?? null, fn ($q, $v) => $q->where('room_id', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
    }
}
