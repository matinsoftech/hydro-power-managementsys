<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogUnitOutage extends Model
{
    protected $fillable = [
        'import_batch_id', 'log_day_id', 'unit', 'date', 'serial',
        'trip_to', 'resume_hrs', 'synch_hrs', 'outage_hrs', 'reason',
    ];

    protected $casts = [
        'unit' => 'integer',
        'serial' => 'integer',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(LogDay::class, 'log_day_id');
    }

    public function getUnitLabelAttribute(): string
    {
        return 'Unit ' . $this->unit;
    }
}
