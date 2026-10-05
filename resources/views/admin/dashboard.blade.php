@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.css">
<style>
    .hp-dash {
        --hp-blue: #2f6bff;
        --hp-blue-soft: #eaf0ff;
        --hp-green: #16a34a;
        --hp-green-soft: #e9f9ef;
        --hp-red: #ef4444;
        --hp-red-soft: #fdecec;
        --hp-purple: #7c3aed;
        --hp-purple-soft: #f3eaff;
        --hp-ink: #1f2937;
        --hp-mute: #6b7280;
        --hp-line: #e8ecf3;
        --hp-bg: #f5f7fb;
        --hp-teal: #0aa1aa;
        padding: 1.25rem 1.25rem 2rem;
        color: var(--hp-ink);
    }
    .hp-dash .hp-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
        margin-bottom: 1.15rem;
    }
    .hp-dash .hp-head h3 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    .hp-dash .hp-head p {
        margin: .35rem 0 0;
        color: var(--hp-mute);
        font-size: .95rem;
    }
    .hp-dash .hp-range-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .55rem;
    }
    .hp-dash .hp-range {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        background: #fff;
        border: 1px solid var(--hp-line);
        border-radius: 10px;
        padding: .55rem .85rem;
        color: var(--hp-ink);
        font-size: .88rem;
        font-weight: 500;
        min-width: 190px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }
    .hp-dash .hp-range i { color: var(--hp-mute); }
    .hp-dash .hp-range-form .nepali-datepicker {
        width: 150px;
        border: 1px solid var(--hp-line);
        border-radius: 10px;
        padding: .55rem .75rem;
        font-size: .88rem;
        background: #fff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }
    .hp-dash .hp-apply {
        background: var(--hp-teal);
        border: 1px solid var(--hp-teal);
        color: #fff;
        border-radius: 10px;
        padding: .5rem 1rem;
        font-size: .88rem;
        font-weight: 600;
    }
    .hp-dash .hp-apply:hover { filter: brightness(.95); color: #fff; }
    .ndp-container, .ndp-popup, [class*="nepali-date"] { z-index: 1080 !important; }
    .hp-dash .hp-card {
        background: #fff;
        border: 1px solid var(--hp-line);
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
        height: 100%;
    }
    .hp-dash .hp-kpi {
        padding: 1.1rem 1.15rem 1rem;
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }
    .hp-dash .hp-kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .75rem;
    }
    .hp-dash .hp-kpi-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }
    .hp-dash .hp-kpi-icon.blue { background: var(--hp-blue-soft); color: var(--hp-blue); }
    .hp-dash .hp-kpi-icon.green { background: var(--hp-green-soft); color: var(--hp-green); }
    .hp-dash .hp-kpi-icon.red { background: var(--hp-red-soft); color: var(--hp-red); }
    .hp-dash .hp-kpi-icon.purple { background: var(--hp-purple-soft); color: var(--hp-purple); }
    .hp-dash .hp-kpi-icon.teal { background: #e6f7f8; color: var(--hp-teal); }
    .hp-dash .hp-kpi-icon.orange { background: #fff4e8; color: #ea580c; }
    .hp-dash .hp-kpi-icon.indigo { background: #eef2ff; color: #4f46e5; }
    .hp-dash .hp-kpi-icon.cyan { background: #ecfeff; color: #0891b2; }
    .hp-dash .hp-kpi-label {
        font-size: .82rem;
        color: var(--hp-mute);
        margin-bottom: .25rem;
    }
    .hp-dash .hp-kpi-value {
        font-size: 1.55rem;
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }
    .hp-dash .hp-kpi-sub {
        font-size: .75rem;
        color: var(--hp-mute);
        margin-top: .3rem;
    }
    .hp-dash .hp-kpi-detail {
        font-size: .72rem;
        color: #94a3b8;
        margin-top: .15rem;
    }
    .hp-dash .hp-kpi-foot {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: .75rem;
    }
    .hp-dash .hp-trend {
        font-size: .75rem;
        font-weight: 600;
        color: var(--hp-green);
    }
    .hp-dash .hp-trend.flat { color: var(--hp-mute); }
    .hp-dash .hp-trend.down { color: var(--hp-red); }
    .hp-dash .hp-spark {
        width: 88px;
        height: 34px;
    }
    .hp-dash .hp-panel {
        padding: 1.1rem 1.2rem 1.2rem;
    }
    .hp-dash .hp-panel-h {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1rem;
    }
    .hp-dash .hp-panel-h h5 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
    }
    .hp-dash .hp-mini-select {
        border: 1px solid var(--hp-line);
        background: #fff;
        border-radius: 8px;
        padding: .35rem .65rem;
        font-size: .8rem;
        color: var(--hp-ink);
    }
    .hp-dash .hp-chart-wrap {
        position: relative;
        height: 280px;
    }
    .hp-dash .hp-donut-wrap {
        position: relative;
        height: 220px;
        max-width: 260px;
        margin: 0 auto;
    }
    .hp-dash .hp-donut-center {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        text-align: center;
    }
    .hp-dash .hp-donut-center strong {
        font-size: 1.55rem;
        line-height: 1;
    }
    .hp-dash .hp-donut-center span {
        font-size: .78rem;
        color: var(--hp-mute);
        margin-top: .25rem;
    }
    .hp-dash .hp-legend {
        list-style: none;
        margin: 1rem 0 0;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .55rem .85rem;
    }
    .hp-dash .hp-legend li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        font-size: .86rem;
        color: var(--hp-mute);
    }
    .hp-dash .hp-legend .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: .4rem;
    }
    .hp-dash .hp-legend b { color: var(--hp-ink); font-weight: 600; }
    .hp-dash .hp-table {
        width: 100%;
        margin: 0;
    }
    .hp-dash .hp-table th {
        font-size: .75rem;
        font-weight: 600;
        color: var(--hp-mute);
        border-bottom: 1px solid var(--hp-line);
        padding: .65rem .4rem;
        white-space: nowrap;
        background: transparent;
    }
    .hp-dash .hp-table td {
        font-size: .88rem;
        padding: .8rem .4rem;
        border-bottom: 1px solid #f1f4f8;
        vertical-align: middle;
    }
    .hp-dash .hp-table tr:last-child td { border-bottom: 0; }
    .hp-dash .hp-status {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        font-weight: 500;
    }
    .hp-dash .hp-status .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--hp-green);
    }
    .hp-dash .hp-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .2rem .65rem;
        font-size: .75rem;
        font-weight: 600;
    }
    .hp-dash .hp-badge.open { background: #fee2e2; color: #b91c1c; }
    .hp-dash .hp-badge.solved { background: #dcfce7; color: #15803d; }
    .hp-dash .hp-sev-major { color: #ea580c; font-weight: 600; }
    .hp-dash .hp-sev-resolved { color: var(--hp-blue); font-weight: 600; }
    .hp-dash .hp-view-all {
        color: var(--hp-blue);
        font-size: .84rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }
    .hp-dash .hp-view-all:hover { text-decoration: underline; }
    .hp-dash .hp-empty {
        text-align: center;
        color: var(--hp-mute);
        padding: 1.5rem .5rem;
        font-size: .9rem;
    }
    @media (max-width: 767.98px) {
        .hp-dash { padding: 1rem .75rem 1.5rem; }
        .hp-dash .hp-head h3 { font-size: 1.4rem; }
        .hp-dash .hp-chart-wrap { height: 240px; }
    }
</style>
@endsection

@section('content')
@php
    $k = $dashboard['kpis'];
    $charts = $dashboard['charts'];
    $fmtTrend = function ($pct) {
        $pct = (float) $pct;
        $abs = abs($pct);
        if ($pct > 0) return ['up', '↑ ' . $abs . '% vs prior period'];
        if ($pct < 0) return ['down', '↓ ' . $abs . '% vs prior period'];
        return ['flat', '→ 0% vs prior period'];
    };
    $kpiCards = [
        ['key' => 'users', 'icon' => 'fa-users', 'color' => 'blue', 'spark' => 'sparkUsers'],
        ['key' => 'main_mwh', 'icon' => 'fa-bolt', 'color' => 'green', 'spark' => 'sparkMain', 'label' => 'Main Generation (MWh)'],
        ['key' => 'check_mwh', 'icon' => 'fa-gauge-high', 'color' => 'teal', 'spark' => 'sparkCheck', 'label' => 'Check Generation (MWh)'],
        ['key' => 'unit1_mwh', 'icon' => 'fa-industry', 'color' => 'orange', 'spark' => 'sparkU1', 'label' => 'Unit 1 Energy (MWh)'],
        ['key' => 'unit2_mwh', 'icon' => 'fa-industry', 'color' => 'indigo', 'spark' => 'sparkU2', 'label' => 'Unit 2 Energy (MWh)'],
        ['key' => 'faults', 'icon' => 'fa-triangle-exclamation', 'color' => 'red', 'spark' => 'sparkFaults'],
        ['key' => 'outages', 'icon' => 'fa-plug-circle-xmark', 'color' => 'cyan', 'spark' => 'sparkOutages', 'label' => 'Generator Outages'],
        ['key' => 'imports', 'icon' => 'fa-file-import', 'color' => 'purple', 'spark' => 'sparkImports', 'label' => 'Import Batches'],
    ];
    $labels = [
        'users' => 'Total Users',
        'faults' => 'Total Faults',
    ];
@endphp
<div class="container-fluid hp-dash">
    <div class="hp-head">
        <div>
            <h3>Dashboard</h3>
            <p>Welcome back — overview for <strong>{{ $dashboard['range']['label'] }}</strong> ({{ $dashboard['range']['days'] }} BS days).</p>
        </div>
        <form method="GET" action="{{ route('dashboard') }}" class="hp-range-form" id="hpRangeForm">
            <div class="hp-range" title="Selected BS range">
                <i class="fa-regular fa-calendar"></i>
                <span id="hpRangeLabel">{{ $dashboard['range']['label'] }}</span>
            </div>
            <input type="text" name="start" id="hpStartBs" class="form-control form-control-sm nepali-datepicker"
                   value="{{ $dashboard['range']['start'] }}" placeholder="Start (BS)" autocomplete="off" readonly>
            <input type="text" name="end" id="hpEndBs" class="form-control form-control-sm nepali-datepicker"
                   value="{{ $dashboard['range']['end'] }}" placeholder="End (BS)" autocomplete="off" readonly>
            <button type="submit" class="btn hp-apply">Apply</button>
        </form>
    </div>

    <div class="row g-3 mb-3">
        @foreach($kpiCards as $card)
            @php
                $item = $k[$card['key']];
                [$tClass, $tText] = $fmtTrend($item['trend']);
                $title = $card['label'] ?? ($labels[$card['key']] ?? ucfirst(str_replace('_', ' ', $card['key'])));
            @endphp
            <div class="col-sm-6 col-xl-3">
                <div class="hp-card hp-kpi">
                    <div class="hp-kpi-top">
                        <div>
                            <div class="hp-kpi-label">{{ $title }}</div>
                            <div class="hp-kpi-value">
                                @if(str_contains($card['key'], 'mwh'))
                                    {{ number_format($item['value'], 2) }}
                                @else
                                    {{ number_format($item['value']) }}
                                @endif
                            </div>
                            <div class="hp-kpi-sub">{{ $item['sub'] }}</div>
                            <div class="hp-kpi-detail">{{ $item['detail'] }}</div>
                        </div>
                        <span class="hp-kpi-icon {{ $card['color'] }}"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                    </div>
                    <div class="hp-kpi-foot">
                        <span class="hp-trend {{ $tClass }}">{{ $tText }}</span>
                        <canvas class="hp-spark" id="{{ $card['spark'] }}" height="34" width="88"></canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="hp-card hp-panel">
                <div class="hp-panel-h">
                    <h5>Generation Overview</h5>
                    <span class="text-muted small">Plant daily energy (MWh)</span>
                </div>
                <div class="hp-chart-wrap">
                    <canvas id="generationChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="hp-card hp-panel">
                <div class="hp-panel-h">
                    <h5>Fault Status</h5>
                    <span class="text-muted small">All time</span>
                </div>
                <div class="hp-donut-wrap">
                    <canvas id="faultChart"></canvas>
                    <div class="hp-donut-center">
                        <strong>{{ number_format($charts['faults']['total']) }}</strong>
                        <span>Total Faults</span>
                    </div>
                </div>
                <ul class="hp-legend">
                    <li><span><span class="dot" style="background:#ef4444"></span>Critical</span><b>{{ $charts['faults']['critical'] }}</b></li>
                    <li><span><span class="dot" style="background:#f97316"></span>Open / Major</span><b>{{ $charts['faults']['major'] }}</b></li>
                    <li><span><span class="dot" style="background:#eab308"></span>Minor</span><b>{{ $charts['faults']['minor'] }}</b></li>
                    <li><span><span class="dot" style="background:#3b82f6"></span>Resolved</span><b>{{ $charts['faults']['resolved'] }}</b></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="hp-card hp-panel">
                <div class="hp-panel-h">
                    <h5>Unit 1 vs Unit 2</h5>
                    <a href="{{ route('admin.reports.log') }}" class="hp-view-all">Log Report →</a>
                </div>
                <div class="hp-chart-wrap">
                    <canvas id="unitChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="hp-card hp-panel">
                <div class="hp-panel-h">
                    <h5>Recent Imports</h5>
                    <a href="{{ route('admin.import_export') }}" class="hp-view-all">Import / Export →</a>
                </div>
                <div class="table-responsive">
                    <table class="hp-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>File</th>
                                <th>Rows</th>
                                <th>When</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboard['recent_imports'] as $batch)
                                <tr>
                                    <td>{{ ucfirst($batch->type) }}</td>
                                    <td title="{{ $batch->original_filename }}">{{ \Illuminate\Support\Str::limit($batch->original_filename, 22) }}</td>
                                    <td>{{ number_format($batch->record_count) }}</td>
                                    <td>{{ optional($batch->imported_at)->format('M d H:i') ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="hp-empty">No imports yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-7">
            <div class="hp-card hp-panel">
                <div class="hp-panel-h">
                    <h5>Recent Generation Data</h5>
                    <a href="{{ route('admin.reports.generation') }}" class="hp-view-all">View All →</a>
                </div>
                <div class="table-responsive">
                    <table class="hp-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Hours / Time</th>
                                <th>Energy (MWh)</th>
                                <th>Avg KW</th>
                                <th>Plant / Unit</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboard['recent_generation'] as $row)
                                <tr>
                                    <td>{{ $row->date }}</td>
                                    <td>{{ $row->time }}</td>
                                    <td>{{ number_format((float) $row->generation_mwh, 3) }}</td>
                                    <td>{{ $row->avg_kw === null ? '—' : number_format((float) $row->avg_kw, 0) }}</td>
                                    <td>{{ $row->unit }}</td>
                                    <td>
                                        <span class="hp-status">
                                            <span class="dot"></span> {{ $row->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="hp-empty">No generation rows yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="hp-card hp-panel">
                <div class="hp-panel-h">
                    <h5>Recent Faults</h5>
                    <a href="{{ route('admin.reports.fault') }}" class="hp-view-all">View All →</a>
                </div>
                <div class="table-responsive">
                    <table class="hp-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Fault Description</th>
                                <th>Severity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboard['recent_faults'] as $fault)
                                @php
                                    $when = $fault->fault_time ? \Carbon\Carbon::parse($fault->fault_time) : null;
                                    $isOpen = $fault->status === 'Unsolved';
                                @endphp
                                <tr>
                                    <td>{{ $when ? $when->format('M d, Y') : '—' }}</td>
                                    <td>{{ $when ? $when->format('H:i') : '—' }}</td>
                                    <td>{{ $fault->reason ?: '—' }}</td>
                                    <td class="{{ $isOpen ? 'hp-sev-major' : 'hp-sev-resolved' }}">
                                        {{ $isOpen ? 'Major' : 'Resolved' }}
                                    </td>
                                    <td>
                                        <span class="hp-badge {{ $isOpen ? 'open' : 'solved' }}">
                                            {{ $isOpen ? 'Open' : 'Resolved' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="hp-empty">No faults recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    function syncRangeLabel() {
        var startEl = document.getElementById('hpStartBs');
        var endEl = document.getElementById('hpEndBs');
        var label = document.getElementById('hpRangeLabel');
        if (!startEl || !endEl || !label) return;
        if (startEl.value && endEl.value) {
            label.textContent = startEl.value + ' - ' + endEl.value;
        }
    }

    if (window.NepaliDatePicker && typeof NepaliDatePicker.attach === 'function') {
        ['#hpStartBs', '#hpEndBs'].forEach(function (selector) {
            var el = document.querySelector(selector);
            if (!el) return;
            NepaliDatePicker.attach(selector, {
                language: 'en',
                onChange: function (date) {
                    if (date && typeof date.format === 'function') {
                        el.value = date.format('YYYY-MM-DD');
                        syncRangeLabel();
                    }
                },
            });
        });
    }

    var genLabels = @json($charts['generation']['labels']);
    var genValues = @json($charts['generation']['values']);
    var unitLabels = @json($charts['units']['labels']);
    var unit1 = @json($charts['units']['unit1']);
    var unit2 = @json($charts['units']['unit2']);
    var faultData = [
        {{ (int) $charts['faults']['critical'] }},
        {{ (int) $charts['faults']['major'] }},
        {{ (int) $charts['faults']['minor'] }},
        {{ (int) $charts['faults']['resolved'] }}
    ];
    var sparks = {
        sparkUsers: @json($k['users']['spark']),
        sparkMain: @json($k['main_mwh']['spark']),
        sparkCheck: @json($k['check_mwh']['spark']),
        sparkU1: @json($k['unit1_mwh']['spark']),
        sparkU2: @json($k['unit2_mwh']['spark']),
        sparkFaults: @json($k['faults']['spark']),
        sparkOutages: @json($k['outages']['spark']),
        sparkImports: @json($k['imports']['spark'])
    };
    var sparkColors = {
        sparkUsers: '#2f6bff',
        sparkMain: '#16a34a',
        sparkCheck: '#0aa1aa',
        sparkU1: '#ea580c',
        sparkU2: '#4f46e5',
        sparkFaults: '#ef4444',
        sparkOutages: '#0891b2',
        sparkImports: '#7c3aed'
    };

    function makeSpark(id, values, color) {
        var el = document.getElementById(id);
        if (!el || !window.Chart) return;
        new Chart(el, {
            type: 'line',
            data: {
                labels: values.map(function (_, i) { return i; }),
                datasets: [{
                    data: values,
                    borderColor: color,
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 0
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: { x: { display: false }, y: { display: false } }
            }
        });
    }

    Object.keys(sparks).forEach(function (id) {
        makeSpark(id, sparks[id] || [0, 0, 0, 0, 0, 0, 0], sparkColors[id]);
    });

    var genCtx = document.getElementById('generationChart');
    if (genCtx && window.Chart) {
        new Chart(genCtx, {
            type: 'line',
            data: {
                labels: genLabels,
                datasets: [{
                    label: 'Generation (MWh)',
                    data: genValues,
                    borderColor: '#2f6bff',
                    backgroundColor: function (context) {
                        var chart = context.chart;
                        var area = chart.chartArea;
                        if (!area) return 'rgba(47,107,255,0.12)';
                        var g = chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
                        g.addColorStop(0, 'rgba(47,107,255,0.28)');
                        g.addColorStop(1, 'rgba(47,107,255,0.02)');
                        return g;
                    },
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2f6bff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: 11 }, maxRotation: 0 } },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Generation (MWh)', color: '#6b7280', font: { size: 11 } },
                        grid: { color: '#eef1f6' },
                        ticks: { color: '#6b7280', font: { size: 11 } }
                    }
                }
            }
        });
    }

    var unitCtx = document.getElementById('unitChart');
    if (unitCtx && window.Chart) {
        new Chart(unitCtx, {
            type: 'line',
            data: {
                labels: unitLabels,
                datasets: [
                    {
                        label: 'Unit 1',
                        data: unit1,
                        borderColor: '#ea580c',
                        backgroundColor: 'rgba(234,88,12,0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.2,
                        pointRadius: 2
                    },
                    {
                        label: 'Unit 2',
                        data: unit2,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79,70,229,0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.2,
                        pointRadius: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { boxWidth: 10, usePointStyle: true } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: 11 }, maxRotation: 0 } },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Energy (MWh)', color: '#6b7280', font: { size: 11 } },
                        grid: { color: '#eef1f6' },
                        ticks: { color: '#6b7280', font: { size: 11 } }
                    }
                }
            }
        });
    }

    var faultCtx = document.getElementById('faultChart');
    if (faultCtx && window.Chart) {
        var hasFault = faultData.some(function (v) { return v > 0; });
        new Chart(faultCtx, {
            type: 'doughnut',
            data: {
                labels: ['Critical', 'Open / Major', 'Minor', 'Resolved'],
                datasets: [{
                    data: hasFault ? faultData : [1],
                    backgroundColor: hasFault ? ['#ef4444', '#f97316', '#eab308', '#3b82f6'] : ['#e5e7eb'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { display: false }, tooltip: { enabled: hasFault } }
            }
        });
    }
})();
</script>
@endsection
