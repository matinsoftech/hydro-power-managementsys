@extends('layouts.app')

@section('title', 'Fault Report')

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
            <h4>Fault Report</h4>
            <p>
                Grid failure &amp; force outage analysis
                @if($report['date_min'])
                    <br class="d-lg-none">{{ $report['date_min'] }} → {{ $report['date_max'] }}
                @endif
            </p>
        </div>
        <div class="fr-actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fa fa-print me-1"></i> Print
            </button>
            <a href="{{ route('admin.reports.fault.export', request()->query()) }}" class="btn btn-fr btn-sm">
                <i class="fa fa-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.fault') }}" class="fr-filters">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">
                    Unit
                    <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Filter events by generation unit. All units = Unit 1 + Unit 2 combined.">i</button>
                </label>
                <select name="unit" class="form-select form-select-sm">
                    <option value="">All units</option>
                    <option value="1" @selected(($filters['unit'] ?? '') == '1')>Unit 1</option>
                    <option value="2" @selected(($filters['unit'] ?? '') == '2')>Unit 2</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">
                    Start date (BS)
                    <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Nepali (Bikram Sambat) start date. Use the calendar to pick YYYY-MM-DD (e.g. 2083-05-02).">i</button>
                </label>
                <input type="text" name="start" id="failureStartBs" class="form-control form-control-sm nepali-datepicker"
                       value="{{ $filters['start'] ?? '' }}" placeholder="{{ $allMin ?: '2083-05-01' }}" autocomplete="off" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">
                    End date (BS)
                    <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Nepali (Bikram Sambat) end date. Use the calendar to pick YYYY-MM-DD (e.g. 2083-05-31).">i</button>
                </label>
                <input type="text" name="end" id="failureEndBs" class="form-control form-control-sm nepali-datepicker"
                       value="{{ $filters['end'] ?? '' }}" placeholder="{{ $allMax ?: '2083-05-31' }}" autocomplete="off" readonly>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-fr btn-sm">Apply Filter</button>
                <a href="{{ route('admin.reports.fault') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    @if($k['total_events'] === 0)
        <div class="fr-card">
            <div class="fr-empty">
                No fault data for this filter.
                <div class="mt-2">
                    <a href="{{ route('admin.import_export', ['tab' => 'failure']) }}">Import GRID FAIL Excel</a> first.
                </div>
            </div>
        </div>
    @else
        {{-- KPI row --}}
        @php
            $peak = collect($t['daily'])->sortByDesc('events')->first();
            $top = collect($t['categories'])->first();
        @endphp
        <div class="row g-2 mb-3">
            <div class="col-6 col-lg-3">
                <div class="fr-kpi accent">
                    <div class="fr-kpi-top">
                        <div class="label">Total Events</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Total number of grid failure / force outage records in the selected date range (both units).">i</button>
                    </div>
                    <div class="value">{{ number_format($k['total_events']) }}</div>
                    <div class="sub">{{ $k['days_affected'] }} day(s) affected</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi warn">
                    <div class="fr-kpi-top">
                        <div class="label">Total Downtime</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Sum of all event durations (From → Synch). Average is total downtime divided by event count.">i</button>
                    </div>
                    <div class="value">{{ $k['total_downtime'] }}</div>
                    <div class="sub">Avg {{ $k['avg_downtime'] }} / event</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi info">
                    <div class="fr-kpi-top">
                        <div class="label">NEA / Grid Trips</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Events caused by NEA grid failure, system trip, or grid gone — the most common external outage type.">i</button>
                    </div>
                    <div class="value">{{ number_format($k['nea_trips']) }}</div>
                    <div class="sub">Downtime {{ $k['nea_downtime'] }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi ok">
                    <div class="fr-kpi-top">
                        <div class="label">Forced vs Planned</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Forced = unexpected trips/outages. Planned = normal stop, shutdown, intake cleaning, or intentional unit stop.">i</button>
                    </div>
                    <div class="value">{{ $k['forced_outages'] }} / {{ $k['planned_stops'] }}</div>
                    <div class="sub">Forced · Planned</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi accent">
                    <div class="fr-kpi-top">
                        <div class="label">Unit 1 Events</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Number of failure / outage events recorded for Unit 1, with total Unit 1 downtime.">i</button>
                    </div>
                    <div class="value">{{ $k['unit1_events'] }}</div>
                    <div class="sub">Downtime {{ $k['unit1_downtime'] }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi info">
                    <div class="fr-kpi-top">
                        <div class="label">Unit 2 Events</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Number of failure / outage events recorded for Unit 2, with total Unit 2 downtime.">i</button>
                    </div>
                    <div class="value">{{ $k['unit2_events'] }}</div>
                    <div class="sub">Downtime {{ $k['unit2_downtime'] }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi peak">
                    <div class="fr-kpi-top">
                        <div class="label">Peak Day</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="The date with the highest number of fault events in the selected range, plus that day's downtime.">i</button>
                    </div>
                    <div class="value sm">{{ $peak['date'] ?? '—' }}</div>
                    <div class="sub">{{ $peak['events'] ?? 0 }} events · {{ $peak['downtime'] ?? '0:00:00' }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fr-kpi cause">
                    <div class="fr-kpi-top">
                        <div class="label">Top Cause</div>
                        <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Most frequent cause category after grouping similar remarks (e.g. NEA / Grid Trip, Voltage Fluctuations).">i</button>
                    </div>
                    <div class="value sm">{{ $top['category'] ?? '—' }}</div>
                    <div class="sub">{{ $top['events'] ?? 0 }} events · {{ $top['downtime'] ?? '0:00:00' }}</div>
                </div>
            </div>
        </div>

        {{-- Charts: Chart.js on desktop, CSS/SVG figures on mobile (320px design) --}}
        @php
            $dailyMax = max(1, (int) collect($c['daily_events'] ?? [])->max());
            $hourMax = max(1, (int) collect($c['hour_events'] ?? [])->max());
            $u1 = (int) ($c['unit_events'][0] ?? 0);
            $u2 = (int) ($c['unit_events'][1] ?? 0);
            $uTotal = max(1, $u1 + $u2);
            $u1Pct = round(($u1 / $uTotal) * 100, 1);
            $circ = 2 * M_PI * 15.9;
            $u1Dash = round(($u1 / $uTotal) * $circ, 1);
            $u1Gap = round($circ - $u1Dash, 1);
            $catMax = max(1, (int) collect($c['category_events'] ?? [])->max());
        @endphp
        <div class="row">
            <div class="col-lg-8">
                <div class="fr-card">
                    <div class="fr-card-h">Daily events &amp; downtime</div>
                    <div class="fr-card-b fr-desktop-only"><canvas id="dailyChart" height="110"></canvas></div>
                    <div class="fr-card-b fr-mobile-cards">
                        <div class="fr-bars" id="frDailyBars">
                            @foreach(($c['daily_labels'] ?? []) as $i => $label)
                                @php
                                    $ev = (int) ($c['daily_events'][$i] ?? 0);
                                    $h = max(2, round(($ev / $dailyMax) * 100));
                                    $day = preg_match('/(\d{2})$/', (string) $label, $m) ? $m[1] : \Illuminate\Support\Str::substr($label, -2);
                                @endphp
                                <div class="fr-bar"><i style="height:{{ $h }}%"></i>{{ $day }}</div>
                            @endforeach
                        </div>
                        <div class="fr-lg"><span><s style="background:#0e9fa8"></s>Events per day</span></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="fr-card">
                    <div class="fr-card-h">Unit comparison</div>
                    <div class="fr-card-b fr-desktop-only"><canvas id="unitChart" height="220"></canvas></div>
                    <div class="fr-card-b fr-mobile-cards">
                        <div class="fr-donut-wrap">
                            <svg width="150" height="150" viewBox="0 0 42 42" aria-hidden="true">
                                <circle cx="21" cy="21" r="15.9" fill="none" stroke="#c75b24" stroke-width="8"/>
                                <circle cx="21" cy="21" r="15.9" fill="none" stroke="#0e9fa8" stroke-width="8"
                                    stroke-dasharray="{{ $u1Dash }} {{ $u1Gap }}" transform="rotate(-90 21 21)"/>
                            </svg>
                            <div class="fr-lg">
                                <span><s style="background:#0e9fa8"></s>Unit 1 · {{ $u1 }}</span>
                                <span><s style="background:#c75b24"></s>Unit 2 · {{ $u2 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">Cause categories</div>
                    <div class="fr-card-b fr-desktop-only"><canvas id="categoryChart" height="180"></canvas></div>
                    <div class="fr-card-b fr-mobile-cards">
                        @foreach(($c['category_labels'] ?? []) as $i => $label)
                            @php
                                $ev = (int) ($c['category_events'][$i] ?? 0);
                                $w = max(3, round(($ev / $catMax) * 100));
                                $short = \Illuminate\Support\Str::limit($label, 14, '');
                            @endphp
                            <div class="fr-hb">
                                <em title="{{ $label }}">{{ $short }}</em>
                                <div><i style="width:{{ $w }}%"></i></div>
                                <b>{{ $ev }}</b>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">Events by hour of day</div>
                    <div class="fr-card-b fr-desktop-only"><canvas id="hourChart" height="180"></canvas></div>
                    <div class="fr-card-b fr-mobile-cards">
                        <div class="fr-bars fr-bars-hours">
                            @foreach(($c['hour_events'] ?? []) as $ev)
                                @php $h = max(2, round(((int)$ev / $hourMax) * 100)); @endphp
                                <div class="fr-bar"><i style="height:{{ $h }}%"></i></div>
                            @endforeach
                        </div>
                        <div class="fr-lg" style="justify-content:space-between">
                            <span>00:00</span><span>06:00</span><span>12:00</span><span>18:00</span><span>23:00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tables --}}
        <div class="row">
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">
                        <span>Top Reasons</span>
                        <span class="fr-pill">{{ count($t['top_reasons']) }}</span>
                    </div>
                    <div class="fr-card-b p-0 table-responsive fr-desktop-only">
                        <table class="table table-sm fr-table mb-0">
                            <thead>
                                <tr>
                                    <th><span class="fr-th"># <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Row serial number.">i</button></span></th>
                                    <th><span class="fr-th">Reason <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Exact remark text from the GRID FAIL sheet (Reason/Remarks).">i</button></span></th>
                                    <th><span class="fr-th">Events <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="How many times this exact reason appears in the filtered data.">i</button></span></th>
                                    <th><span class="fr-th">Downtime <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Total duration of all events with this reason (H:MM:SS).">i</button></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($t['top_reasons'] as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($row['reason'], 70) }}</td>
                                        <td>{{ $row['events'] }}</td>
                                        <td>{{ $row['downtime'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="fr-empty">No reasons</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="fr-mobile-cards">
                        @forelse($t['top_reasons'] as $row)
                            <div class="fr-li">
                                <span class="n">{{ $loop->iteration }}</span>
                                <p>{{ $row['reason'] }}</p>
                                <div class="m"><b>{{ $row['events'] }} evt</b>{{ $row['downtime'] }}</div>
                            </div>
                        @empty
                            <div class="fr-m-empty">No reasons</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">
                        <span>Category Breakdown</span>
                    </div>
                    <div class="fr-card-b p-0 table-responsive fr-desktop-only">
                        <table class="table table-sm fr-table mb-0">
                            <thead>
                                <tr>
                                    <th><span class="fr-th">Category <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Grouped cause type (NEA/Grid Trip, Voltage, Planned stop, etc.).">i</button></span></th>
                                    <th><span class="fr-th">Events <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Number of events in this category.">i</button></span></th>
                                    <th><span class="fr-th">Share <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Percent of total events that fall in this category.">i</button></span></th>
                                    <th><span class="fr-th">Downtime <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Total downtime for this category (H:MM:SS).">i</button></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t['categories'] as $row)
                                    <tr>
                                        <td>
                                            <span class="fr-badge {{ str_contains($row['category'],'NEA') ? 'nea' : (str_contains($row['category'],'Planned') ? 'planned' : (str_contains($row['category'],'Voltage') ? 'volt' : '')) }}">
                                                {{ $row['category'] }}
                                            </span>
                                        </td>
                                        <td>{{ $row['events'] }}</td>
                                        <td>{{ $k['total_events'] ? number_format(($row['events'] / $k['total_events']) * 100, 1) : 0 }}%</td>
                                        <td>{{ $row['downtime'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="fr-mobile-cards">
                        @php
                            $catColors = ['#d6334a','#e0a000','#12a150','#0e9fa8','#8a8fae','#7a4fd6','#5b6cf0','#c75b24','#2a8fd0'];
                        @endphp
                        @foreach($t['categories'] as $row)
                            @php $share = $k['total_events'] ? number_format(($row['events'] / $k['total_events']) * 100, 1) : 0; @endphp
                            <div class="fr-li" style="align-items:center">
                                <span class="dot" style="background:{{ $catColors[$loop->index % count($catColors)] }}"></span>
                                <p>{{ $row['category'] }}</p>
                                <div class="m"><b>{{ $row['events'] }} · {{ $share }}%</b>{{ $row['downtime'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="fr-card">
                    <div class="fr-card-h">Longest Outages</div>
                    <div class="fr-card-b p-0 table-responsive fr-desktop-only">
                        <table class="table table-sm fr-table mb-0">
                            <thead>
                                <tr>
                                    <th><span class="fr-th">Unit <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Which generation unit was affected (Unit 1 or Unit 2).">i</button></span></th>
                                    <th><span class="fr-th">Date <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Nepali (BS) date of the outage event.">i</button></span></th>
                                    <th><span class="fr-th">From → Synch <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="From = trip/stop start time. Synch = when the unit was synchronized back.">i</button></span></th>
                                    <th><span class="fr-th">Duration <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Outage length for this event (usually Synch − From).">i</button></span></th>
                                    <th><span class="fr-th">Reason <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Operator remark explaining why the unit tripped or was stopped.">i</button></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t['longest'] as $row)
                                    <tr>
                                        <td>{{ $row['unit'] }}</td>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ $row['from_hrs'] }} → {{ $row['synch_hrs'] }}</td>
                                        <td><strong>{{ $row['duration'] }}</strong></td>
                                        <td>{{ \Illuminate\Support\Str::limit($row['reason'], 45) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="fr-mobile-cards">
                        @foreach($t['longest'] as $row)
                            @php $uNum = str_contains((string)$row['unit'], '2') ? '2' : '1'; @endphp
                            <div class="fr-ev">
                                <div class="t">
                                    <span class="fr-u u{{ $uNum }}">Unit {{ $uNum }}</span>
                                    <span class="x">{{ $row['date'] }}</span>
                                    <span class="d">{{ $row['duration'] }}</span>
                                </div>
                                <div class="x">{{ $row['from_hrs'] }} → {{ $row['synch_hrs'] }} synch</div>
                                <div class="y">{{ $row['reason'] }}</div>
                            </div>
                        @endforeach
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
                                    <th><span class="fr-th">Date <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Nepali date for the daily summary row.">i</button></span></th>
                                    <th><span class="fr-th">Events <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Total events on this day (both units).">i</button></span></th>
                                    <th><span class="fr-th">Unit 1 <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Unit 1 event count on this day.">i</button></span></th>
                                    <th><span class="fr-th">Unit 2 <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Unit 2 event count on this day.">i</button></span></th>
                                    <th><span class="fr-th">Downtime <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Combined downtime of all events on this day.">i</button></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t['daily'] as $row)
                                    <tr>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ $row['events'] }}</td>
                                        <td>{{ $row['unit1_events'] }}</td>
                                        <td>{{ $row['unit2_events'] }}</td>
                                        <td>{{ $row['downtime'] }}</td>
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
                                    <th>Evt</th>
                                    <th>U1</th>
                                    <th>U2</th>
                                    <th>Downtime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($t['daily'] as $row)
                                    <tr>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ $row['events'] }}</td>
                                        <td>{{ $row['unit1_events'] }}</td>
                                        <td>{{ $row['unit2_events'] }}</td>
                                        <td><b>{{ $row['downtime'] }}</b></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="fr-m-empty">No daily summary</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="fr-card">
            <div class="fr-card-h">
                <span>Recent Events</span>
                <a href="{{ route('admin.import_export', ['tab' => 'failure']) }}" class="btn btn-sm btn-outline-secondary">Manage Imports</a>
            </div>
            <div class="fr-card-b p-0 table-responsive fr-desktop-only">
                <table class="table table-sm fr-table mb-0">
                    <thead>
                        <tr>
                            <th><span class="fr-th">Unit <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Generation unit for this event.">i</button></span></th>
                            <th><span class="fr-th">Date <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Nepali (BS) event date from the GRID FAIL sheet.">i</button></span></th>
                            <th><span class="fr-th">From <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Time when the unit tripped or was stopped (FROM HRS).">i</button></span></th>
                            <th><span class="fr-th">To <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Time when grid/power returned (TO HRS). May be empty.">i</button></span></th>
                            <th><span class="fr-th">Synch <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Time when the unit was synchronized back online (SYNCH HRS).">i</button></span></th>
                            <th><span class="fr-th">Duration <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Total outage duration for this event (DURATION HRS).">i</button></span></th>
                            <th><span class="fr-th">Category <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Auto-grouped cause category based on the reason text.">i</button></span></th>
                            <th><span class="fr-th">Reason <button type="button" class="fr-info-btn" data-bs-toggle="tooltip" title="Full operator remark / cause description.">i</button></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($t['recent'] as $row)
                            <tr>
                                <td>{{ $row['unit'] }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['from_hrs'] }}</td>
                                <td>{{ $row['to_hrs'] }}</td>
                                <td>{{ $row['synch_hrs'] }}</td>
                                <td>{{ $row['duration'] }}</td>
                                <td>
                                    <span class="fr-badge {{ str_contains($row['category'],'NEA') ? 'nea' : (str_contains($row['category'],'Planned') ? 'planned' : '') }}">
                                        {{ $row['category'] }}
                                    </span>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($row['reason'], 60) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="fr-mobile-cards">
                @forelse($t['recent'] as $row)
                    @php $uNum = str_contains((string)$row['unit'], '2') ? '2' : '1'; @endphp
                    <div class="fr-ev">
                        <div class="t">
                            <span class="fr-u u{{ $uNum }}">Unit {{ $uNum }}</span>
                            <span class="x">{{ $row['date'] }}</span>
                            <span class="d">{{ $row['duration'] }}</span>
                        </div>
                        <div class="x">{{ $row['from_hrs'] }} → {{ $row['to_hrs'] ?: '—' }} · synch {{ $row['synch_hrs'] }}</div>
                        <div class="y">
                            <span class="fr-pill {{ str_contains($row['category'],'NEA') ? 'nea' : (str_contains($row['category'],'Planned') ? 'planned' : (str_contains($row['category'],'Voltage') ? 'volt' : '')) }}">
                                {{ $row['category'] }}
                            </span>
                            {{ $row['reason'] }}
                        </div>
                    </div>
                @empty
                    <div class="fr-m-empty">No recent events</div>
                @endforelse
            </div>
        </div>
        <a href="{{ route('admin.import_export', ['tab' => 'failure']) }}" class="fr-more-btn d-lg-none">Manage imports</a>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    // Nepali (BS) calendar for filters
    if (window.NepaliDatePicker && typeof NepaliDatePicker.attach === 'function') {
        ['#failureStartBs', '#failureEndBs'].forEach(function (selector) {
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

    // Info tooltips on KPI cards / filters / table headers
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

    // Chart.js only on desktop — mobile uses CSS/SVG figures
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
                    label: 'Events',
                    data: charts.daily_events,
                    backgroundColor: 'rgba(10,161,170,0.35)',
                    borderColor: accent,
                    borderWidth: 1,
                    yAxisID: 'y',
                },
                {
                    type: 'line',
                    label: 'Downtime (min)',
                    data: charts.daily_downtime_minutes,
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
                y: { beginAtZero: true, title: { display: true, text: 'Events' } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Minutes' } }
            }
        }
    });

    new Chart(document.getElementById('unitChart'), {
        type: 'doughnut',
        data: {
            labels: charts.unit_labels,
            datasets: [{
                data: charts.unit_events,
                backgroundColor: [accent, warn],
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: charts.category_labels,
            datasets: [{
                label: 'Events',
                data: charts.category_events,
                backgroundColor: [warn, blue, accent, green, '#8b5cf6', '#f59e0b', '#64748b', '#ec4899'],
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('hourChart'), {
        type: 'bar',
        data: {
            labels: charts.hour_labels,
            datasets: [{
                label: 'Events',
                data: charts.hour_events,
                backgroundColor: 'rgba(59,130,246,0.45)',
                borderColor: blue,
                borderWidth: 1,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
})();
</script>
@endsection
