<?php

namespace App\Services;

use App\Models\GenerationReading;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GenerationReportService
{
    /**
     * @param  array{start?: mixed, end?: mixed}  $filters
     */
    public function query(array $filters = []): Builder
    {
        $query = GenerationReading::query();

        if (!empty($filters['start'])) {
            $query->where('bs_initial_date', '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->where('bs_initial_date', '<=', $filters['end']);
        }

        return $query;
    }

    /**
     * @param  array{start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $rows = $this->query($filters)
            ->orderBy('bs_initial_date')
            ->orderBy('id')
            ->get();

        $enriched = $rows->map(function (GenerationReading $row) {
            $main = (float) ($row->main_generation_kwh ?? 0);
            $check = (float) ($row->check_generation_kwh ?? 0);
            $meterType = $this->meterDayType($main, $check);

            return [
                'id' => $row->id,
                'bs_initial_date' => $row->bs_initial_date,
                'bs_final_date' => $row->bs_final_date,
                'ad_initial_date' => $row->ad_initial_date,
                'ad_final_date' => $row->ad_final_date,
                'main_initial' => $row->main_initial,
                'main_final' => $row->main_final,
                'main_generation_kwh' => $main,
                'check_initial' => $row->check_initial,
                'check_final' => $row->check_final,
                'check_generation_kwh' => $check,
                'combined_kwh' => $main + $check,
                'variance_kwh' => $main - $check,
                'meter_type' => $meterType,
                'month_label' => $row->month_label,
                'company' => $row->company,
            ];
        });

        $dayCount = $enriched->count();
        $totalMain = (float) $enriched->sum('main_generation_kwh');
        $totalCheck = (float) $enriched->sum('check_generation_kwh');
        $totalCombined = $totalMain + $totalCheck;

        $byDate = $enriched->groupBy('bs_initial_date')->map(function (Collection $dayRows, $date) {
            return [
                'date' => $date,
                'main_kwh' => round((float) $dayRows->sum('main_generation_kwh'), 3),
                'check_kwh' => round((float) $dayRows->sum('check_generation_kwh'), 3),
                'combined_kwh' => round((float) $dayRows->sum('combined_kwh'), 3),
                'variance_kwh' => round((float) $dayRows->sum('variance_kwh'), 3),
            ];
        })->values();

        $byMeterType = $enriched->groupBy('meter_type')->map(function (Collection $group, $type) {
            return [
                'type' => $type,
                'days' => $group->count(),
                'main_kwh' => round((float) $group->sum('main_generation_kwh'), 3),
                'check_kwh' => round((float) $group->sum('check_generation_kwh'), 3),
            ];
        })->sortByDesc('days')->values();

        $peakMain = $byDate->sortByDesc('main_kwh')->first();
        $peakCheck = $byDate->sortByDesc('check_kwh')->first();

        $topMainDays = $byDate->sortByDesc('main_kwh')->take(10)->values();
        $topCheckDays = $byDate->sortByDesc('check_kwh')->take(10)->values();

        $varianceDays = $byDate
            ->filter(fn ($d) => $d['check_kwh'] > 0)
            ->sortByDesc(fn ($d) => abs($d['variance_kwh']))
            ->take(10)
            ->values();

        $cumulativeMain = 0.0;
        $cumulativeCheck = 0.0;
        $cumulativeLabels = [];
        $cumulativeMainSeries = [];
        $cumulativeCheckSeries = [];
        foreach ($byDate as $day) {
            $cumulativeMain += $day['main_kwh'];
            $cumulativeCheck += $day['check_kwh'];
            $cumulativeLabels[] = $day['date'];
            $cumulativeMainSeries[] = round($cumulativeMain, 3);
            $cumulativeCheckSeries[] = round($cumulativeCheck, 3);
        }

        $mainOnlyDays = $enriched->where('meter_type', 'Main meter only')->count();
        $checkOnlyDays = $enriched->where('meter_type', 'Check meter only')->count();
        $bothDays = $enriched->where('meter_type', 'Both meters')->count();

        return [
            'filters' => $filters,
            'kpis' => [
                'total_days' => $dayCount,
                'total_main_kwh' => round($totalMain, 3),
                'total_check_kwh' => round($totalCheck, 3),
                'total_combined_kwh' => round($totalCombined, 3),
                'avg_main_kwh' => $dayCount > 0 ? round($totalMain / $dayCount, 2) : 0,
                'avg_check_kwh' => $dayCount > 0 ? round($totalCheck / $dayCount, 2) : 0,
                'main_only_days' => $mainOnlyDays,
                'check_only_days' => $checkOnlyDays,
                'both_meter_days' => $bothDays,
                'peak_main_date' => $peakMain['date'] ?? null,
                'peak_main_kwh' => $peakMain['main_kwh'] ?? 0,
                'peak_check_date' => $peakCheck['date'] ?? null,
                'peak_check_kwh' => $peakCheck['check_kwh'] ?? 0,
                'check_share_pct' => $totalCombined > 0 ? round(($totalCheck / $totalCombined) * 100, 1) : 0,
            ],
            'charts' => [
                'daily_labels' => $byDate->pluck('date')->values(),
                'daily_main_kwh' => $byDate->pluck('main_kwh')->values(),
                'daily_check_kwh' => $byDate->pluck('check_kwh')->values(),
                'daily_variance_kwh' => $byDate->pluck('variance_kwh')->values(),
                'meter_labels' => ['Main meter', 'Check meter'],
                'meter_totals' => [round($totalMain, 3), round($totalCheck, 3)],
                'meter_type_labels' => $byMeterType->pluck('type')->values(),
                'meter_type_days' => $byMeterType->pluck('days')->values(),
                'cumulative_labels' => $cumulativeLabels,
                'cumulative_main' => $cumulativeMainSeries,
                'cumulative_check' => $cumulativeCheckSeries,
            ],
            'tables' => [
                'daily' => $byDate,
                'meter_types' => $byMeterType,
                'top_main_days' => $topMainDays,
                'top_check_days' => $topCheckDays,
                'variance_days' => $varianceDays,
                'recent' => $enriched->sortByDesc('bs_initial_date')->take(15)->values()->map(function ($row) {
                    return [
                        'bs_initial_date' => $row['bs_initial_date'],
                        'bs_final_date' => $row['bs_final_date'] ?: '—',
                        'ad_initial_date' => $row['ad_initial_date'] ?: '—',
                        'main_kwh' => number_format($row['main_generation_kwh'], 0),
                        'check_kwh' => number_format($row['check_generation_kwh'], 0),
                        'meter_type' => $row['meter_type'],
                        'month_label' => $row['month_label'] ?: '—',
                    ];
                }),
            ],
            'date_min' => $enriched->min('bs_initial_date'),
            'date_max' => $enriched->max('bs_initial_date'),
        ];
    }

    private function meterDayType(float $main, float $check): string
    {
        $hasMain = $main > 0;
        $hasCheck = $check > 0;

        if ($hasMain && $hasCheck) {
            return 'Both meters';
        }
        if ($hasMain) {
            return 'Main meter only';
        }
        if ($hasCheck) {
            return 'Check meter only';
        }

        return 'No generation';
    }
}
