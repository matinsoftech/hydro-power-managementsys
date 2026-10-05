<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogLineHour extends Model
{
    protected $fillable = [
        'import_batch_id', 'log_day_id', 'date', 'hour_label', 'hour_order',
        'v_ry', 'v_yb', 'v_br', 'i_r', 'i_y', 'i_b', 'freq', 'pf', 'kw', 'kvar', 'kwh',
    ];

    protected $casts = [
        'hour_order' => 'integer',
        'v_ry' => 'float', 'v_yb' => 'float', 'v_br' => 'float',
        'i_r' => 'float', 'i_y' => 'float', 'i_b' => 'float',
        'freq' => 'float', 'pf' => 'float', 'kw' => 'float', 'kvar' => 'float', 'kwh' => 'float',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(LogDay::class, 'log_day_id');
    }
}
