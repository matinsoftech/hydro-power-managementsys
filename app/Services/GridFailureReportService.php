<?php

namespace App\Services;

use App\Models\GridFailure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GridFailureReportService
{
    /**
     * @param  array{unit?: mixed, start?: mixed, end?: mixed}  $filters
     */
    public function query(array $filters = []): Builder
    {
        $query = GridFailure::query();

        if (!empty($filters['unit'])) {
            $query->where('unit', (int) $filters['unit']);
        }
        if (!empty($filters['start'])) {
            $query->where('date', '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->where('date', '<=', $filters['end']);
        }

        return $query;
    }

    /**
     * @param  array{unit?: mixed, start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $rows = $this->query($filters)
            ->orderBy('date')
            ->orderBy('from_hrs')
            ->orderBy('unit')
            ->get();

        $enriched = $rows->map(function (GridFailure $row) {
            $seconds = $this->durationToSeconds($row->duration_hrs);

            return [
                'id' => $row->id,
                'unit' => (int) $row->unit,
                'date' => $row->date,
                'from_hrs' => $row->from_hrs,
                'to_hrs' => $row->to_hrs,
                'synch_hrs' => $row->synch_hrs,
                'duration_hrs' => $row->duration_hrs,
                'duration_seconds' => $seconds,
                'reason' => $row->reason,
                'reason_category' => $this->categorizeReason($row->reason),
                'hour' => $this->extractHour($row->from_hrs),
            ];
        });

        $totalSeconds = (int) $enriched->sum('duration_seconds');
        $eventCount = $enriched->count();
        $unit1 = $enriched->where('unit', 1);
        $unit2 = $enriched->where('unit', 2);
        $daysAffected = $enriched->pluck('date')->unique()->count();

        $byDate = $enriched->groupBy('date')->map(function (Collection $dayRows, $date) {
            return [
                'date' => $date,
                'events' => $dayRows->count(),
                'unit1_events' => $dayRows->where('unit', 1)->count(),
                'unit2_events' => $dayRows->where('unit', 2)->count(),
                'downtime_seconds' => (int) $dayRows->sum('duration_seconds'),
                'downtime' => $this->formatDuration((int) $dayRows->sum('duration_seconds')),
            ];
        })->values();

        $byCategory = $enriched->groupBy('reason_category')->map(function (Collection $group, $category) {
            return [
                'category' => $category,
                'events' => $group->count(),
                'downtime_seconds' => (int) $group->sum('duration_seconds'),
                'downtime' => $this->formatDuration((int) $group->sum('duration_seconds')),
            ];
        })->sortByDesc('events')->values();

        $byHour = [];
        for ($h = 0; $h < 24; $h++) {
            $byHour[] = [
                'hour' => sprintf('%02d:00', $h),
                'events' => $enriched->where('hour', $h)->count(),
            ];
        }

        $topReasons = $enriched
            ->filter(fn ($r) => !empty($r['reason']))
            ->groupBy(fn ($r) => mb_strtolower(trim($r['reason'])))
            ->map(function (Collection $group) {
                $first = $group->first();
                return [
                    'reason' => $first['reason'],
                    'events' => $group->count(),
                    'downtime' => $this->formatDuration((int) $group->sum('duration_seconds')),
                    'downtime_seconds' => (int) $group->sum('duration_seconds'),
                ];
            })
            ->sortByDesc('events')
            ->take(10)
            ->values();

        $longest = $enriched
            ->sortByDesc('duration_seconds')
            ->take(10)
            ->values()
            ->map(function ($row) {
                return [
                    'unit' => 'Unit ' . $row['unit'],
                    'date' => $row['date'],
                    'from_hrs' => $row['from_hrs'] ?: '—',
                    'synch_hrs' => $row['synch_hrs'] ?: '—',
                    'duration' => $row['duration_hrs'] ?: $this->formatDuration($row['duration_seconds']),
                    'reason' => $row['reason'] ?: '—',
                    'category' => $row['reason_category'],
                ];
            });

        $neaEvents = $enriched->where('reason_category', 'NEA / Grid Trip');
        $plannedEvents = $enriched->where('reason_category', 'Planned / Normal Stop');
        $forcedCount = $eventCount - $plannedEvents->count();

        return [
            'filters' => $filters,
            'kpis' => [
                'total_events' => $eventCount,
                'total_downtime' => $this->formatDuration($totalSeconds),
                'total_downtime_seconds' => $totalSeconds,
                'avg_downtime' => $eventCount > 0
                    ? $this->formatDuration((int) round($totalSeconds / $eventCount))
                    : '0:00:00',
                'days_affected' => $daysAffected,
                'unit1_events' => $unit1->count(),
                'unit2_events' => $unit2->count(),
                'unit1_downtime' => $this->formatDuration((int) $unit1->sum('duration_seconds')),
                'unit2_downtime' => $this->formatDuration((int) $unit2->sum('duration_seconds')),
                'nea_trips' => $neaEvents->count(),
                'nea_downtime' => $this->formatDuration((int) $neaEvents->sum('duration_seconds')),
                'planned_stops' => $plannedEvents->count(),
                'forced_outages' => max(0, $forcedCount),
            ],
            'charts' => [
                'daily_labels' => $byDate->pluck('date')->values(),
                'daily_events' => $byDate->pluck('events')->values(),
                'daily_downtime_minutes' => $byDate->map(fn ($d) => round($d['downtime_seconds'] / 60, 1))->values(),
                'unit_labels' => ['Unit 1', 'Unit 2'],
                'unit_events' => [$unit1->count(), $unit2->count()],
                'unit_downtime_minutes' => [
                    round($unit1->sum('duration_seconds') / 60, 1),
                    round($unit2->sum('duration_seconds') / 60, 1),
                ],
                'category_labels' => $byCategory->pluck('category')->values(),
                'category_events' => $byCategory->pluck('events')->values(),
                'hour_labels' => collect($byHour)->pluck('hour')->values(),
                'hour_events' => collect($byHour)->pluck('events')->values(),
            ],
            'tables' => [
                'daily' => $byDate,
                'categories' => $byCategory,
                'top_reasons' => $topReasons,
                'longest' => $longest,
                'recent' => $enriched->sortByDesc('date')->take(15)->values()->map(function ($row) {
                    return [
                        'unit' => 'Unit ' . $row['unit'],
                        'date' => $row['date'],
                        'from_hrs' => $row['from_hrs'] ?: '—',
                        'to_hrs' => $row['to_hrs'] ?: '—',
                        'synch_hrs' => $row['synch_hrs'] ?: '—',
                        'duration' => $row['duration_hrs'] ?: '—',
                        'reason' => $row['reason'] ?: '—',
                        'category' => $row['reason_category'],
                    ];
                }),
            ],
            'date_min' => $enriched->min('date'),
            'date_max' => $enriched->max('date'),
        ];
    }

    public function categorizeReason(?string $reason): string
    {
        $r = mb_strtolower(trim((string) $reason));

        if ($r === '') {
            return 'Other';
        }
        if (str_contains($r, 'nea') || str_contains($r, 'grid gone') || str_contains($r, 'system trip')) {
            return 'NEA / Grid Trip';
        }
        if (str_contains($r, 'voltage')) {
            return 'Voltage Fluctuations';
        }
        if (str_contains($r, 'intake')) {
            return 'Intake Cleaning';
        }
        if (str_contains($r, 'over current') || str_contains($r, 'overcurrent')) {
            return 'Over Current';
        }
        if (str_contains($r, 'penstoke') || str_contains($r, 'penstock') || str_contains($r, 'pressure')) {
            return 'Penstock Pressure';
        }
        if (str_contains($r, 'busbar') || str_contains($r, 'vibration')) {
            return 'Busbar / Vibration';
        }
        if (str_contains($r, 'water') || str_contains($r, 'discharge')) {
            return 'Low Water / Discharge';
        }
        if (
            str_contains($r, 'normally stopped')
            || str_contains($r, 'shut down')
            || str_contains($r, 'shutdown')
            || str_contains($r, 'link change')
        ) {
            return 'Planned / Normal Stop';
        }

        return 'Other';
    }

    public function durationToSeconds(?string $duration): int
    {
        if (!$duration) {
            return 0;
        }
        $duration = trim($duration);
        if (!preg_match('/^(\d+):(\d{1,2})(?::(\d{1,2}))?$/', $duration, $m)) {
            return 0;
        }

        $h = (int) $m[1];
        $min = (int) $m[2];
        $s = isset($m[3]) ? (int) $m[3] : 0;

        return ($h * 3600) + ($min * 60) + $s;
    }

    public function formatDuration(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }

    private function extractHour(?string $time): ?int
    {
        if (!$time || !preg_match('/^(\d{1,2}):/', $time, $m)) {
            return null;
        }
        $h = (int) $m[1];
        return ($h >= 0 && $h <= 23) ? $h : null;
    }
}
