<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogDay;
use App\Services\PlantLogReportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlantLogReportController extends Controller
{
    public function index(Request $request, PlantLogReportService $reportService)
    {
        $filters = $this->filters($request);

        return view('admin.reports.log', [
            'report' => $reportService->build($filters),
            'filters' => $filters,
            'sections' => PlantLogReportService::sections(),
            'allMin' => LogDay::min('date'),
            'allMax' => LogDay::max('date'),
        ]);
    }

    public function export(Request $request, PlantLogReportService $reportService): StreamedResponse
    {
        $filters = $this->filters($request);
        // Export all matching rows for the section (no page slice).
        $report = $reportService->build($filters, PHP_INT_MAX);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($report['section_label'], 0, 31));

        $headers = array_column($report['columns'], 'label');
        $sheet->fromArray([$headers], null, 'A1');

        $r = 2;
        foreach ($report['rows'] as $row) {
            $sheet->fromArray([$this->exportRow($report['section'], $row)], null, 'A' . $r++);
        }

        $meta = $spreadsheet->createSheet();
        $meta->setTitle('Filter');
        $meta->fromArray([
            ['Metric', 'Value'],
            ['Section', $report['section_label']],
            ['Unit', !empty($filters['unit']) ? 'Unit ' . $filters['unit'] : 'All'],
            ['Start', $filters['start'] ?: '—'],
            ['End', $filters['end'] ?: '—'],
            ['Days', $report['kpis']['days'] ?? 0],
            ['Rows', $report['kpis']['hour_rows'] ?? ($report['rows']->count() ?? 0)],
        ]);

        $filename = 'log-report-' . str_replace('_', '-', $report['section']) . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** @return array{section: string, unit: mixed, start: mixed, end: mixed} */
    private function filters(Request $request): array
    {
        $section = PlantLogReportService::normalizeSection($request->input('section'));
        $unit = $request->input('unit');
        if (!PlantLogReportService::usesUnitFilter($section)) {
            $unit = null;
        }

        return [
            'section' => $section,
            'unit' => $unit,
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];
    }

    /** @param object|array<string, mixed> $row */
    private function exportRow(string $section, $row): array
    {
        return match ($section) {
            PlantLogReportService::SECTION_GENERATOR_VOLTAGE => [
                $row->date,
                'Unit ' . $row->unit,
                $row->hour_label,
                $row->v_ry,
                $row->v_yb,
                $row->v_br,
            ],
            PlantLogReportService::SECTION_GENERATOR_CURRENT => [
                $row->date,
                'Unit ' . $row->unit,
                $row->hour_label,
                $row->i_r,
                $row->i_y,
                $row->i_b,
            ],
            PlantLogReportService::SECTION_LINE_VOLTAGE => [
                $row->date,
                $row->hour_label,
                $row->v_ry,
                $row->v_yb,
                $row->v_br,
            ],
            PlantLogReportService::SECTION_LINE_CURRENT => [
                $row->date,
                $row->hour_label,
                $row->i_r,
                $row->i_y,
                $row->i_b,
            ],
            PlantLogReportService::SECTION_LINE_PANEL => [
                $row->date,
                $row->hour_label,
                $row->v_ry,
                $row->v_yb,
                $row->v_br,
                $row->i_r,
                $row->i_y,
                $row->i_b,
                $row->freq,
                $row->pf,
                $row->kw,
                $row->kvar,
                $row->kwh,
            ],
            PlantLogReportService::SECTION_METER => [
                $row->date,
                $row->hour_label,
                $row->main_meter,
                $row->check_meter,
                $row->main_diff,
                $row->check_diff,
            ],
            default => [
                $row->date,
                'Unit ' . $row->unit,
                $row->hour_count,
                $row->avg_kw,
                $row->energy_kwh,
                $row->first_kwh,
                $row->last_kwh,
            ],
        };
    }
}
