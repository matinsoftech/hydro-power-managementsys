<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogUnitHour extends Model
{
    protected $fillable = [
        'import_batch_id', 'log_day_id', 'unit', 'date', 'hour_label', 'hour_order',
        'rpm', 'v_ry', 'v_yb', 'v_br', 'i_r', 'i_y', 'i_b', 'freq', 'kvar', 'pf', 'kw', 'kwh', 'avr_v', 'avr_i',
    ];

    protected $casts = [
        'unit' => 'integer',
        'hour_order' => 'integer',
        'rpm' => 'float', 'v_ry' => 'float', 'v_yb' => 'float', 'v_br' => 'float',
        'i_r' => 'float', 'i_y' => 'float', 'i_b' => 'float', 'freq' => 'float',
        'kvar' => 'float', 'pf' => 'float', 'kw' => 'float', 'kwh' => 'float',
        'avr_v' => 'float', 'avr_i' => 'float',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(LogDay::class, 'log_day_id');
    }
}
