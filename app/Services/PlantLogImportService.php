<?php

namespace App\Services;

use App\Exceptions\PlantLogImportException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class PlantLogImportService
{
    /**
     * Parse a monthly plant log workbook (one sheet per BS day).
     * Reads Unit-1, Unit-2, 11KV line, and meter reading hourly blocks.
     *
     * @return array{
     *   days: array<int, array<string, mixed>>,
     *   unit_hours: array<int, array<string, mixed>>,
     *   line_hours: array<int, array<string, mixed>>,
     *   meter_hours: array<int, array<string, mixed>>,
     *   unit_days: array<int, array<string, mixed>>,
     *   warnings: array<int, string>,
     *   skipped: int,
     *   company: ?string,
     *   month_label: ?string
     * }
     */
    public function parse(string $filePath, ?string $originalFilename = null): array
    {
        if (!is_readable($filePath)) {
            throw new PlantLogImportException('Uploaded file could not be read from temporary storage.');
        }

        $filesize = filesize($filePath);
        if ($filesize === false || $filesize === 0) {
            throw new PlantLogImportException('The uploaded file is empty.');
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (ReaderException $e) {
            throw new PlantLogImportException(
                'Could not open the spreadsheet. Use a valid monthly plant log Excel file.',
                [$e->getMessage()],
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new PlantLogImportException(
                'Unexpected error while opening the spreadsheet.',
                [$e->getMessage()],
                0,
                $e
            );
        }

        $days = [];
        $unitHours = [];
        $lineHours = [];
        $meterHours = [];
        $unitDays = [];
        $warnings = [];
        $skipped = 0;
        $company = null;
        $dates = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $title = trim($sheet->getTitle());
            if (strcasecmp($title, 'Sheet2') === 0) {
                $skipped++;
                continue;
            }

            $date = $this->sheetDate($sheet);
            if ($date === null) {
                $skipped++;
                $warnings[] = 'Sheet "' . $title . '" skipped — no BS date found.';
                continue;
            }

            $u1 = strtoupper((string) $this->text($sheet, 1, 3));
            $u2 = strtoupper((string) $this->text($sheet, 17, 3));
            if (!str_contains($u1, 'UNIT-1') && !str_contains($u1, 'UNIT 1')) {
                $skipped++;
                $warnings[] = "Sheet {$date} skipped — UNIT-1 LOG SHEET header not found.";
                continue;
            }

            $sheetCompany = $this->text($sheet, 1, 1);
            if ($company === null && $sheetCompany) {
                $company = $sheetCompany;
            }

            $dates[] = $date;
            $days[] = [
                'date' => $date,
                'company' => $sheetCompany,
                'month_label' => substr($date, 0, 7),
            ];

            $hourRows = $this->readHourlyRows($sheet);
            foreach ($hourRows as $hour) {
                $unitHours[] = array_merge($hour['unit1'], ['date' => $date]);
                $unitHours[] = array_merge($hour['unit2'], ['date' => $date]);
                $lineHours[] = array_merge($hour['line'], ['date' => $date]);
                $meterHours[] = array_merge($hour['meter'], ['date' => $date]);
            }

            foreach ([1, 2] as $unit) {
                $kwhKey = $unit === 1 ? 'unit1' : 'unit2';
                $kwhs = [];
                $kws = [];
                foreach ($hourRows as $hour) {
                    if ($hour[$kwhKey]['kwh'] !== null) {
                        $kwhs[] = $hour[$kwhKey]['kwh'];
                    }
                    if ($hour[$kwhKey]['kw'] !== null) {
                        $kws[] = $hour[$kwhKey]['kw'];
                    }
                }
                $first = $kwhs[0] ?? null;
                $last = $kwhs ? $kwhs[count($kwhs) - 1] : null;
                $energy = ($first !== null && $last !== null) ? round($last - $first, 3) : null;

                $unitDays[] = [
                    'unit' => $unit,
                    'date' => $date,
                    'total_running' => null,
                    'total_outage' => null,
                    'initial_reading' => null,
                    'final_reading' => null,
                    'total_generation_kwh' => null,
                    'first_kwh' => $first,
                    'last_kwh' => $last,
                    'energy_kwh' => $energy,
                    'avg_kw' => $kws ? round(array_sum($kws) / count($kws), 3) : null,
                    'hour_count' => count($hourRows),
                ];
            }
        }

        if ($days === []) {
            throw new PlantLogImportException(
                'No plant log day sheets were found.',
                [
                    'Each sheet should be named YYYY-MM-DD (BS) and contain UNIT-1 / UNIT-2 LOG SHEET.',
                    'Also expected: 11 KV LINE PANEL PARAMETERS and METER READING.',
                    $originalFilename ? "File: {$originalFilename}" : null,
                ]
            );
        }

        sort($dates);
        $monthLabel = $dates[0] . ' → ' . $dates[count($dates) - 1];

        return [
            'days' => $days,
            'unit_hours' => $unitHours,
            'line_hours' => $lineHours,
            'meter_hours' => $meterHours,
            'unit_days' => $unitDays,
            'warnings' => $warnings,
            'skipped' => $skipped,
            'company' => $company,
            'month_label' => $monthLabel,
        ];
    }

    /**
     * @return array<int, array{unit1: array, unit2: array, line: array, meter: array}>
     */
    private function readHourlyRows(Worksheet $sheet): array
    {
        $rows = [];
        $order = 0;
        for ($excelRow = 6; $excelRow <= 29; $excelRow++) {
            $hour = $this->normalizeHourLabel($this->text($sheet, 1, $excelRow));
            if ($hour === null) {
                continue;
            }
            $order++;

            $rows[] = [
                'unit1' => [
                    'unit' => 1,
                    'hour_label' => $hour,
                    'hour_order' => $order,
                    'rpm' => $this->number($sheet, 2, $excelRow),
                    'v_ry' => $this->number($sheet, 3, $excelRow),
                    'v_yb' => $this->number($sheet, 4, $excelRow),
                    'v_br' => $this->number($sheet, 5, $excelRow),
                    'i_r' => $this->number($sheet, 6, $excelRow),
                    'i_y' => $this->number($sheet, 7, $excelRow),
                    'i_b' => $this->number($sheet, 8, $excelRow),
                    'freq' => $this->number($sheet, 9, $excelRow),
                    'kvar' => $this->number($sheet, 10, $excelRow),
                    'pf' => $this->number($sheet, 11, $excelRow),
                    'kw' => $this->number($sheet, 12, $excelRow),
                    'kwh' => $this->number($sheet, 13, $excelRow),
                    'avr_v' => $this->number($sheet, 14, $excelRow),
                    'avr_i' => $this->number($sheet, 15, $excelRow),
                ],
                'unit2' => [
                    'unit' => 2,
                    'hour_label' => $hour,
                    'hour_order' => $order,
                    'rpm' => $this->number($sheet, 17, $excelRow),
                    'v_ry' => $this->number($sheet, 18, $excelRow),
                    'v_yb' => $this->number($sheet, 19, $excelRow),
                    'v_br' => $this->number($sheet, 20, $excelRow),
                    'i_r' => $this->number($sheet, 21, $excelRow),
                    'i_y' => $this->number($sheet, 22, $excelRow),
                    'i_b' => $this->number($sheet, 23, $excelRow),
                    'freq' => $this->number($sheet, 24, $excelRow),
                    'kvar' => $this->number($sheet, 25, $excelRow),
                    'pf' => $this->number($sheet, 26, $excelRow),
                    'kw' => $this->number($sheet, 27, $excelRow),
                    'kwh' => $this->number($sheet, 28, $excelRow),
                    'avr_v' => $this->number($sheet, 29, $excelRow),
                    'avr_i' => $this->number($sheet, 30, $excelRow),
                ],
                'line' => [
                    'hour_label' => $hour,
                    'hour_order' => $order,
                    'v_ry' => $this->number($sheet, 32, $excelRow),
                    'v_yb' => $this->number($sheet, 33, $excelRow),
                    'v_br' => $this->number($sheet, 34, $excelRow),
                    'i_r' => $this->number($sheet, 35, $excelRow),
                    'i_y' => $this->number($sheet, 36, $excelRow),
                    'i_b' => $this->number($sheet, 37, $excelRow),
                    'freq' => $this->number($sheet, 38, $excelRow),
                    'pf' => $this->number($sheet, 39, $excelRow),
                    'kw' => $this->number($sheet, 40, $excelRow),
                    'kvar' => $this->number($sheet, 41, $excelRow),
                    'kwh' => $this->number($sheet, 42, $excelRow),
                ],
                'meter' => [
                    'hour_label' => $this->normalizeHourLabel($this->text($sheet, 44, $excelRow)) ?: $hour,
                    'hour_order' => $order,
                    'main_meter' => $this->number($sheet, 45, $excelRow),
                    'check_meter' => $this->number($sheet, 46, $excelRow),
                    'main_diff' => $this->number($sheet, 47, $excelRow),
                    'check_diff' => $this->number($sheet, 48, $excelRow),
                ],
            ];
        }

        return $rows;
    }

    private function sheetDate(Worksheet $sheet): ?string
    {
        $title = trim($sheet->getTitle());
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $title, $m) && $this->isBsDate($m[1], $m[2], $m[3])) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }

        $raw = (string) $this->text($sheet, 1, 2);
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/', $raw, $m) && $this->isBsDate($m[3], $m[2], $m[1])) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        return null;
    }

    private function isBsDate(string $y, string $m, string $d): bool
    {
        $y = (int) $y; $m = (int) $m; $d = (int) $d;
        return $y >= 2000 && $y <= 2200 && $m >= 1 && $m <= 12 && $d >= 1 && $d <= 32;
    }

    private function normalizeHourLabel(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim(str_replace(' ', '', $value));
        if (!preg_match('/^(\d{1,2})(?::(\d{2}))?$/', $value, $m)) {
            return null;
        }
        $h = (int) $m[1];
        $min = isset($m[2]) ? (int) $m[2] : 0;

        return sprintf('%d:%02d', $h, $min);
    }

    private function text(Worksheet $sheet, int $col, int $row): ?string
    {
        $value = trim(preg_replace('/\s+/', ' ', (string) $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row)->getFormattedValue()) ?? '');

        return $value === '' ? null : $value;
    }

    private function number(Worksheet $sheet, int $col, int $row): ?float
    {
        $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row);
        $raw = $cell->getValue();
        if (is_string($raw) && str_starts_with(trim($raw), '=')) {
            try {
                $raw = $cell->getCalculatedValue();
            } catch (Throwable) {
                $raw = null;
            }
        }
        if ($raw === null || $raw === '') {
            return null;
        }
        if (is_numeric($raw)) {
            return round((float) $raw, 4);
        }
        $text = str_replace([',', ' '], '', trim((string) $cell->getFormattedValue()));

        return is_numeric($text) ? round((float) $text, 4) : null;
    }
}
