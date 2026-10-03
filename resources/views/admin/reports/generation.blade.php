@extends('layouts.app')

@section('title', 'Generation Report')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.css">
<style>
    .fr-page {
        --fr-accent: #0aa1aa;
        --fr-muted: #6b7c82;
        --fr-border: #e6ecee;
        --fr-text: #1f2d32;
        --fr-warn: #c45c26;
        --fr-info: #3b82f6;
        --fr-ok: #1b7a45;
    }
    .fr-page .fr-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-end;
        margin-bottom: 1rem;
    }
    .fr-page .fr-head h4 { margin: 0; font-weight: 600; }
    .fr-page .fr-head p { margin: 4px 0 0; color: var(--fr-muted); font-size: .9rem; }

    .fr-filters {
        background: #fff;
        border: 1px solid var(--fr-border);
        border-radius: 10px;
        padding: .85rem 1rem;
        margin-bottom: 1rem;
    }

    .fr-kpi {
        position: relative;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: .7rem .85rem .75rem;
        height: 100%;
    }
    .fr-kpi.accent { background: #e7f7f8; color: #0a6f76; }
    .fr-kpi.warn { background: #fdf1e9; color: #9a4318; }
    .fr-kpi.info { background: #eaf1ff; color: #1d4ed8; }
    .fr-kpi.ok { background: #e9f7ef; color: #166534; }
    .fr-kpi.slate { background: #eef2f4; color: #455a64; }
    .fr-kpi.peak { background: #fff4e5; color: #b45309; }
    .fr-kpi.cause { background: #fdeceb; color: #b42318; }

    .fr-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        margin-bottom: .15rem;
    }
    .fr-kpi .label {
        color: inherit;
        opacity: .75;
        font-size: .72rem;
        line-height: 1.2;
        margin: 0;
    }
    .fr-info-btn {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1px solid currentColor;
        background: rgba(255,255,255,.55);
        color: inherit;
        opacity: .7;
        font-size: .65rem;
        font-weight: 700;
        line-height: 1;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }
    .fr-info-btn:hover,
    .fr-info-btn:focus {
        opacity: 1;
        outline: none;
        background: #fff;
    }
    .fr-kpi .value {
        font-size: 1.2rem;
        font-weight: 700;
        color: inherit;
        line-height: 1.2;
        word-break: break-word;
    }
    .fr-kpi .value.sm { font-size: 1rem; }
    .fr-kpi .sub {
        color: inherit;
        opacity: .7;
        font-size: .7rem;
        margin-top: .25rem;
        line-height: 1.3;
    }

    .tooltip .tooltip-inner {
        max-width: 240px;
        text-align: left;
        font-size: .75rem;
        padding: .55rem .7rem;
    }

    .fr-card {
        border: 1px solid var(--fr-border);
        border-radius: 10px;
        background: #fff;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .fr-card .fr-card-h {
        padding: .75rem 1rem;
        border-bottom: 1px solid var(--fr-border);
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .fr-card .fr-card-b { padding: 1rem; }
    .fr-badge {
        display: inline-block;
        padding: .15rem .5rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 600;
        background: #eef9fa;
        color: #08848c;
    }
    .fr-badge.nea { background: #fdeceb; color: #b42318; }
    .fr-badge.planned { background: #e7f7ee; color: #1b7a45; }
    .fr-badge.volt { background: #fff4e5; color: #b54708; }
    .fr-table th { white-space: nowrap; font-size: .8rem; background: #f5f8f9; }
    .fr-table td { font-size: .88rem; vertical-align: middle; }
    .fr-empty { text-align: center; color: var(--fr-muted); padding: 2rem 1rem; }
    .btn-fr {
        background: var(--fr-accent);
        border-color: var(--fr-accent);
        color: #fff;
        border-radius: 8px;
    }
    .fr-filters .form-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .fr-th {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .fr-table th .fr-info-btn {
        opacity: .55;
        width: 15px;
        height: 15px;
        font-size: .58rem;
        border-color: #6b7c82;
        color: #6b7c82;
        background: #fff;
    }
    .fr-filters .fr-info-btn {
        opacity: .65;
        width: 16px;
        height: 16px;
        font-size: .6rem;
        border-color: #6b7c82;
        color: #6b7c82;
        background: #fff;
        vertical-align: middle;
    }
    .fr-filters .fr-info-btn:hover,
    .fr-table th .fr-info-btn:hover {
        opacity: 1;
        color: #0aa1aa;
        border-color: #0aa1aa;
    }
    .fr-filters .nepali-datepicker {
        background: #fff;
        cursor: pointer;
    }
    .ndp-container, .ndp-popup, [class*="nepali-date"] {
        z-index: 1080 !important;
    }

    .fr-mobile-cards { display: none; }
    .fr-desktop-only { display: block; }
    .fr-actions { display: flex; gap: 8px; flex-wrap: wrap; }

    @media (max-width: 991.98px) {
        .fr-desktop-only { display: none !important; }
        .fr-mobile-cards { display: block; }
        .fr-page .fr-head .fr-actions { width: 100%; }
        .fr-m-empty { text-align: center; color: var(--fr-muted); padding: 1.25rem; font-size: 12.5px; }
    }

    @media print {
        .left-sidebar, .topbar, .fr-filters, .btn, .sidebar-nav, .scroll-sidebar, .app-bottom-bar { display: none !important; }
        .page-wrapper { margin: 0 !important; }
        .fr-card, .fr-kpi { break-inside: avoid; }
        .fr-mobile-cards { display: none !important; }
        .fr-desktop-only { display: block !important; }
    }
</style>
@endsection

@section('content')
@php
    $k = $report['kpis'];
    $c = $report['charts'];
    $t = $report['tables'];
@endphp
<div class="container-fluid fr-page">
    <div class="fr-head">
        <div>
            <h4>Generation Report</h4>
            <p>
                24-hour main &amp; check meter generation analysis
                @if($report['date_min'])
                    <br class="d-lg-none">{{ $report['date_min'] }} → {{ $report['date_max'] }}
                @endif
            </p>
        </div>
        <div class="fr-actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fa fa-print me-1"></i> Print
            </button>
            <a href="{{ route('admin.reports.generation.export', request()->query()) }}" class="btn btn-fr btn-sm">
                <i class="fa fa-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.generation') }}" class="fr-filters">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">
                    Start date (BS)
                    <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Nepali (Bikram Sambat) start date. Use the calendar to pick YYYY-MM-DD (e.g. 2083-05-01).">i</button>
                </label>
                <input type="text" name="start" id="generationStartBs" class="form-control form-control-sm nepali-datepicker"
                       value="{{ $filters['start'] ?? '' }}" placeholder="{{ $allMin ?: '2083-05-01' }}" autocomplete="off" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">
                    End date (BS)
                    <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Nepali (Bikram Sambat) end date. Use the calendar to pick YYYY-MM-DD (e.g. 2083-05-31).">i</button>
                </label>
                <input type="text" name="end" id="generationEndBs" class="form-control form-control-sm nepali-datepicker"
                       value="{{ $filters['end'] ?? '' }}" placeholder="{{ $allMax ?: '2083-05-31' }}" autocomplete="off" readonly>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-fr btn-sm">Apply Filter</button>
                <a href="{{ route('admin.reports.generation') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    @if($k['total_days'] === 0)
        <div class="fr-card">
            <div class="fr-empty">
                No generation data for this filter.
                <div class="mt-2">
                    <a href="{{ route('admin.import_export', ['tab' => 'generation']) }}">Import 24 HOURS GENERATION Excel</a> first.
                </div>
            </div>
        </div>
    @else
        <div class="row g-2 mb-3">
            <div class="col-6 col-lg-3">
                <div class="fr-kpi accent">
                    <div class="fr-kpi-top">
                        <div class="label">Total Days</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Number of daily 24-hour reading rows in the selected BS date range.">i</button>
                    </div>
                    <div class="value">{{ number_format($k['total_days']) }}</div>
                    <div class="sub">Daily meter readings</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi warn">
                    <div class="fr-kpi-top">
                        <div class="label">Total Main (kWh)</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Sum of Main meter GENERATION (KWH) for all days in the filter.">i</button>
                    </div>
                    <div class="value">{{ number_format($k['total_main_kwh'], 0) }}</div>
                    <div class="sub">Avg {{ number_format($k['avg_main_kwh'], 0) }} / day</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi info">
                    <div class="fr-kpi-top">
                        <div class="label">Total Check (kWh)</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Sum of Check meter GENERATION (KWH) for all days in the filter.">i</button>
                    </div>
                    <div class="value">{{ number_format($k['total_check_kwh'], 0) }}</div>
                    <div class="sub">{{ $k['check_share_pct'] }}% of combined</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi ok">
                    <div class="fr-kpi-top">
                        <div class="label">Combined (kWh)</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Main + Check generation total for the selected period.">i</button>
                    </div>
                    <div class="value">{{ number_format($k['total_combined_kwh'], 0) }}</div>
                    <div class="sub">Main + Check</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi accent">
                    <div class="fr-kpi-top">
                        <div class="label">Main-only Days</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Days where Main meter has generation but Check meter is zero or blank.">i</button>
                    </div>
                    <div class="value">{{ number_format($k['main_only_days']) }}</div>
                    <div class="sub">Typical daily rows</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi info">
                    <div class="fr-kpi-top">
                        <div class="label">Check-only Days</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Days where Check meter has generation but Main meter is zero (e.g. month-end check row).">i</button>
                    </div>
                    <div class="value">{{ number_format($k['check_only_days']) }}</div>
                    <div class="sub">Check meter active</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi peak">
                    <div class="fr-kpi-top">
                        <div class="label">Peak Main Day</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="BS date with the highest Main meter daily generation in this filter.">i</button>
                    </div>
                    <div class="value sm">{{ $k['peak_main_date'] ?: '—' }}</div>
                    <div class="sub">{{ number_format($k['peak_main_kwh'], 0) }} kWh</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi slate">
                    <div class="fr-kpi-top">
                        <div class="label">Peak Check Day</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="BS date with the highest Check meter daily generation in this filter.">i</button>
                    </div>
                    <div class="value sm">{{ $k['peak_check_date'] ?: '—' }}</div>
                    <div class="sub">{{ number_format($k['peak_check_kwh'], 0) }} kWh</div>
                </div>
            </div>
        </div>

        @php
            $dailyMax = max(1, (float) collect($c['daily_main_kwh'] ?? [])->max());
            $m1 = (float) ($c['meter_totals'][0] ?? 0);
            $m2 = (float) ($c['meter_totals'][1] ?? 0);
            $mTotal = max(0.001, $m1 + $m2);
            $circ = 2 * M_PI * 15.9;
            $m1Dash = round(($m1 / $mTotal) * $circ, 1);
            $m1Gap = round($circ - $m1Dash, 1);
            $typeMax = max(1, (int) collect($c['meter_type_days'] ?? [])->max());
            $cumMax = max(1, (float) collect($c['cumulative_main'] ?? [])->max());
        @endphp
        <div class="row">
            <div class="col-lg-8">
                <div class="fr-card">
                    <div class="fr-card-h">Daily Main &amp; Check Generation (kWh)</div>
                    <div class="fr-card-b fr-desktop-only"><canvas id="dailyChart" height="110"></canvas></div>
                    <div class="fr-card-b fr-mobile-cards">
                        <div class="fr-bars">
                            @foreach(($c['daily_labels'] ?? []) as $i => $label)
                                @php
                                    $val = (float) ($c['daily_main_kwh'][$i] ?? 0);
                                    $h = max(2, round(($val / $dailyMax) * 100));
                                    $day = preg_match('/(\d{2})$/', (string) $label, $m) ? $m[1] : \Illuminate\Support\Str::substr($label, -2);
                                @endphp
                                <div class="fr-bar"><i style="height:{{ $h }}%"></i>{{ $day }}</div>
                            @endforeach
                        </div>
                        <div class="fr-lg"><span><s style="background:#0e9fa8"></s>Main kWh per day</span></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="fr-card">
                    <div class="fr-card-h">Meter Share (Total kWh)</div>
                    <div class="fr-card-b fr-desktop-only"><canvas id="meterChart" height="220"></canvas></div>
                    <div class="fr-card-b fr-mobile-cards">
                        <div class="fr-donut-wrap">
                            <svg width="150" height="150" viewBox="0 0 42 42" aria-hidden="true">
                                <circle cx="21" cy="21" r="15.9" fill="none" stroke="#c75b24" stroke-width="8"/>
                                <circle cx="21" cy="21" r="15.9" fill="none" stroke="#0e9fa8" stroke-width="8"
                                    stroke-dasharray="{{ $m1Dash }} {{ $m1Gap }}" transform="rotate(-90 21 21)"/>
                            </svg>
                            <div class="fr-lg">
                                <span><s style="background:#0e9fa8"></s>Main · {{ number_format($m1, 0) }}</span>
                                <span><s style="background:#c75b24"></s>Check · {{ number_format($m2, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">Reading Type by Day</div>
                    <div class="fr-card-b fr-desktop-only"><canvas id="typeChart" height="180"></canvas></div>
                    <div class="fr-card-b fr-mobile-cards">
                        @foreach(($c['meter_type_labels'] ?? []) as $i => $label)
                            @php
                                $days = (int) ($c['meter_type_days'][$i] ?? 0);
                                $w = max(3, round(($days / $typeMax) * 100));
                                $short = \Illuminate\Support\Str::limit($label, 14, '');
                            @endphp
                            <div class="fr-hb">
                                <em title="{{ $label }}">{{ $short }}</em>
                                <div><i style="width:{{ $w }}%"></i></div>
                                <b>{{ $days }}</b>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">Cumulative Generation (kWh)</div>
                    <div class="fr-card-b fr-desktop-only"><canvas id="cumulativeChart" height="180"></canvas></div>
                    <div class="fr-card-b fr-mobile-cards">
                        <div class="fr-bars">
                            @foreach(($c['cumulative_labels'] ?? []) as $i => $label)
                                @php
                                    $val = (float) ($c['cumulative_main'][$i] ?? 0);
                                    $h = max(2, round(($val / $cumMax) * 100));
                                    $day = preg_match('/(\d{2})$/', (string) $label, $m) ? $m[1] : \Illuminate\Support\Str::substr($label, -2);
                                @endphp
                                <div class="fr-bar"><i style="height:{{ $h }}%"></i>{{ $day }}</div>
                            @endforeach
                        </div>
                        <div class="fr-lg"><span><s style="background:#0e9fa8"></s>Cumulative Main kWh</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">
                        <span>Top Main Generation Days</span>
                        <span class="fr-pill">{{ count($t['top_main_days']) }}</span>
                    </div>
                    <div class="fr-card-b p-0 table-responsive fr-desktop-only">
                        <table class="table table-sm fr-table mb-0">
                            <thead>
                                <tr>
                                    <th><span class="fr-th"># <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Rank by Main kWh.">i</button></span></th>
                                    <th><span class="fr-th">BS Date <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Nepali start date of the 24-hour period.">i</button></span></th>
                                    <th><span class="fr-th">Main kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Main meter generation for that day.">i</button></span></th>
                                    <th><span class="fr-th">Check kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Check meter generation for that day (may be 0).">i</button></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t['top_main_days'] as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row['date'] }}</td>
                                        <td><strong>{{ number_format($row['main_kwh'], 0) }}</strong></td>
                                        <td>{{ number_format($row['check_kwh'], 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="fr-mobile-cards">
                        @foreach($t['top_main_days'] as $row)
                            <div class="fr-li">
                                <span class="n">{{ $loop->iteration }}</span>
                                <p>{{ $row['date'] }}</p>
                                <div class="m"><b>{{ number_format($row['main_kwh'], 0) }} kWh</b>Check {{ number_format($row['check_kwh'], 0) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">Meter Type Breakdown</div>
                    <div class="fr-card-b p-0 table-responsive fr-desktop-only">
                        <table class="table table-sm fr-table mb-0">
                            <thead>
                                <tr>
                                    <th><span class="fr-th">Type <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Whether the day used Main only, Check only, or both meters.">i</button></span></th>
                                    <th><span class="fr-th">Days <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Number of daily rows in this category.">i</button></span></th>
                                    <th><span class="fr-th">Main kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Total Main generation in this category.">i</button></span></th>
                                    <th><span class="fr-th">Check kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Total Check generation in this category.">i</button></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t['meter_types'] as $row)
                                    <tr>
                                        <td><span class="fr-badge">{{ $row['type'] }}</span></td>
                                        <td>{{ $row['days'] }}</td>
                                        <td>{{ number_format($row['main_kwh'], 0) }}</td>
                                        <td>{{ number_format($row['check_kwh'], 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="fr-mobile-cards">
                        @php $typeColors = ['#0e9fa8','#c75b24','#5b6cf0','#12a150']; @endphp
                        @foreach($t['meter_types'] as $row)
                            <div class="fr-li" style="align-items:center">
                                <span class="dot" style="background:{{ $typeColors[$loop->index % count($typeColors)] }}"></span>
                                <p>{{ $row['type'] }}</p>
                                <div class="m"><b>{{ $row['days'] }} days</b>{{ number_format($row['main_kwh'], 0) }} / {{ number_format($row['check_kwh'], 0) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">Largest Main − Check Variance</div>
                    <div class="fr-card-b p-0 table-responsive fr-desktop-only">
                        <table class="table table-sm fr-table mb-0">
                            <thead>
                                <tr>
                                    <th><span class="fr-th">BS Date <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Days where Check meter had data — useful for reconciliation.">i</button></span></th>
                                    <th><span class="fr-th">Main kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Main meter generation.">i</button></span></th>
                                    <th><span class="fr-th">Check kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Check meter generation.">i</button></span></th>
                                    <th><span class="fr-th">Variance <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Main − Check (kWh).">i</button></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($t['variance_days'] as $row)
                                    <tr>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ number_format($row['main_kwh'], 0) }}</td>
                                        <td>{{ number_format($row['check_kwh'], 0) }}</td>
                                        <td><strong>{{ number_format($row['variance_kwh'], 0) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="fr-empty">No check-meter days in range</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="fr-mobile-cards">
                        @forelse($t['variance_days'] as $row)
                            <div class="fr-ev">
                                <div class="t">
                                    <span class="fr-u u1">Main</span>
                                    <span class="x">{{ $row['date'] }}</span>
                                    <span class="d">{{ number_format($row['variance_kwh'], 0) }}</span>
                                </div>
                                <div class="x">Main {{ number_format($row['main_kwh'], 0) }} · Check {{ number_format($row['check_kwh'], 0) }}</div>
                            </div>
                        @empty
                            <div class="fr-m-empty">No check-meter days in range</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">Daily Summary</div>
                    <div class="fr-card-b p-0 table-responsive fr-desktop-only" style="max-height:420px; overflow:auto;">
                        <table class="table table-sm fr-table mb-0">
                            <thead>
                                <tr>
                                    <th><span class="fr-th">BS Date <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Start date of each 24-hour reading.">i</button></span></th>
                                    <th><span class="fr-th">Main kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Daily Main generation.">i</button></span></th>
                                    <th><span class="fr-th">Check kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Daily Check generation.">i</button></span></th>
                                    <th><span class="fr-th">Combined <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Main + Check for the day.">i</button></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t['daily'] as $row)
                                    <tr>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ number_format($row['main_kwh'], 0) }}</td>
                                        <td>{{ number_format($row['check_kwh'], 0) }}</td>
                                        <td>{{ number_format($row['combined_kwh'], 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="fr-mobile-cards fr-m-table-wrap">
                        <table class="fr-m-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Main</th>
                                    <th>Check</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($t['daily'] as $row)
                                    <tr>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ number_format($row['main_kwh'], 0) }}</td>
                                        <td>{{ number_format($row['check_kwh'], 0) }}</td>
                                        <td><b>{{ number_format($row['combined_kwh'], 0) }}</b></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="fr-m-empty">No daily summary</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="fr-card">
            <div class="fr-card-h">
                <span>Recent Readings</span>
                <a href="{{ route('admin.import_export', ['tab' => 'generation']) }}" class="btn btn-sm btn-outline-secondary">Manage Imports</a>
            </div>
            <div class="fr-card-b p-0 table-responsive fr-desktop-only">
                <table class="table table-sm fr-table mb-0">
                    <thead>
                        <tr>
                            <th><span class="fr-th">BS Initial <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Nepali start date (12AM).">i</button></span></th>
                            <th><span class="fr-th">BS Final <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Nepali end date (next 12AM).">i</button></span></th>
                            <th><span class="fr-th">AD Initial <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Gregorian start date from the Excel sheet.">i</button></span></th>
                            <th><span class="fr-th">Main kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Main meter daily generation.">i</button></span></th>
                            <th><span class="fr-th">Check kWh <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Check meter daily generation.">i</button></span></th>
                            <th><span class="fr-th">Type <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Which meters recorded generation on this day.">i</button></span></th>
                            <th><span class="fr-th">Month <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Month label from the imported Excel title row.">i</button></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($t['recent'] as $row)
                            <tr>
                                <td>{{ $row['bs_initial_date'] }}</td>
                                <td>{{ $row['bs_final_date'] }}</td>
                                <td>{{ $row['ad_initial_date'] }}</td>
                                <td>{{ $row['main_kwh'] }}</td>
                                <td>{{ $row['check_kwh'] }}</td>
                                <td><span class="fr-badge">{{ $row['meter_type'] }}</span></td>
                                <td>{{ \Illuminate\Support\Str::limit($row['month_label'], 40) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="fr-mobile-cards">
                @forelse($t['recent'] as $row)
                    <div class="fr-ev">
                        <div class="t">
                            <span class="fr-u u1">{{ $row['meter_type'] }}</span>
                            <span class="x">{{ $row['bs_initial_date'] }} → {{ $row['bs_final_date'] }}</span>
                            <span class="d">{{ $row['main_kwh'] }}</span>
                        </div>
                        <div class="x">AD {{ $row['ad_initial_date'] }} · Check {{ $row['check_kwh'] }} kWh</div>
                        <div class="y">{{ $row['month_label'] }}</div>
                    </div>
                @empty
                    <div class="fr-m-empty">No recent readings</div>
                @endforelse
            </div>
        </div>
        <a href="{{ route('admin.import_export', ['tab' => 'generation']) }}" class="fr-more-btn d-lg-none">Manage imports</a>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (window.NepaliDatePicker && typeof NepaliDatePicker.attach === 'function') {
        ['#generationStartBs', '#generationEndBs'].forEach(function (selector) {
            var el = document.querySelector(selector);
            if (!el) return;
            NepaliDatePicker.attach(selector, {
                language: 'en',
                onChange: function (date) {
                    if (date && typeof date.format === 'function') {
                        el.value = date.format('YYYY-MM-DD');
                    }
                },
            });
        });
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('.fr-info-btn'));
    tooltipTriggerList.forEach(function (el) {
        if (window.bootstrap && bootstrap.Tooltip) {
            new bootstrap.Tooltip(el);
        } else if (window.jQuery && jQuery.fn.tooltip) {
            jQuery(el).tooltip();
        }
    });

    const charts = @json($report['charts'] ?? []);
    if (!charts.daily_labels || !charts.daily_labels.length) {
        return;
    }

    if (window.matchMedia && window.matchMedia('(max-width: 991.98px)').matches) {
        return;
    }

    const accent = '#0aa1aa';
    const warn = '#c45c26';
    const blue = '#3b82f6';
    const green = '#1b7a45';

    new Chart(document.getElementById('dailyChart'), {
        data: {
            labels: charts.daily_labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Main kWh',
                    data: charts.daily_main_kwh,
                    backgroundColor: 'rgba(10,161,170,0.35)',
                    borderColor: accent,
                    borderWidth: 1,
                    yAxisID: 'y',
                },
                {
                    type: 'line',
                    label: 'Check kWh',
                    data: charts.daily_check_kwh,
                    borderColor: warn,
                    backgroundColor: 'rgba(196,92,38,0.15)',
                    tension: 0.3,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Main kWh' } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Check kWh' } }
            }
        }
    });

    new Chart(document.getElementById('meterChart'), {
        type: 'doughnut',
        data: {
            labels: charts.meter_labels,
            datasets: [{
                data: charts.meter_totals,
                backgroundColor: [accent, warn],
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('typeChart'), {
        type: 'bar',
        data: {
            labels: charts.meter_type_labels,
            datasets: [{
                label: 'Days',
                data: charts.meter_type_days,
                backgroundColor: [accent, warn, blue, green, '#64748b'],
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    new Chart(document.getElementById('cumulativeChart'), {
        type: 'line',
        data: {
            labels: charts.cumulative_labels,
            datasets: [
                {
                    label: 'Main cumulative',
                    data: charts.cumulative_main,
                    borderColor: accent,
                    backgroundColor: 'rgba(10,161,170,0.12)',
                    tension: 0.25,
                    fill: true,
                },
                {
                    label: 'Check cumulative',
                    data: charts.cumulative_check,
                    borderColor: warn,
                    backgroundColor: 'rgba(196,92,38,0.08)',
                    tension: 0.25,
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'kWh' } } }
        }
    });
})();
</script>
@endsection
