<?php

namespace App\Services;

use App\Exceptions\GeneratorLogImportException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class GeneratorLogImportService
{
    /**
     * Parse a monthly generator-meter workbook in the Unit 1 / Unit 2 reading format:
     * DATE | UNIT-1 (Initial, Final, Total Generation) | UNIT-2 (Initial, Final, Total Generation)
     *
     * @return array{
     *   days: array<int, array<string, mixed>>,
     *   events: array<int, array<string, mixed>>,
     *   warnings: array<int, string>,
     *   skipped: int,
     *   company: ?string,
     *   month_label: ?string
     * }
     *
     * @throws GeneratorLogImportException
     */
    public function parse(string $filePath, ?string $originalFilename = null): array
    {
        if (!is_readable($filePath)) {
            throw new GeneratorLogImportException('Uploaded file could not be read from temporary storage.');
        }

        $filesize = filesize($filePath);
        if ($filesize === false || $filesize === 0) {
            throw new GeneratorLogImportException('The uploaded file is empty.');
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (ReaderException $e) {
            throw new GeneratorLogImportException(
                'Could not open the spreadsheet. Use a valid Generator Meter Excel (Unit 1 / Unit 2 monthly format).',
                [$e->getMessage()],
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new GeneratorLogImportException(
                'Unexpected error while opening the spreadsheet.',
                [$e->getMessage()],
                0,
                $e
            );
        }

        $days = [];
        $warnings = [];
        $skipped = 0;
        $company = null;
        $monthLabel = null;
        $seen = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $title = trim($sheet->getTitle());
            if (preg_match('/^sheet\s*[23]$/i', $title)) {
                $skipped++;
                continue;
            }

            $layout = $this->detectLayout($sheet);
            if ($layout === null) {
                $skipped++;
                $warnings[] = 'Sheet "' . $title . '" skipped — Unit 1 / Unit 2 monthly headers not found.';
                continue;
            }

            $sheetCompany = $this->text($sheet, 1, 1);
            if ($company === null && $sheetCompany) {
                $company = $sheetCompany;
            }
            if ($monthLabel === null) {
                $monthLabel = $this->monthFromTitle($sheetCompany) ?: $this->monthFromTitle($title);
            }

            $rowCount = 0;
            $highest = min(200, (int) $sheet->getHighestRow());
            for ($row = $layout['data_start']; $row <= $highest; $row++) {
                $date = $this->parseBsDate($this->text($sheet, $layout['date_col'], $row));
                if ($date === null) {
                    continue;
                }

                foreach ([1, 2] as $unit) {
                    $cols = $layout['units'][$unit] ?? null;
                    if ($cols === null) {
                        continue;
                    }

                    $initial = $this->number($sheet, $cols['initial'], $row);
                    $final = $this->number($sheet, $cols['final'], $row);
                    $generation = $this->number($sheet, $cols['generation'], $row);
                    if ($generation === null && $initial !== null && $final !== null) {
                        $generation = round($final - $initial, 3);
                    }

                    // Skip completely empty unit rows (no readings and no generation).
                    if ($initial === null && $final === null && ($generation === null || $generation === 0.0)) {
                        continue;
                    }

                    $key = $unit . '|' . $date;
                    if (isset($seen[$key])) {
                        continue;
                    }
                    $seen[$key] = true;

                    $days[] = [
                        'generator' => $unit,
                        'date' => $date,
                        'total_running' => null,
                        'total_outage' => null,
                        'initial_reading' => $initial,
                        'final_reading' => $final,
                        'total_generation_kwh' => $generation,
                        'company' => $sheetCompany,
                        'month_label' => $monthLabel ?: substr($date, 0, 7),
                    ];
                    $rowCount++;
                }
            }

            if ($rowCount === 0) {
                $warnings[] = 'Sheet "' . $title . '" had Unit headers but no dated reading rows.';
            }
        }

        if ($days === []) {
            throw new GeneratorLogImportException(
                'No Unit 1 / Unit 2 generator meter rows were found.',
                [
                    'Expected one monthly sheet with DATE, UNIT-1 and UNIT-2 columns.',
                    'Under each unit: INITIAL READING, FINAL READING, TOTAL GENERATION (KWH).',
                    'Dates should be BS like 6/1/2083 or 2083-06-01.',
                    $originalFilename ? "File: {$originalFilename}" : null,
                ]
            );
        }

        usort($days, fn ($a, $b) => [$a['date'], $a['generator']] <=> [$b['date'], $b['generator']]);
        $dates = array_values(array_unique(array_column($days, 'date')));
        sort($dates);

        return [
            'days' => $days,
            'events' => [],
            'warnings' => $warnings,
            'skipped' => $skipped,
            'company' => $company,
            'month_label' => $monthLabel ?: ($dates[0] . ' → ' . $dates[count($dates) - 1]),
        ];
    }

    /**
     * @return array{
     *   date_col: int,
     *   data_start: int,
     *   units: array<int, array{initial:int, final:int, generation:int}>
     * }|null
     */
    private function detectLayout(Worksheet $sheet): ?array
    {
        $highestRow = min(15, (int) $sheet->getHighestRow());
        $highestCol = min(30, Coordinate::columnIndexFromString($sheet->getHighestColumn() ?: 'A'));

        $unitStarts = [];
        $dateCol = null;
        $headerRow = null;

        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 1; $col <= $highestCol; $col++) {
                $label = $this->norm($this->text($sheet, $col, $row));
                if ($label === 'date') {
                    $dateCol = $col;
                    $headerRow = $row;
                }
                if (preg_match('/^(unit|unut)[-_ ]?1$/', $label) || $label === 'unit1' || $label === 'unut1') {
                    $unitStarts[1] = $col;
                    $headerRow = $headerRow ?: $row;
                }
                if (preg_match('/^(unit|unut)[-_ ]?2$/', $label) || $label === 'unit2' || $label === 'unut2') {
                    $unitStarts[2] = $col;
                    $headerRow = $headerRow ?: $row;
                }
            }
        }

        if ($dateCol === null || $unitStarts === [] || $headerRow === null) {
            return null;
        }

        $subHeaderRow = $headerRow + 1;
        $units = [];
        foreach ($unitStarts as $unit => $startCol) {
            $endCol = ($unit === 1 && isset($unitStarts[2]))
                ? $unitStarts[2] - 1
                : min($startCol + 8, $highestCol);

            $map = ['initial' => null, 'final' => null, 'generation' => null];
            for ($col = $startCol; $col <= $endCol; $col++) {
                $label = $this->norm($this->text($sheet, $col, $subHeaderRow));
                if ($label === '') {
                    continue;
                }
                if (str_contains($label, 'initial') && $map['initial'] === null) {
                    $map['initial'] = $col;
                } elseif (str_contains($label, 'final') && $map['final'] === null) {
                    $map['final'] = $col;
                } elseif (
                    (str_contains($label, 'total') && str_contains($label, 'generation'))
                    || (str_contains($label, 'generation') && str_contains($label, 'kwh'))
                    || $label === 'totalgeneration'
                ) {
                    $map['generation'] = $col;
                }
            }

            // Fallback to the sample layout offsets if sub-headers are merged oddly.
            if ($map['initial'] === null) {
                $map['initial'] = $startCol;
            }
            if ($map['final'] === null) {
                $map['final'] = $startCol + 2;
            }
            if ($map['generation'] === null) {
                $map['generation'] = $startCol + 4;
            }

            $units[$unit] = $map;
        }

        return [
            'date_col' => $dateCol,
            'data_start' => $subHeaderRow + 1,
            'units' => $units,
        ];
    }

    private function monthFromTitle(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (preg_match('/\((\d{4})-(\d{2})\)/', $value, $m)) {
            return $m[1] . '-' . $m[2];
        }
        if (preg_match('/\b(\d{4})-(\d{2})\b/', $value, $m)) {
            return $m[1] . '-' . $m[2];
        }

        return null;
    }

    private function parseBsDate(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value, $m) && $this->isBsDate($m[1], $m[2], $m[3])) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $m)) {
            // Prefer M/D/YYYY (sample sheet uses 6/1/2083), then D/M/YYYY.
            if ($this->isBsDate($m[3], $m[1], $m[2])) {
                return sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]);
            }
            if ($this->isBsDate($m[3], $m[2], $m[1])) {
                return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }
        }

        return null;
    }

    private function isBsDate(string|int $y, string|int $m, string|int $d): bool
    {
        $y = (int) $y;
        $m = (int) $m;
        $d = (int) $d;

        return $y >= 2000 && $y <= 2200 && $m >= 1 && $m <= 12 && $d >= 1 && $d <= 32;
    }

    private function norm(?string $value): string
    {
        return strtolower(preg_replace('/\s+/', '', (string) $value) ?? '');
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
