<?php

namespace App\Services;

use App\Models\GeneratorDailyLog;
use App\Models\GeneratorOutage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GeneratorLogReportService
{
    /**
     * @param  array{generator?: mixed, start?: mixed, end?: mixed}  $filters
     */
    public function daysQuery(array $filters = []): Builder
    {
        $query = GeneratorDailyLog::query();
        $this->applyFilters($query, $filters, 'date');

        return $query;
    }

    /**
     * @param  array{generator?: mixed, start?: mixed, end?: mixed}  $filters
     */
    public function eventsQuery(array $filters = []): Builder
    {
        $query = GeneratorOutage::query();
        $this->applyFilters($query, $filters, 'date');

        return $query;
    }

    /**
     * @param  array{generator?: mixed, start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $days = $this->daysQuery($filters)->orderBy('date')->orderBy('generator')->get();
        $events = $this->eventsQuery($filters)->orderBy('date')->orderBy('generator')->orderBy('serial')->get();

        $byGenerator = [];
        foreach ([1, 2] as $generator) {
            $generatorDays = $days->where('generator', $generator)->values();
            $generatorEvents = $events->where('generator', $generator)->values();
            $byGenerator[$generator] = [
                'days' => $generatorDays->count(),
                'events' => $generatorEvents->count(),
                'outage' => $this->formatSeconds($this->sumDurations($generatorEvents->pluck('outage_hrs'))),
                'running' => $this->formatSeconds($this->sumDurations($generatorDays->pluck('total_running'))),
                'generation_kwh' => round((float) $generatorDays->sum('total_generation_kwh'), 3),
            ];
        }

        return [
            'filters' => $filters,
            'kpis' => [
                'days' => $days->pluck('date')->unique()->count(),
                'rows' => $days->count(),
                'events' => $events->count(),
                'outage' => $this->formatSeconds($this->sumDurations($events->pluck('outage_hrs'))),
                'running' => $this->formatSeconds($this->sumDurations($days->pluck('total_running'))),
                'generation_kwh' => round((float) $days->sum('total_generation_kwh'), 3),
                'generators' => $byGenerator,
            ],
            'tables' => [
                'days' => $days,
                'events' => $events,
            ],
            'date_min' => $days->min('date'),
            'date_max' => $days->max('date'),
        ];
    }

    /**
     * @param  Builder<GeneratorDailyLog>|Builder<GeneratorOutage>  $query
     * @param  array{generator?: mixed, start?: mixed, end?: mixed}  $filters
     */
    private function applyFilters(Builder $query, array $filters, string $dateColumn): void
    {
        if (!empty($filters['generator'])) {
            $query->where('generator', (int) $filters['generator']);
        }
        if (!empty($filters['start'])) {
            $query->where($dateColumn, '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->where($dateColumn, '<=', $filters['end']);
        }
    }

    private function sumDurations(Collection $values): int
    {
        return (int) $values->sum(fn ($value) => $this->durationToSeconds($value));
    }

    private function durationToSeconds(mixed $value): int
    {
        $text = trim((string) $value);
        if (!preg_match('/^(\d+):(\d{2})(?::(\d{2}))?$/', $text, $match)) {
            return 0;
        }

        return ((int) $match[1]) * 3600 + ((int) $match[2]) * 60 + (int) ($match[3] ?? 0);
    }

    private function formatSeconds(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remain = $seconds % 60;

        return sprintf('%d:%02d:%02d', $hours, $minutes, $remain);
    }
}
