<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneratorDailyLog extends Model
{
    protected $fillable = [
        'import_batch_id',
        'generator',
        'date',
        'total_running',
        'total_outage',
        'initial_reading',
        'final_reading',
        'total_generation_kwh',
        'company',
        'month_label',
    ];

    protected $casts = [
        'generator' => 'integer',
        'initial_reading' => 'float',
        'final_reading' => 'float',
        'total_generation_kwh' => 'float',
    ];

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function outages(): HasMany
    {
        return $this->hasMany(GeneratorOutage::class);
    }

    public function getGeneratorLabelAttribute(): string
    {
        return 'Generator ' . $this->generator;
    }

    public function fingerprint(): string
    {
        return self::fingerprintFromArray($this->toArray());
    }

    public static function fingerprintFromArray(array $row): string
    {
        $norm = static function ($value): string {
            if ($value === null || $value === '') {
                return '';
            }
            if (is_numeric($value)) {
                return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
            }

            return mb_strtolower(trim((string) $value));
        };

        return implode('|', [
            (string) ($row['generator'] ?? ''),
            $norm($row['date'] ?? ''),
            $norm($row['total_running'] ?? ''),
            $norm($row['total_outage'] ?? ''),
            $norm($row['initial_reading'] ?? ''),
            $norm($row['final_reading'] ?? ''),
            $norm($row['total_generation_kwh'] ?? ''),
        ]);
    }
}
