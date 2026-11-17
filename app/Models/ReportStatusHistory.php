<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStatusHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['report_id', 'old_status', 'new_status', 'changed_by', 'notes'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by')->withTrashed();
    }

    public function getOldLabelAttribute(): ?string
    {
        return $this->old_status ? (Report::STATUS_LABELS[$this->old_status] ?? $this->old_status) : null;
    }

    public function getNewLabelAttribute(): string
    {
        return Report::STATUS_LABELS[$this->new_status] ?? $this->new_status;
    }
}
