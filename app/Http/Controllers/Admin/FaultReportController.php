<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GridFailure;
use App\Services\GridFailureReportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FaultReportController extends Controller
{
    public function index(Request $request, GridFailureReportService $reportService)
    {
        $filters = [
            'unit' => $request->input('unit'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];

        $report = $reportService->build($filters);

        // Default filter bounds from full dataset when empty
        $allMin = GridFailure::min('date');
        $allMax = GridFailure::max('date');

        return view('admin.reports.fault', [
            'report' => $report,
            'filters' => $filters,
            'allMin' => $allMin,
            'allMax' => $allMax,
        ]);
    }

    public function export(Request $request, GridFailureReportService $reportService): StreamedResponse
    {
        $filters = [
            'unit' => $request->input('unit'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];

        $report = $reportService->build($filters);
        $rows = $reportService->query($filters)
            ->orderBy('date')
            ->orderBy('unit')
            ->orderBy('from_hrs')
            ->get();

        $spreadsheet = new Spreadsheet();

        // Summary sheet
        $summary = $spreadsheet->getActiveSheet();
        $summary->setTitle('Summary');
        $summary->fromArray([
            ['Metric', 'Value'],
            ['Total Events', $report['kpis']['total_events']],
            ['Total Downtime', $report['kpis']['total_downtime']],
            ['Average Downtime', $report['kpis']['avg_downtime']],
            ['Days Affected', $report['kpis']['days_affected']],
            ['Unit 1 Events', $report['kpis']['unit1_events']],
            ['Unit 1 Downtime', $report['kpis']['unit1_downtime']],
            ['Unit 2 Events', $report['kpis']['unit2_events']],
            ['Unit 2 Downtime', $report['kpis']['unit2_downtime']],
            ['NEA / Grid Trips', $report['kpis']['nea_trips']],
            ['NEA Downtime', $report['kpis']['nea_downtime']],
            ['Planned Stops', $report['kpis']['planned_stops']],
            ['Forced Outages', $report['kpis']['forced_outages']],
            ['Filter Unit', $filters['unit'] ?: 'All'],
            ['Filter Start', $filters['start'] ?: '—'],
            ['Filter End', $filters['end'] ?: '—'],
        ]);

        // Daily sheet
        $daily = $spreadsheet->createSheet();
        $daily->setTitle('Daily');
        $daily->fromArray([['Date', 'Events', 'Unit 1', 'Unit 2', 'Downtime']], null, 'A1');
        $r = 2;
        foreach ($report['tables']['daily'] as $day) {
            $daily->fromArray([[
                $day['date'],
                $day['events'],
                $day['unit1_events'],
                $day['unit2_events'],
                $day['downtime'],
            ]], null, 'A' . $r);
            $r++;
        }

        // Categories sheet
        $cats = $spreadsheet->createSheet();
        $cats->setTitle('Categories');
        $cats->fromArray([['Category', 'Events', 'Downtime']], null, 'A1');
        $r = 2;
        foreach ($report['tables']['categories'] as $cat) {
            $cats->fromArray([[$cat['category'], $cat['events'], $cat['downtime']]], null, 'A' . $r);
            $r++;
        }

        // Detail sheet
        $detail = $spreadsheet->createSheet();
        $detail->setTitle('Events');
        $detail->fromArray([[
            'Unit', 'Date', 'From', 'To', 'Synch', 'Duration', 'Reason', 'Category',
        ]], null, 'A1');
        $r = 2;
        foreach ($rows as $row) {
            $detail->fromArray([[
                'Unit ' . $row->unit,
                $row->date,
                $row->from_hrs,
                $row->to_hrs,
                $row->synch_hrs,
                $row->duration_hrs,
                $row->reason,
                $reportService->categorizeReason($row->reason),
            ]], null, 'A' . $r);
            $r++;
        }

        $filename = 'fault-report-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
