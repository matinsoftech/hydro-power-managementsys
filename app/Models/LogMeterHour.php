<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogMeterHour extends Model
{
    protected $fillable = [
        'import_batch_id', 'log_day_id', 'date', 'hour_label', 'hour_order',
        'main_meter', 'check_meter', 'main_diff', 'check_diff',
    ];

    protected $casts = [
        'hour_order' => 'integer',
        'main_meter' => 'float', 'check_meter' => 'float',
        'main_diff' => 'float', 'check_diff' => 'float',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(LogDay::class, 'log_day_id');
    }
}
