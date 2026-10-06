<?php

namespace App\Services;

use App\Models\GeneratorDailyLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GeneratorLogReportService
{
    /**
     * @param  array{unit?: mixed, generator?: mixed, start?: mixed, end?: mixed}  $filters
     */
    public function daysQuery(array $filters = []): Builder
    {
        $query = GeneratorDailyLog::query();
        $this->applyFilters($query, $filters);

        return $query;
    }

    /**
     * @param  array{unit?: mixed, generator?: mixed, start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $unit = $filters['unit'] ?? $filters['generator'] ?? null;
        $filters['unit'] = $unit;

        $days = $this->daysQuery($filters)->orderBy('date')->orderBy('generator')->get();

        $byUnit = [];
        foreach ([1, 2] as $u) {
            $unitDays = $days->where('generator', $u)->values();
            $byUnit[$u] = [
                'days' => $unitDays->count(),
                'generation_kwh' => round((float) $unitDays->sum('total_generation_kwh'), 3),
                'avg_generation_kwh' => $unitDays->count()
                    ? round((float) $unitDays->avg('total_generation_kwh'), 2)
                    : 0,
                'first_initial' => optional($unitDays->first())->initial_reading,
                'last_final' => optional($unitDays->last())->final_reading,
            ];
        }

        $combined = $days->groupBy('date')->map(function (Collection $rows, $date) {
            $u1 = $rows->firstWhere('generator', 1);
            $u2 = $rows->firstWhere('generator', 2);

            return [
                'date' => $date,
                'u1_initial' => $u1?->initial_reading,
                'u1_final' => $u1?->final_reading,
                'u1_generation' => $u1?->total_generation_kwh,
                'u2_initial' => $u2?->initial_reading,
                'u2_final' => $u2?->final_reading,
                'u2_generation' => $u2?->total_generation_kwh,
            ];
        })->values();

        return [
            'filters' => $filters,
            'kpis' => [
                'days' => $days->pluck('date')->unique()->count(),
                'rows' => $days->count(),
                'generation_kwh' => round((float) $days->sum('total_generation_kwh'), 3),
                'units' => $byUnit,
            ],
            'tables' => [
                'days' => $days,
                'combined' => $combined,
            ],
            'date_min' => $days->min('date'),
            'date_max' => $days->max('date'),
        ];
    }

    /**
     * @param  Builder<GeneratorDailyLog>  $query
     * @param  array{unit?: mixed, generator?: mixed, start?: mixed, end?: mixed}  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        $unit = $filters['unit'] ?? $filters['generator'] ?? null;
        if (!empty($unit)) {
            $query->where('generator', (int) $unit);
        }
        if (!empty($filters['start'])) {
            $query->where('date', '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->where('date', '<=', $filters['end']);
        }
    }
}
