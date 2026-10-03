<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GridFailure extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_batch_id',
        'unit',
        'date',
        'from_hrs',
        'to_hrs',
        'synch_hrs',
        'duration_hrs',
        'reason',
        'month_label',
        'company',
    ];

    public function importBatch()
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function fingerprint(): string
    {
        return implode('|', [
            (int) $this->unit,
            (string) $this->date,
            (string) ($this->from_hrs ?? ''),
            (string) ($this->to_hrs ?? ''),
            (string) ($this->synch_hrs ?? ''),
            (string) ($this->duration_hrs ?? ''),
            mb_strtolower(trim((string) ($this->reason ?? ''))),
        ]);
    }

    public static function fingerprintFromArray(array $row): string
    {
        return implode('|', [
            (int) ($row['unit'] ?? 0),
            (string) ($row['date'] ?? ''),
            (string) ($row['from_hrs'] ?? ''),
            (string) ($row['to_hrs'] ?? ''),
            (string) ($row['synch_hrs'] ?? ''),
            (string) ($row['duration_hrs'] ?? ''),
            mb_strtolower(trim((string) ($row['reason'] ?? ''))),
        ]);
    }

    public function getUnitLabelAttribute(): string
    {
        return 'Unit ' . $this->unit;
    }
}
