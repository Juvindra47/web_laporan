<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepairLog extends Model
{
    use SoftDeletes;

    protected $fillable = ['report_id', 'technician_id', 'description', 'before_photo', 'after_photo'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class)->withTrashed();
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id')->withTrashed();
    }

    public function getBeforePhotoUrlAttribute(): ?string
    {
        return $this->before_photo ? asset('storage/' . $this->before_photo) : null;
    }

    public function getAfterPhotoUrlAttribute(): ?string
    {
        return $this->after_photo ? asset('storage/' . $this->after_photo) : null;
    }
}
