<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogDay extends Model
{
    protected $fillable = [
        'import_batch_id', 'date', 'company', 'month_label',
    ];

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function unitHours(): HasMany
    {
        return $this->hasMany(LogUnitHour::class);
    }

    public function lineHours(): HasMany
    {
        return $this->hasMany(LogLineHour::class);
    }

    public function meterHours(): HasMany
    {
        return $this->hasMany(LogMeterHour::class);
    }

    public function unitDays(): HasMany
    {
        return $this->hasMany(LogUnitDay::class);
    }

    public function unitOutages(): HasMany
    {
        return $this->hasMany(LogUnitOutage::class);
    }

    public static function fingerprintFromArray(array $row): string
    {
        return mb_strtolower(trim((string) ($row['date'] ?? '')));
    }
}
