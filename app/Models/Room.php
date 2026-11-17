<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = ['room_name', 'building', 'description'];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->building ? "{$this->room_name} ({$this->building})" : $this->room_name;
    }
}
