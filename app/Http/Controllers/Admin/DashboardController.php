<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fault;
use App\Models\GenerationReading;
use App\Models\GeneratorDailyLog;
use App\Models\GeneratorOutage;
use App\Models\ImportBatch;
use App\Models\LogDay;
use App\Models\LogUnitDay;
use App\Models\LogUnitHour;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        [$bsStart, $bsEnd] = $this->resolveBsRange(
            $request->input('start'),
            $request->input('end')
        );
        [$prevStart, $prevEnd] = $this->previousBsRange($bsStart, $bsEnd);

        $mainNow = $this->mainGenerationKwh($bsStart, $bsEnd);
        $mainPrev = $this->mainGenerationKwh($prevStart, $prevEnd);
        $checkNow = $this->checkGenerationKwh($bsStart, $bsEnd);
        $checkPrev = $this->checkGenerationKwh($prevStart, $prevEnd);

        $unit1Now = $this->unitEnergy($bsStart, $bsEnd, 1);
        $unit1Prev = $this->unitEnergy($prevStart, $prevEnd, 1);
        $unit2Now = $this->unitEnergy($bsStart, $bsEnd, 2);
        $unit2Prev = $this->unitEnergy($prevStart, $prevEnd, 2);

        $logDays = LogDay::whereBetween('date', [$bsStart, $bsEnd])->count();
        $outagesNow = GeneratorOutage::whereBetween('date', [$bsStart, $bsEnd])->count();
        $outagesPrev = GeneratorOutage::whereBetween('date', [$prevStart, $prevEnd])->count();
        $g1Gen = (float) GeneratorDailyLog::where('generator', 1)->whereBetween('date', [$bsStart, $bsEnd])->sum('total_generation_kwh');
        $g2Gen = (float) GeneratorDailyLog::where('generator', 2)->whereBetween('date', [$bsStart, $bsEnd])->sum('total_generation_kwh');

        $openFaults = Fault::where('status', 'Unsolved')->count();
        $resolvedFaults = Fault::where('status', 'Solved')->count();
        $faultTotal = Fault::count();

        $batches = ImportBatch::query()->whereNull('undone_at')->count();
        $batchesByType = ImportBatch::query()
            ->whereNull('undone_at')
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $generationSeries = $this->generationSeries($bsStart, $bsEnd);
        $unitSeries = $this->unitSeries($bsStart, $bsEnd);

        $dashboard = [
            'range' => [
                'start' => $bsStart,
                'end' => $bsEnd,
                'label' => $bsStart . ' - ' . $bsEnd,
                'days' => $this->bsDaySpan($bsStart, $bsEnd),
            ],
            'kpis' => [
                'users' => [
                    'value' => User::count(),
                    'sub' => 'Active system accounts',
                    'detail' => 'Admins & operators',
                    'trend' => 0.0,
                    'spark' => $this->adSparkline(User::query()->orderBy('created_at')->get(), 'created_at'),
                ],
                'main_mwh' => [
                    'value' => round($mainNow / 1000, 2),
                    'sub' => number_format($mainNow, 0) . ' kWh main meter',
                    'detail' => 'Avg/day ' . ($logDays ? number_format(($mainNow / 1000) / max(1, $logDays), 2) : '0') . ' MWh',
                    'trend' => $this->trendPct($mainNow - $mainPrev, max(1.0, $mainPrev)),
                    'spark' => array_values(array_slice($generationSeries['values'], -8)),
                ],
                'check_mwh' => [
                    'value' => round($checkNow / 1000, 2),
                    'sub' => number_format($checkNow, 0) . ' kWh check meter',
                    'detail' => 'Diff vs main ' . number_format(abs($mainNow - $checkNow), 0) . ' kWh',
                    'trend' => $this->trendPct($checkNow - $checkPrev, max(1.0, $checkPrev)),
                    'spark' => $this->numericSpark([$checkPrev / 1000, $checkNow / 1000]),
                ],
                'unit1_mwh' => [
                    'value' => round($unit1Now / 1000, 2),
                    'sub' => number_format($unit1Now, 0) . ' kWh from log',
                    'detail' => 'Avg KW ' . number_format((float) LogUnitDay::where('unit', 1)->whereBetween('date', [$bsStart, $bsEnd])->avg('avg_kw'), 0),
                    'trend' => $this->trendPct($unit1Now - $unit1Prev, max(1.0, $unit1Prev)),
                    'spark' => array_values(array_slice($unitSeries['unit1'], -8)),
                ],
                'unit2_mwh' => [
                    'value' => round($unit2Now / 1000, 2),
                    'sub' => number_format($unit2Now, 0) . ' kWh from log',
                    'detail' => 'Avg KW ' . number_format((float) LogUnitDay::where('unit', 2)->whereBetween('date', [$bsStart, $bsEnd])->avg('avg_kw'), 0),
                    'trend' => $this->trendPct($unit2Now - $unit2Prev, max(1.0, $unit2Prev)),
                    'spark' => array_values(array_slice($unitSeries['unit2'], -8)),
                ],
                'faults' => [
                    'value' => $faultTotal,
                    'sub' => $openFaults . ' open · ' . $resolvedFaults . ' resolved',
                    'detail' => $openFaults > 0 ? 'Needs attention' : 'All clear',
                    'trend' => 0.0,
                    'spark' => $this->adSparkline(Fault::query()->orderBy('created_at')->get(), 'created_at'),
                ],
                'outages' => [
                    'value' => $outagesNow,
                    'sub' => 'Generator trip / outage rows',
                    'detail' => 'G1 ' . number_format($g1Gen, 0) . ' · G2 ' . number_format($g2Gen, 0) . ' kWh',
                    'trend' => $this->trendPct($outagesNow - $outagesPrev, max(1, $outagesPrev)),
                    'spark' => $this->bsCountSpark(GeneratorOutage::query(), $bsStart, $bsEnd),
                ],
                'imports' => [
                    'value' => $batches,
                    'sub' => 'Import batches on file',
                    'detail' => sprintf(
                        'Fail %d · Gen %d · Meter %d · Log %d',
                        (int) ($batchesByType[ImportBatch::TYPE_FAILURE] ?? 0),
                        (int) ($batchesByType[ImportBatch::TYPE_GENERATION] ?? 0),
                        (int) ($batchesByType[ImportBatch::TYPE_GENERATOR] ?? 0),
                        (int) ($batchesByType[ImportBatch::TYPE_LOG] ?? 0)
                    ),
                    'trend' => 0.0,
                    'spark' => $this->adSparkline(ImportBatch::query()->orderBy('imported_at')->get(), 'imported_at'),
                ],
            ],
            'charts' => [
                'generation' => $generationSeries,
                'units' => $unitSeries,
                'faults' => [
                    'critical' => 0,
                    'major' => $openFaults,
                    'minor' => 0,
                    'resolved' => $resolvedFaults,
                    'total' => $faultTotal,
                ],
            ],
            'recent_generation' => $this->recentGeneration(),
            'recent_faults' => Fault::query()->latest('fault_time')->limit(8)->get(),
            'recent_imports' => ImportBatch::query()
                ->with('importer')
                ->whereNull('undone_at')
                ->latest('imported_at')
                ->limit(6)
                ->get(),
            'unit_daily' => LogUnitDay::query()
                ->whereBetween('date', [$bsStart, $bsEnd])
                ->orderByDesc('date')
                ->orderBy('unit')
                ->limit(12)
                ->get(),
        ];

        return view('admin.dashboard', compact('dashboard'));
    }

    /** @return array{0: string, 1: string} */
    private function resolveBsRange(?string $start, ?string $end): array
    {
        $fallbackEnd = LogUnitDay::max('date')
            ?: GenerationReading::max('bs_final_date')
            ?: GenerationReading::max('bs_initial_date')
            ?: '2083-01-01';

        $bsEnd = $this->isBsDate($end) ? $end : $fallbackEnd;
        $bsStart = $this->isBsDate($start) ? $start : (substr($bsEnd, 0, 8) . '01');

        if ($bsStart > $bsEnd) {
            [$bsStart, $bsEnd] = [$bsEnd, $bsStart];
        }

        return [$bsStart, $bsEnd];
    }

    private function isBsDate(?string $value): bool
    {
        if ($value === null || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return false;
        }
        $y = (int) $m[1];
        $mo = (int) $m[2];
        $d = (int) $m[3];

        return $y >= 2000 && $y <= 2200 && $mo >= 1 && $mo <= 12 && $d >= 1 && $d <= 32;
    }

    /** @return array{0: string, 1: string} */
    private function previousBsRange(string $start, string $end): array
    {
        $startTs = strtotime($start);
        $endTs = strtotime($end);
        if ($startTs === false || $endTs === false) {
            return [$start, $end];
        }
        $days = max(0, (int) floor(($endTs - $startTs) / 86400));
        $prevEnd = date('Y-m-d', $startTs - 86400);
        $prevStart = date('Y-m-d', strtotime($prevEnd) - ($days * 86400));

        return [$prevStart, $prevEnd];
    }

    private function bsDaySpan(string $start, string $end): int
    {
        $a = strtotime($start);
        $b = strtotime($end);
        if ($a === false || $b === false) {
            return 0;
        }

        return max(1, (int) floor(($b - $a) / 86400) + 1);
    }

    private function mainGenerationKwh(string $bsStart, string $bsEnd): float
    {
        $fromGen = (float) GenerationReading::query()
            ->where(function ($q) use ($bsStart, $bsEnd) {
                $q->whereBetween('bs_initial_date', [$bsStart, $bsEnd])
                    ->orWhereBetween('bs_final_date', [$bsStart, $bsEnd]);
            })
            ->sum('main_generation_kwh');

        if ($fromGen > 0) {
            return $fromGen;
        }

        return (float) LogUnitDay::query()
            ->whereBetween('date', [$bsStart, $bsEnd])
            ->sum('energy_kwh');
    }

    private function checkGenerationKwh(string $bsStart, string $bsEnd): float
    {
        return (float) GenerationReading::query()
            ->where(function ($q) use ($bsStart, $bsEnd) {
                $q->whereBetween('bs_initial_date', [$bsStart, $bsEnd])
                    ->orWhereBetween('bs_final_date', [$bsStart, $bsEnd]);
            })
            ->sum('check_generation_kwh');
    }

    private function unitEnergy(string $bsStart, string $bsEnd, int $unit): float
    {
        return (float) LogUnitDay::query()
            ->where('unit', $unit)
            ->whereBetween('date', [$bsStart, $bsEnd])
            ->sum('energy_kwh');
    }

    /** @return array{labels: array<int, string>, values: array<int, float>} */
    private function generationSeries(string $bsStart, string $bsEnd): array
    {
        $days = LogUnitDay::query()
            ->selectRaw('date, SUM(energy_kwh) as energy')
            ->whereBetween('date', [$bsStart, $bsEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($days->isNotEmpty()) {
            $slice = $days->take(-14);

            return [
                'labels' => $slice->map(fn ($d) => $d->date)->values()->all(),
                'values' => $slice->map(fn ($d) => round(((float) $d->energy) / 1000, 2))->values()->all(),
            ];
        }

        $readings = GenerationReading::query()
            ->where(function ($q) use ($bsStart, $bsEnd) {
                $q->whereBetween('bs_initial_date', [$bsStart, $bsEnd])
                    ->orWhereBetween('bs_final_date', [$bsStart, $bsEnd]);
            })
            ->orderBy('bs_initial_date')
            ->get();

        if ($readings->isNotEmpty()) {
            $slice = $readings->take(-14);

            return [
                'labels' => $slice->map(fn ($r) => $r->bs_initial_date ?: '—')->values()->all(),
                'values' => $slice->map(fn ($r) => round(((float) $r->main_generation_kwh) / 1000, 2))->values()->all(),
            ];
        }

        return ['labels' => [$bsStart, $bsEnd], 'values' => [0.0, 0.0]];
    }

    /** @return array{labels: array<int, string>, unit1: array<int, float>, unit2: array<int, float>} */
    private function unitSeries(string $bsStart, string $bsEnd): array
    {
        $rows = LogUnitDay::query()
            ->whereBetween('date', [$bsStart, $bsEnd])
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $labels = [];
        $u1 = [];
        $u2 = [];
        foreach ($rows->take(-14) as $date => $dayRows) {
            $labels[] = $date;
            $u1[] = round(((float) optional($dayRows->firstWhere('unit', 1))->energy_kwh) / 1000, 2);
            $u2[] = round(((float) optional($dayRows->firstWhere('unit', 2))->energy_kwh) / 1000, 2);
        }

        if ($labels === []) {
            return ['labels' => [$bsStart], 'unit1' => [0.0], 'unit2' => [0.0]];
        }

        return ['labels' => $labels, 'unit1' => $u1, 'unit2' => $u2];
    }

    /** @return Collection<int, object> */
    private function recentGeneration(): Collection
    {
        $days = LogUnitDay::query()
            ->orderByDesc('date')
            ->orderBy('unit')
            ->limit(10)
            ->get();

        if ($days->isNotEmpty()) {
            return $days->map(function (LogUnitDay $row) {
                return (object) [
                    'date' => $row->date,
                    'time' => $row->hour_count . ' hrs',
                    'generation_mwh' => round(((float) $row->energy_kwh) / 1000, 3),
                    'unit' => 'Unit ' . $row->unit,
                    'avg_kw' => $row->avg_kw,
                    'status' => ((float) $row->energy_kwh) > 0 ? 'Normal' : 'Idle',
                ];
            });
        }

        $hours = LogUnitHour::query()
            ->whereNotNull('kw')
            ->orderByDesc('date')
            ->orderByDesc('hour_order')
            ->limit(8)
            ->get();

        if ($hours->isNotEmpty()) {
            return $hours->map(function (LogUnitHour $row) {
                return (object) [
                    'date' => $row->date,
                    'time' => $row->hour_label,
                    'generation_mwh' => round(((float) $row->kw) / 1000, 3),
                    'unit' => 'Unit ' . $row->unit,
                    'avg_kw' => $row->kw,
                    'status' => ((float) $row->kw) > 0 ? 'Normal' : 'Idle',
                ];
            });
        }

        return GenerationReading::query()->latest('id')->limit(8)->get()->map(function (GenerationReading $row) {
            return (object) [
                'date' => $row->bs_initial_date ?: $row->ad_initial_date,
                'time' => '—',
                'generation_mwh' => round(((float) $row->main_generation_kwh) / 1000, 2),
                'unit' => $row->company ?: 'Plant',
                'avg_kw' => null,
                'status' => 'Normal',
            ];
        });
    }

    private function trendPct(float $delta, float $base): float
    {
        return round(($delta / max(1.0, abs($base))) * 100, 0);
    }

    /** @param array<int, float> $values */
    private function numericSpark(array $values): array
    {
        $out = array_map(fn ($v) => round((float) $v, 2), $values);
        while (count($out) < 7) {
            array_unshift($out, 0.0);
        }

        return array_slice($out, -7);
    }

    /** @return array<int, float> */
    private function bsCountSpark($query, string $bsStart, string $bsEnd): array
    {
        $rows = (clone $query)
            ->whereBetween('date', [$bsStart, $bsEnd])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        if ($rows->isEmpty()) {
            return [0, 0, 0, 0, 0, 0, 0];
        }

        return array_values(array_slice($rows->map(fn ($v) => (float) $v)->all(), -7));
    }

    /**
     * @param  Collection<int, mixed>  $rows
     * @return array<int, float>
     */
    private function adSparkline(Collection $rows, string $dateField): array
    {
        if ($rows->isEmpty()) {
            return [0, 0, 0, 0, 0, 0, 0];
        }

        $end = now()->endOfDay();
        $buckets = [];
        for ($i = 6; $i >= 0; $i--) {
            $buckets[$end->copy()->subDays($i)->toDateString()] = 0;
        }

        foreach ($rows as $row) {
            $raw = data_get($row, $dateField);
            if (!$raw) {
                continue;
            }
            $key = Carbon::parse($raw)->toDateString();
            if (array_key_exists($key, $buckets)) {
                $buckets[$key]++;
            }
        }

        return array_values($buckets);
    }
}
