<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenerationReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_batch_id',
        'ad_initial_date',
        'ad_final_date',
        'bs_initial_date',
        'bs_final_date',
        'main_initial',
        'main_final',
        'main_generation_kwh',
        'check_initial',
        'check_final',
        'check_generation_kwh',
        'month_label',
        'company',
    ];

    protected $casts = [
        'main_initial' => 'float',
        'main_final' => 'float',
        'main_generation_kwh' => 'float',
        'check_initial' => 'float',
        'check_final' => 'float',
        'check_generation_kwh' => 'float',
    ];

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function fingerprint(): string
    {
        return self::fingerprintFromArray($this->toArray());
    }

    public static function fingerprintFromArray(array $row): string
    {
        $norm = static function ($v): string {
            if ($v === null || $v === '') {
                return '';
            }
            if (is_numeric($v)) {
                return rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.');
            }

            return mb_strtolower(trim((string) $v));
        };

        return implode('|', [
            $norm($row['bs_initial_date'] ?? ''),
            $norm($row['bs_final_date'] ?? ''),
            $norm($row['ad_initial_date'] ?? ''),
            $norm($row['ad_final_date'] ?? ''),
            $norm($row['main_initial'] ?? ''),
            $norm($row['main_final'] ?? ''),
            $norm($row['main_generation_kwh'] ?? ''),
            $norm($row['check_initial'] ?? ''),
            $norm($row['check_final'] ?? ''),
            $norm($row['check_generation_kwh'] ?? ''),
        ]);
    }
}
