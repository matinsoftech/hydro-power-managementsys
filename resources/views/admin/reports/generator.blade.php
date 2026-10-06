@extends('layouts.app')

@section('title', 'Generator Meter Report')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.css">
<style>
    .fr-page { --fr-accent: #0aa1aa; --fr-muted: #6b7c82; --fr-border: #e6ecee; --fr-text: #1f2d32; }
    .fr-page .fr-head { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; align-items: flex-end; margin-bottom: 1rem; }
    .fr-page .fr-head h4 { margin: 0; font-weight: 600; }
    .fr-page .fr-head p { margin: 4px 0 0; color: var(--fr-muted); font-size: .9rem; }
    .fr-filters { background: #fff; border: 1px solid var(--fr-border); border-radius: 10px; padding: .85rem 1rem; margin-bottom: 1rem; }
    .fr-presets { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: .75rem; }
    .fr-preset { border: 1px solid var(--fr-border); background: #fff; color: var(--fr-text); border-radius: 999px; padding: .28rem .7rem; font-size: .75rem; font-weight: 600; }
    .fr-preset:hover { border-color: var(--fr-accent); color: #087880; }
    .fr-preset.active { background: var(--fr-accent); border-color: var(--fr-accent); color: #fff; }
    .fr-kpi { border-radius: 10px; padding: .7rem .85rem .75rem; height: 100%; }
    .fr-kpi.u1 { background: #fdf1e9; color: #9a4318; }
    .fr-kpi.u2 { background: #eaf1ff; color: #1d4ed8; }
    .fr-kpi .label { font-size: .72rem; opacity: .75; }
    .fr-kpi .value { font-size: 1.2rem; font-weight: 700; line-height: 1.2; }
    .fr-kpi .sub { font-size: .7rem; opacity: .7; margin-top: .25rem; }
    .fr-meter-title { font-size: .75rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; margin: 0 0 .4rem .15rem; }
    .fr-meter-group.u1 .fr-meter-title { color: #9a4318; }
    .fr-meter-group.u2 .fr-meter-title { color: #1d4ed8; }
    .fr-meter-group { margin-bottom: .85rem; }
    .fr-card { border: 1px solid var(--fr-border); border-radius: 10px; background: #fff; margin-bottom: 1rem; overflow: hidden; }
    .fr-card-h { padding: .75rem 1rem; font-weight: 600; border-bottom: 1px solid var(--fr-border); display: flex; justify-content: space-between; align-items: center; }
    .fr-table th { white-space: nowrap; font-size: .8rem; background: #f5f8f9; text-align: center; }
    .fr-table td { font-size: .88rem; vertical-align: middle; text-align: center; }
    .fr-table td:first-child, .fr-table th:first-child { text-align: left; }
    .fr-empty { text-align: center; color: var(--fr-muted); padding: 2rem 1rem; }
    .btn-fr { background: var(--fr-accent); border-color: var(--fr-accent); color: #fff; border-radius: 8px; }
    .fr-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .ndp-container, .ndp-popup, [class*="nepali-date"] { z-index: 1080 !important; }
    @media print {
        .left-sidebar, .topbar, .fr-filters, .btn, .sidebar-nav, .scroll-sidebar, .app-bottom-bar { display: none !important; }
        .page-wrapper { margin: 0 !important; }
    }
</style>
@endsection

@section('content')
@php
    $k = $report['kpis'];
    $unit = $filters['unit'] ?? '';
    $show1 = $unit !== '2';
    $show2 = $unit !== '1';
    $fmt = function ($value) {
        return $value === null || $value === '' ? '—' : number_format((float) $value, 0);
    };
@endphp
<div class="container-fluid fr-page">
    <div class="fr-head">
        <div>
            <h4>Generator Meter Report</h4>
            <p>
                Monthly Unit 1 / Unit 2 initial, final &amp; total generation
                @if($report['date_min'])
                    <br class="d-lg-none">{{ $report['date_min'] }} → {{ $report['date_max'] }}
                @endif
            </p>
        </div>
        <div class="fr-actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fa fa-print me-1"></i> Print
            </button>
            <a href="{{ route('admin.reports.generator.export', request()->query()) }}" class="btn btn-fr btn-sm">
                <i class="fa fa-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.generator') }}" class="fr-filters" id="generatorFilterForm">
        <div class="fr-presets" role="group" aria-label="Quick date range">
            <button type="button" class="fr-preset" data-range="today">Today</button>
            <button type="button" class="fr-preset" data-range="yesterday">Yesterday</button>
            <button type="button" class="fr-preset" data-range="this_week">This week</button>
            <button type="button" class="fr-preset" data-range="last_week">Last week</button>
            <button type="button" class="fr-preset" data-range="last_month">Last month</button>
            <button type="button" class="fr-preset" data-range="this_month">This month</button>
        </div>
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Unit</label>
                <select name="unit" class="form-select form-select-sm">
                    <option value="">All units</option>
                    <option value="1" @selected($unit == '1')>Unit 1</option>
                    <option value="2" @selected($unit == '2')>Unit 2</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Start date (BS)</label>
                <input type="text" name="start" id="generatorStartBs" class="form-control form-control-sm nepali-datepicker"
                       value="{{ $filters['start'] ?? '' }}" placeholder="{{ $allMin ?: '2083-06-01' }}" autocomplete="off" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">End date (BS)</label>
                <input type="text" name="end" id="generatorEndBs" class="form-control form-control-sm nepali-datepicker"
                       value="{{ $filters['end'] ?? '' }}" placeholder="{{ $allMax ?: '2083-06-30' }}" autocomplete="off" readonly>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-fr btn-sm">Apply Filter</button>
                <a href="{{ route('admin.reports.generator') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    @if($k['rows'] === 0)
        <div class="fr-card">
            <div class="fr-empty">
                No generator meter data for this filter.
                <div class="mt-2">
                    <a href="{{ route('admin.import_export', ['tab' => 'generator']) }}">Generator Meter Import</a> first.
                </div>
            </div>
        </div>
    @else
        @if($show1)
            <div class="fr-meter-group u1">
                <div class="fr-meter-title">Unit 1</div>
                <div class="row g-2">
                    <div class="col-6 col-lg-3"><div class="fr-kpi u1"><div class="label">Days</div><div class="value">{{ number_format($k['units'][1]['days']) }}</div><div class="sub">Logged days</div></div></div>
                    <div class="col-6 col-lg-3"><div class="fr-kpi u1"><div class="label">Total generation</div><div class="value">{{ number_format($k['units'][1]['generation_kwh'], 0) }}</div><div class="sub">kWh</div></div></div>
                    <div class="col-6 col-lg-3"><div class="fr-kpi u1"><div class="label">Avg / day</div><div class="value">{{ number_format($k['units'][1]['avg_generation_kwh'], 0) }}</div><div class="sub">kWh</div></div></div>
                    <div class="col-6 col-lg-3"><div class="fr-kpi u1"><div class="label">Last final</div><div class="value">{{ $fmt($k['units'][1]['last_final']) }}</div><div class="sub">Meter reading</div></div></div>
                </div>
            </div>
        @endif
        @if($show2)
            <div class="fr-meter-group u2">
                <div class="fr-meter-title">Unit 2</div>
                <div class="row g-2">
                    <div class="col-6 col-lg-3"><div class="fr-kpi u2"><div class="label">Days</div><div class="value">{{ number_format($k['units'][2]['days']) }}</div><div class="sub">Logged days</div></div></div>
                    <div class="col-6 col-lg-3"><div class="fr-kpi u2"><div class="label">Total generation</div><div class="value">{{ number_format($k['units'][2]['generation_kwh'], 0) }}</div><div class="sub">kWh</div></div></div>
                    <div class="col-6 col-lg-3"><div class="fr-kpi u2"><div class="label">Avg / day</div><div class="value">{{ number_format($k['units'][2]['avg_generation_kwh'], 0) }}</div><div class="sub">kWh</div></div></div>
                    <div class="col-6 col-lg-3"><div class="fr-kpi u2"><div class="label">Last final</div><div class="value">{{ $fmt($k['units'][2]['last_final']) }}</div><div class="sub">Meter reading</div></div></div>
                </div>
            </div>
        @endif

        <div class="fr-card">
            <div class="fr-card-h">
                <span>Daily Unit readings</span>
                <a href="{{ route('admin.import_export', ['tab' => 'generator']) }}" class="btn btn-sm btn-outline-secondary">Generator Meter Import</a>
            </div>
            <div class="table-responsive" style="max-height:560px;">
                <table class="table table-sm fr-table mb-0 table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">DATE</th>
                            @if($show1)
                                <th colspan="3">UNIT-1</th>
                            @endif
                            @if($show2)
                                <th colspan="3">UNIT-2</th>
                            @endif
                        </tr>
                        <tr>
                            @if($show1)
                                <th>Initial Reading</th>
                                <th>Final Reading</th>
                                <th>Total Generation (kWh)</th>
                            @endif
                            @if($show2)
                                <th>Initial Reading</th>
                                <th>Final Reading</th>
                                <th>Total Generation (kWh)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['tables']['combined'] as $day)
                            <tr>
                                <td>{{ $day['date'] }}</td>
                                @if($show1)
                                    <td>{{ $fmt($day['u1_initial']) }}</td>
                                    <td>{{ $fmt($day['u1_final']) }}</td>
                                    <td>{{ $fmt($day['u1_generation']) }}</td>
                                @endif
                                @if($show2)
                                    <td>{{ $fmt($day['u2_initial']) }}</td>
                                    <td>{{ $fmt($day['u2_final']) }}</td>
                                    <td>{{ $fmt($day['u2_generation']) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.js"></script>
<script>
(function () {
    if (window.NepaliDatePicker && typeof NepaliDatePicker.attach === 'function') {
        ['#generatorStartBs', '#generatorEndBs'].forEach(function (selector) {
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

    var ND = window.NepaliDatePicker && NepaliDatePicker.NepaliDate;
    var form = document.getElementById('generatorFilterForm');
    var startEl = document.getElementById('generatorStartBs');
    var endEl = document.getElementById('generatorEndBs');
    if (!ND || typeof ND.today !== 'function' || !form || !startEl || !endEl) return;

    function ymd(date) { return date.format('YYYY-MM-DD'); }
    function copy(date) { return ND.parse(ymd(date), 'YYYY-MM-DD'); }
    function shift(date, days) { return copy(date).add(days, 'day'); }
    function boundsFor(key, today) {
        var weekStart = shift(today, -today.getDay());
        var monthStart = shift(today, 1 - today.getDate());
        var prevMonthEnd = shift(monthStart, -1);
        if (key === 'today') return [today, today];
        if (key === 'yesterday') return [shift(today, -1), shift(today, -1)];
        if (key === 'this_week') return [weekStart, today];
        if (key === 'last_week') return [shift(weekStart, -7), shift(weekStart, -1)];
        if (key === 'this_month') return [monthStart, today];
        if (key === 'last_month') return [shift(prevMonthEnd, 1 - prevMonthEnd.getDate()), prevMonthEnd];
        return null;
    }
    var today = ND.today();
    document.querySelectorAll('#generatorFilterForm .fr-preset').forEach(function (button) {
        var bounds = boundsFor(button.getAttribute('data-range'), today);
        if (!bounds) return;
        var start = ymd(bounds[0]);
        var end = ymd(bounds[1]);
        if (startEl.value === start && endEl.value === end) button.classList.add('active');
        button.addEventListener('click', function () {
            startEl.value = start;
            endEl.value = end;
            form.submit();
        });
    });
})();
</script>
@endsection
