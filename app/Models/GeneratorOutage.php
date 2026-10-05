<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratorOutage extends Model
{
    protected $fillable = [
        'import_batch_id',
        'generator_daily_log_id',
        'generator',
        'date',
        'serial',
        'trip_to',
        'resume_hrs',
        'synch_hrs',
        'outage_hrs',
        'reason',
    ];

    protected $casts = [
        'generator' => 'integer',
        'serial' => 'integer',
    ];

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(GeneratorDailyLog::class, 'generator_daily_log_id');
    }

    public function getGeneratorLabelAttribute(): string
    {
        return 'Generator ' . $this->generator;
    }

    public static function fingerprintFromArray(array $row): string
    {
        $norm = static function ($value): string {
            return mb_strtolower(trim((string) ($value ?? '')));
        };

        return implode('|', [
            (string) ($row['generator'] ?? ''),
            $norm($row['date'] ?? ''),
            $norm($row['serial'] ?? ''),
            $norm($row['trip_to'] ?? ''),
            $norm($row['resume_hrs'] ?? ''),
            $norm($row['synch_hrs'] ?? ''),
            $norm($row['outage_hrs'] ?? ''),
            $norm($row['reason'] ?? ''),
        ]);
    }
}
