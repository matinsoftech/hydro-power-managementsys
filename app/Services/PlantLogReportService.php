<?php

namespace App\Services;

use App\Models\LogDay;
use App\Models\LogLineHour;
use App\Models\LogMeterHour;
use App\Models\LogUnitDay;
use App\Models\LogUnitHour;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PlantLogReportService
{
    public const SECTION_SUMMARY = 'summary';
    public const SECTION_GENERATOR_VOLTAGE = 'generator_voltage';
    public const SECTION_GENERATOR_CURRENT = 'generator_current';
    public const SECTION_LINE_VOLTAGE = 'line_voltage';
    public const SECTION_LINE_CURRENT = 'line_current';
    public const SECTION_LINE_PANEL = 'line_panel';
    public const SECTION_METER = 'meter';

    /** @return array<string, string> */
    public static function sections(): array
    {
        return [
            self::SECTION_SUMMARY => 'Daily summary',
            self::SECTION_GENERATOR_VOLTAGE => 'Generator Voltage',
            self::SECTION_GENERATOR_CURRENT => 'Generator Current',
            self::SECTION_LINE_VOLTAGE => 'Line Voltage',
            self::SECTION_LINE_CURRENT => 'Line Current',
            self::SECTION_LINE_PANEL => '11 kV Line Panel',
            self::SECTION_METER => 'Meter Reading',
        ];
    }

    public static function normalizeSection(?string $section): string
    {
        $section = $section ?: self::SECTION_SUMMARY;

        return array_key_exists($section, self::sections())
            ? $section
            : self::SECTION_SUMMARY;
    }

    public static function usesUnitFilter(string $section): bool
    {
        return in_array($section, [
            self::SECTION_SUMMARY,
            self::SECTION_GENERATOR_VOLTAGE,
            self::SECTION_GENERATOR_CURRENT,
        ], true);
    }

    /** @param array{section?: mixed, unit?: mixed, start?: mixed, end?: mixed} $filters */
    public function unitDaysQuery(array $filters = []): Builder
    {
        $query = LogUnitDay::query();
        $this->applyDateAndUnit($query, $filters);

        return $query;
    }

    /**
     * @param  array{section?: mixed, unit?: mixed, start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = [], int $perPage = 100): array
    {
        $section = self::normalizeSection($filters['section'] ?? null);
        $filters['section'] = $section;

        $unitDays = $this->unitDaysQuery($filters)->orderBy('date')->orderBy('unit')->get();
        $hourQuery = LogUnitHour::query();
        $this->applyDateAndUnit($hourQuery, $filters);
        $unitHours = $hourQuery->orderBy('date')->orderBy('unit')->orderBy('hour_order')->get();

        $lineQuery = LogLineHour::query();
        $this->applyDate($lineQuery, $filters);
        $lineHours = $lineQuery->orderBy('date')->orderBy('hour_order')->get();

        $meterQuery = LogMeterHour::query();
        $this->applyDate($meterQuery, $filters);
        $meters = $meterQuery->orderBy('date')->orderBy('hour_order')->get();

        $byUnit = [];
        foreach ([1, 2] as $unit) {
            $ud = $unitDays->where('unit', $unit)->values();
            $byUnit[$unit] = [
                'days' => $ud->count(),
                'energy_kwh' => round((float) $ud->sum('energy_kwh'), 3),
                'avg_kw' => $ud->count() ? round((float) $ud->avg('avg_kw'), 2) : 0,
                'hour_rows' => $unitHours->where('unit', $unit)->count(),
            ];
        }

        $dateBounds = $this->dateBounds($filters, $unitDays, $lineHours, $meters);

        $payload = match ($section) {
            self::SECTION_GENERATOR_VOLTAGE => $this->buildUnitVoltage($unitHours, $filters, $perPage),
            self::SECTION_GENERATOR_CURRENT => $this->buildUnitCurrent($unitHours, $filters, $perPage),
            self::SECTION_LINE_VOLTAGE => $this->buildLineVoltage($lineHours, $filters, $perPage),
            self::SECTION_LINE_CURRENT => $this->buildLineCurrent($lineHours, $filters, $perPage),
            self::SECTION_LINE_PANEL => $this->buildLinePanel($lineHours, $filters, $perPage),
            self::SECTION_METER => $this->buildMeter($meters, $filters, $perPage),
            default => $this->buildSummary($unitDays, $unitHours, $meters, $byUnit),
        };

        return array_merge([
            'filters' => $filters,
            'section' => $section,
            'section_label' => self::sections()[$section],
            'sections' => self::sections(),
            'uses_unit' => self::usesUnitFilter($section),
            'date_min' => $dateBounds['min'],
            'date_max' => $dateBounds['max'],
        ], $payload);
    }

    /**
     * @param  Collection<int, LogUnitDay>  $unitDays
     * @param  Collection<int, LogUnitHour>  $unitHours
     * @param  Collection<int, LogMeterHour>  $meters
     * @param  array<int, array<string, mixed>>  $byUnit
     * @return array<string, mixed>
     */
    private function buildSummary(Collection $unitDays, Collection $unitHours, Collection $meters, array $byUnit): array
    {
        return [
            'kpis' => [
                'days' => $unitDays->pluck('date')->unique()->count(),
                'energy_kwh' => round((float) $unitDays->sum('energy_kwh'), 3),
                'main_diff' => round((float) $meters->sum(fn ($m) => max(0, (float) ($m->main_diff ?? 0))), 3),
                'units' => $byUnit,
                'hour_rows' => $unitHours->count(),
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'BS Date'],
                ['key' => 'unit', 'label' => 'Unit'],
                ['key' => 'hour_count', 'label' => 'Hours'],
                ['key' => 'avg_kw', 'label' => 'Avg KW'],
                ['key' => 'energy_kwh', 'label' => 'Energy (kWh)'],
                ['key' => 'first_kwh', 'label' => 'First KWH'],
                ['key' => 'last_kwh', 'label' => 'Last KWH'],
            ],
            'rows' => $unitDays,
            'paginator' => null,
            'empty' => $unitDays->isEmpty(),
        ];
    }

    /**
     * @param  Collection<int, LogUnitHour>  $hours
     * @param  array{unit?: mixed, start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    private function buildUnitVoltage(Collection $hours, array $filters, int $perPage): array
    {
        $paginator = $this->paginateCollection($hours, $perPage, $filters);

        return [
            'kpis' => [
                'days' => $hours->pluck('date')->unique()->count(),
                'hour_rows' => $hours->count(),
                'avg_ry' => $this->avg($hours, 'v_ry'),
                'avg_yb' => $this->avg($hours, 'v_yb'),
                'avg_br' => $this->avg($hours, 'v_br'),
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'BS Date'],
                ['key' => 'unit', 'label' => 'Unit'],
                ['key' => 'hour_label', 'label' => 'Time'],
                ['key' => 'v_ry', 'label' => 'V RY'],
                ['key' => 'v_yb', 'label' => 'V YB'],
                ['key' => 'v_br', 'label' => 'V BR'],
            ],
            'rows' => $paginator->getCollection(),
            'paginator' => $paginator,
            'empty' => $hours->isEmpty(),
        ];
    }

    /**
     * @param  Collection<int, LogUnitHour>  $hours
     * @param  array{unit?: mixed, start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    private function buildUnitCurrent(Collection $hours, array $filters, int $perPage): array
    {
        $paginator = $this->paginateCollection($hours, $perPage, $filters);

        return [
            'kpis' => [
                'days' => $hours->pluck('date')->unique()->count(),
                'hour_rows' => $hours->count(),
                'avg_r' => $this->avg($hours, 'i_r'),
                'avg_y' => $this->avg($hours, 'i_y'),
                'avg_b' => $this->avg($hours, 'i_b'),
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'BS Date'],
                ['key' => 'unit', 'label' => 'Unit'],
                ['key' => 'hour_label', 'label' => 'Time'],
                ['key' => 'i_r', 'label' => 'I R'],
                ['key' => 'i_y', 'label' => 'I Y'],
                ['key' => 'i_b', 'label' => 'I B'],
            ],
            'rows' => $paginator->getCollection(),
            'paginator' => $paginator,
            'empty' => $hours->isEmpty(),
        ];
    }

    /**
     * @param  Collection<int, LogLineHour>  $hours
     * @param  array{start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    private function buildLineVoltage(Collection $hours, array $filters, int $perPage): array
    {
        $paginator = $this->paginateCollection($hours, $perPage, $filters);

        return [
            'kpis' => [
                'days' => $hours->pluck('date')->unique()->count(),
                'hour_rows' => $hours->count(),
                'avg_ry' => $this->avg($hours, 'v_ry'),
                'avg_yb' => $this->avg($hours, 'v_yb'),
                'avg_br' => $this->avg($hours, 'v_br'),
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'BS Date'],
                ['key' => 'hour_label', 'label' => 'Time'],
                ['key' => 'v_ry', 'label' => 'Line V RY'],
                ['key' => 'v_yb', 'label' => 'Line V YB'],
                ['key' => 'v_br', 'label' => 'Line V BR'],
            ],
            'rows' => $paginator->getCollection(),
            'paginator' => $paginator,
            'empty' => $hours->isEmpty(),
        ];
    }

    /**
     * @param  Collection<int, LogLineHour>  $hours
     * @param  array{start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    private function buildLineCurrent(Collection $hours, array $filters, int $perPage): array
    {
        $paginator = $this->paginateCollection($hours, $perPage, $filters);

        return [
            'kpis' => [
                'days' => $hours->pluck('date')->unique()->count(),
                'hour_rows' => $hours->count(),
                'avg_r' => $this->avg($hours, 'i_r'),
                'avg_y' => $this->avg($hours, 'i_y'),
                'avg_b' => $this->avg($hours, 'i_b'),
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'BS Date'],
                ['key' => 'hour_label', 'label' => 'Time'],
                ['key' => 'i_r', 'label' => 'Line I R'],
                ['key' => 'i_y', 'label' => 'Line I Y'],
                ['key' => 'i_b', 'label' => 'Line I B'],
            ],
            'rows' => $paginator->getCollection(),
            'paginator' => $paginator,
            'empty' => $hours->isEmpty(),
        ];
    }

    /**
     * @param  Collection<int, LogLineHour>  $hours
     * @param  array{start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    private function buildLinePanel(Collection $hours, array $filters, int $perPage): array
    {
        $paginator = $this->paginateCollection($hours, $perPage, $filters);
        $energy = $hours->groupBy('date')->sum(function (Collection $dayRows) {
            $ordered = $dayRows->sortBy('hour_order')->values();
            $first = $ordered->firstWhere(fn ($r) => $r->kwh !== null);
            $last = $ordered->reverse()->firstWhere(fn ($r) => $r->kwh !== null);
            if ($first === null || $last === null) {
                return 0;
            }

            return (float) $last->kwh - (float) $first->kwh;
        });

        return [
            'kpis' => [
                'days' => $hours->pluck('date')->unique()->count(),
                'hour_rows' => $hours->count(),
                'avg_kw' => $this->avg($hours, 'kw'),
                'energy_kwh' => round((float) $energy, 3),
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'BS Date'],
                ['key' => 'hour_label', 'label' => 'Time'],
                ['key' => 'v_ry', 'label' => 'V RY'],
                ['key' => 'v_yb', 'label' => 'V YB'],
                ['key' => 'v_br', 'label' => 'V BR'],
                ['key' => 'i_r', 'label' => 'I R'],
                ['key' => 'i_y', 'label' => 'I Y'],
                ['key' => 'i_b', 'label' => 'I B'],
                ['key' => 'freq', 'label' => 'Freq'],
                ['key' => 'pf', 'label' => 'PF'],
                ['key' => 'kw', 'label' => 'KW'],
                ['key' => 'kvar', 'label' => 'KVAr'],
                ['key' => 'kwh', 'label' => 'KWH'],
            ],
            'rows' => $paginator->getCollection(),
            'paginator' => $paginator,
            'empty' => $hours->isEmpty(),
        ];
    }

    /**
     * @param  Collection<int, LogMeterHour>  $hours
     * @param  array{start?: mixed, end?: mixed}  $filters
     * @return array<string, mixed>
     */
    private function buildMeter(Collection $hours, array $filters, int $perPage): array
    {
        $paginator = $this->paginateCollection($hours, $perPage, $filters);

        return [
            'kpis' => [
                'days' => $hours->pluck('date')->unique()->count(),
                'hour_rows' => $hours->count(),
                'main_diff' => round((float) $hours->sum(fn ($m) => max(0, (float) ($m->main_diff ?? 0))), 3),
                'check_diff' => round((float) $hours->sum(fn ($m) => max(0, (float) ($m->check_diff ?? 0))), 3),
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'BS Date'],
                ['key' => 'hour_label', 'label' => 'Time'],
                ['key' => 'main_meter', 'label' => 'Main Meter'],
                ['key' => 'check_meter', 'label' => 'Check Meter'],
                ['key' => 'main_diff', 'label' => 'Main Diff'],
                ['key' => 'check_diff', 'label' => 'Check Diff'],
            ],
            'rows' => $paginator->getCollection(),
            'paginator' => $paginator,
            'empty' => $hours->isEmpty(),
        ];
    }

    /**
     * @param  Collection<int, mixed>  $items
     * @param  array<string, mixed>  $filters
     */
    private function paginateCollection(Collection $items, int $perPage, array $filters): LengthAwarePaginator
    {
        $page = max(1, (int) request()->input('page', 1));
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $slice,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => array_filter([
                    'section' => $filters['section'] ?? null,
                    'unit' => $filters['unit'] ?? null,
                    'start' => $filters['start'] ?? null,
                    'end' => $filters['end'] ?? null,
                ], fn ($v) => $v !== null && $v !== ''),
            ]
        );
    }

    /** @param Collection<int, mixed> $rows */
    private function avg(Collection $rows, string $field): float
    {
        $vals = $rows->pluck($field)->filter(fn ($v) => $v !== null);
        if ($vals->isEmpty()) {
            return 0.0;
        }

        return round((float) $vals->avg(), 3);
    }

    /**
     * @param  array{start?: mixed, end?: mixed}  $filters
     * @param  Collection<int, LogUnitDay>  $unitDays
     * @param  Collection<int, LogLineHour>  $lineHours
     * @param  Collection<int, LogMeterHour>  $meters
     * @return array{min: ?string, max: ?string}
     */
    private function dateBounds(array $filters, Collection $unitDays, Collection $lineHours, Collection $meters): array
    {
        $dates = collect()
            ->merge($unitDays->pluck('date'))
            ->merge($lineHours->pluck('date'))
            ->merge($meters->pluck('date'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($dates->isEmpty()) {
            $q = LogDay::query();
            $this->applyDate($q, $filters);

            return ['min' => (clone $q)->min('date'), 'max' => (clone $q)->max('date')];
        }

        return ['min' => $dates->first(), 'max' => $dates->last()];
    }

    /** @param Builder<\Illuminate\Database\Eloquent\Model> $query */
    private function applyDateAndUnit(Builder $query, array $filters): void
    {
        $this->applyDate($query, $filters);
        if (!empty($filters['unit'])) {
            $query->where('unit', (int) $filters['unit']);
        }
    }

    /** @param Builder<\Illuminate\Database\Eloquent\Model> $query */
    private function applyDate(Builder $query, array $filters): void
    {
        if (!empty($filters['start'])) {
            $query->where('date', '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->where('date', '<=', $filters['end']);
        }
    }
}
