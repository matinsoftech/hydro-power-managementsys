<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogUnitDay extends Model
{
    protected $fillable = [
        'import_batch_id', 'log_day_id', 'unit', 'date',
        'total_running', 'total_outage', 'initial_reading', 'final_reading', 'total_generation_kwh',
        'first_kwh', 'last_kwh', 'energy_kwh', 'avg_kw', 'hour_count',
    ];

    protected $casts = [
        'unit' => 'integer',
        'initial_reading' => 'float', 'final_reading' => 'float', 'total_generation_kwh' => 'float',
        'first_kwh' => 'float', 'last_kwh' => 'float', 'energy_kwh' => 'float', 'avg_kw' => 'float',
        'hour_count' => 'integer',
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
