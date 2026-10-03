<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ImportBatch extends Model
{
    use HasFactory;

    public const TYPE_FAILURE = 'failure';
    public const TYPE_GENERATION = 'generation';

    protected $fillable = [
        'type',
        'original_filename',
        'stored_path',
        'record_count',
        'skipped_count',
        'duplicate_count',
        'imported_by',
        'imported_at',
        'undone_at',
        'undone_by',
        'notes',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
        'undone_at' => 'datetime',
        'record_count' => 'integer',
        'skipped_count' => 'integer',
        'duplicate_count' => 'integer',
    ];

    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function undoer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'undone_by');
    }

    public function gridFailures(): HasMany
    {
        return $this->hasMany(GridFailure::class);
    }

    public function generationReadings(): HasMany
    {
        return $this->hasMany(GenerationReading::class);
    }

    public function isUndone(): bool
    {
        return $this->undone_at !== null;
    }

    public function canUndo(): bool
    {
        return !$this->isUndone() && $this->record_count > 0;
    }

    public function deleteStoredFile(): void
    {
        if ($this->stored_path && Storage::disk('local')->exists($this->stored_path)) {
            Storage::disk('local')->delete($this->stored_path);
        }
    }
}
