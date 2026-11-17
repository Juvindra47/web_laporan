<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    protected $fillable = ['report_id', 'technician_id', 'assigned_by', 'assigned_at'];

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime'];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class)->withTrashed();
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id')->withTrashed();
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by')->withTrashed();
    }
}
