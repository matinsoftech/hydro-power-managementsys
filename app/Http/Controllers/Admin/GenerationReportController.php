<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GenerationReading;
use App\Services\GenerationReportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GenerationReportController extends Controller
{
    public function index(Request $request, GenerationReportService $reportService)
    {
        $filters = [
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];

        $report = $reportService->build($filters);

        $allMin = GenerationReading::min('bs_initial_date');
        $allMax = GenerationReading::max('bs_initial_date');

        return view('admin.reports.generation', [
            'report' => $report,
            'filters' => $filters,
            'allMin' => $allMin,
            'allMax' => $allMax,
        ]);
    }

    public function export(Request $request, GenerationReportService $reportService): StreamedResponse
    {
        $filters = [
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];

        $report = $reportService->build($filters);
        $rows = $reportService->query($filters)
            ->orderBy('bs_initial_date')
            ->orderBy('id')
            ->get();

        $spreadsheet = new Spreadsheet();

        $summary = $spreadsheet->getActiveSheet();
        $summary->setTitle('Summary');
        $k = $report['kpis'];
        $summary->fromArray([
            ['Metric', 'Value'],
            ['Total Days', $k['total_days']],
            ['Total Main (kWh)', $k['total_main_kwh']],
            ['Total Check (kWh)', $k['total_check_kwh']],
            ['Combined (kWh)', $k['total_combined_kwh']],
            ['Avg Main / Day', $k['avg_main_kwh']],
            ['Avg Check / Day', $k['avg_check_kwh']],
            ['Peak Main Date', $k['peak_main_date'] ?: '—'],
            ['Peak Main (kWh)', $k['peak_main_kwh']],
            ['Peak Check Date', $k['peak_check_date'] ?: '—'],
            ['Peak Check (kWh)', $k['peak_check_kwh']],
            ['Main-only Days', $k['main_only_days']],
            ['Check-only Days', $k['check_only_days']],
            ['Both Meter Days', $k['both_meter_days']],
            ['Filter Start', $filters['start'] ?: '—'],
            ['Filter End', $filters['end'] ?: '—'],
        ]);

        $daily = $spreadsheet->createSheet();
        $daily->setTitle('Daily');
        $daily->fromArray([['BS Date', 'Main kWh', 'Check kWh', 'Combined', 'Variance']], null, 'A1');
        $r = 2;
        foreach ($report['tables']['daily'] as $day) {
            $daily->fromArray([[
                $day['date'],
                $day['main_kwh'],
                $day['check_kwh'],
                $day['combined_kwh'],
                $day['variance_kwh'],
            ]], null, 'A' . $r);
            $r++;
        }

        $types = $spreadsheet->createSheet();
        $types->setTitle('Meter Types');
        $types->fromArray([['Type', 'Days', 'Main kWh', 'Check kWh']], null, 'A1');
        $r = 2;
        foreach ($report['tables']['meter_types'] as $type) {
            $types->fromArray([[
                $type['type'],
                $type['days'],
                $type['main_kwh'],
                $type['check_kwh'],
            ]], null, 'A' . $r);
            $r++;
        }

        $detail = $spreadsheet->createSheet();
        $detail->setTitle('Readings');
        $detail->fromArray([[
            'BS Initial', 'BS Final', 'AD Initial', 'AD Final',
            'Main Initial', 'Main Final', 'Main kWh',
            'Check Initial', 'Check Final', 'Check kWh',
            'Month', 'Company',
        ]], null, 'A1');
        $r = 2;
        foreach ($rows as $row) {
            $detail->fromArray([[
                $row->bs_initial_date,
                $row->bs_final_date,
                $row->ad_initial_date,
                $row->ad_final_date,
                $row->main_initial,
                $row->main_final,
                $row->main_generation_kwh,
                $row->check_initial,
                $row->check_final,
                $row->check_generation_kwh,
                $row->month_label,
                $row->company,
            ]], null, 'A' . $r);
            $r++;
        }

        $filename = 'generation-report-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
