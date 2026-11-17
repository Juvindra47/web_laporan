<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PELAPOR = 'pelapor';
    public const ROLE_PETUGAS = 'petugas';

    public const ROLE_LABELS = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_PELAPOR => 'Siswa / Guru',
        self::ROLE_PETUGAS => 'Petugas Sarpras',
    ];

    protected $fillable = [
        'name', 'email', 'username', 'password', 'role',
        'phone', 'address', 'profile_photo', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool   { return $this->role === self::ROLE_ADMIN; }
    public function isPelapor(): bool { return $this->role === self::ROLE_PELAPOR; }
    public function isPetugas(): bool { return $this->role === self::ROLE_PETUGAS; }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'technician_id');
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABELS[$this->role] ?? $this->role;
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : 'https://ui-avatars.com/api/?background=0f1f3d&color=fff&bold=true&name=' . urlencode($this->name);
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => route('admin.dashboard'),
            self::ROLE_PETUGAS => route('petugas.dashboard'),
            default => route('pelapor.dashboard'),
        };
    }
}
