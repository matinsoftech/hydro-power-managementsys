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
            'unit' => $request->input('unit', $request->input('generator')),
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
            'unit' => $request->input('unit', $request->input('generator')),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];

        $report = $reportService->build($filters);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Generator meter');

        $month = ($report['date_min'] && $report['date_max'])
            ? substr((string) $report['date_min'], 0, 7)
            : '';
        $sheet->setCellValue('A1', 'SAITIKHOLA SMALL HYDROPOWER PROJECT' . ($month ? '(' . $month . ')' : ''));
        $sheet->setCellValue('A3', 'DATE');
        $sheet->setCellValue('B3', 'UNIT-1');
        $sheet->setCellValue('J3', 'UNIT-2');
        $sheet->fromArray(['INITIAL READING', null, 'FINAL READING', null, 'TOTAL GENERATION (KWH)'], null, 'B4');
        $sheet->fromArray(['INITIAL READING', null, 'FINAL READING', null, 'TOTAL GENERATION (KWH)'], null, 'J4');

        $unit = $filters['unit'] ?? null;
        $row = 5;
        foreach ($report['tables']['combined'] as $day) {
            $sheet->setCellValue('A' . $row, $day['date']);
            if ($unit === null || (string) $unit === '1') {
                $sheet->setCellValue('B' . $row, $day['u1_initial']);
                $sheet->setCellValue('D' . $row, $day['u1_final']);
                $sheet->setCellValue('F' . $row, $day['u1_generation']);
            }
            if ($unit === null || (string) $unit === '2') {
                $sheet->setCellValue('J' . $row, $day['u2_initial']);
                $sheet->setCellValue('L' . $row, $day['u2_final']);
                $sheet->setCellValue('N' . $row, $day['u2_generation']);
            }
            $row++;
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'generator-meter-report.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
