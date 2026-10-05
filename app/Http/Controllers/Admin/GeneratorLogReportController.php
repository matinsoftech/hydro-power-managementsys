<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratorDailyLog;
use App\Services\GeneratorLogReportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GeneratorLogReportController extends Controller
{
    public function index(Request $request, GeneratorLogReportService $reportService)
    {
        $filters = [
            'generator' => $request->input('generator'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];

        $report = $reportService->build($filters);

        return view('admin.reports.generator', [
            'report' => $report,
            'filters' => $filters,
            'allMin' => GeneratorDailyLog::min('date'),
            'allMax' => GeneratorDailyLog::max('date'),
        ]);
    }

    public function export(Request $request, GeneratorLogReportService $reportService): StreamedResponse
    {
        $filters = [
            'generator' => $request->input('generator'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];

        $report = $reportService->build($filters);
        $spreadsheet = new Spreadsheet();
        $summary = $spreadsheet->getActiveSheet();
        $summary->setTitle('Summary');
        $k = $report['kpis'];
        $summary->fromArray([
            ['Metric', 'Value'],
            ['Days', $k['days']],
            ['Outage events', $k['events']],
            ['Event outage (hrs)', $k['outage']],
            ['Total running (hrs)', $k['running']],
            ['Total generation (kWh)', $k['generation_kwh']],
            ['Generator 1 events', $k['generators'][1]['events']],
            ['Generator 1 outage', $k['generators'][1]['outage']],
            ['Generator 1 generation (kWh)', $k['generators'][1]['generation_kwh']],
            ['Generator 2 events', $k['generators'][2]['events']],
            ['Generator 2 outage', $k['generators'][2]['outage']],
            ['Generator 2 generation (kWh)', $k['generators'][2]['generation_kwh']],
            ['Filter generator', $filters['generator'] ? 'Generator ' . $filters['generator'] : 'All'],
            ['Filter start', $filters['start'] ?: '—'],
            ['Filter end', $filters['end'] ?: '—'],
        ]);

        $daily = $spreadsheet->createSheet();
        $daily->setTitle('Daily');
        $daily->fromArray([['BS Date', 'Generator', 'Total Running', 'Total Outage', 'Initial', 'Final', 'Total Generation (kWh)']], null, 'A1');
        $row = 2;
        foreach ($report['tables']['days'] as $day) {
            $daily->fromArray([[
                $day->date,
                'Generator ' . $day->generator,
                $day->total_running,
                $day->total_outage,
                $day->initial_reading,
                $day->final_reading,
                $day->total_generation_kwh,
            ]], null, 'A' . $row);
            $row++;
        }

        $events = $spreadsheet->createSheet();
        $events->setTitle('Outages');
        $events->fromArray([['BS Date', 'Generator', 'S.No', 'To', 'Resume', 'Synch', 'Total Outage', 'Reason']], null, 'A1');
        $row = 2;
        foreach ($report['tables']['events'] as $event) {
            $events->fromArray([[
                $event->date,
                'Generator ' . $event->generator,
                $event->serial,
                $event->trip_to,
                $event->resume_hrs,
                $event->synch_hrs,
                $event->outage_hrs,
                $event->reason,
            ]], null, 'A' . $row);
            $row++;
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'generator-meter-report.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
