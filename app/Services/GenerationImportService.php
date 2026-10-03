<?php

namespace App\Services;

use App\Exceptions\GenerationImportException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class GenerationImportService
{
    private const MAX_ERRORS = 50;
    private const DATA_START_ROW = 6;

    /**
     * Parse monthly 24-hours generation Excel (AD/BS dates + Main/Check meters).
     *
     * @return array{
     *   rows: array<int, array<string, mixed>>,
     *   warnings: array<int, string>,
     *   skipped: int,
     *   company: ?string,
     *   month_label: ?string
     * }
     *
     * @throws GenerationImportException
     */
    public function parse(string $filePath, ?string $originalFilename = null): array
    {
        if (!is_readable($filePath)) {
            throw new GenerationImportException('Uploaded file could not be read from temporary storage.');
        }

        $filesize = filesize($filePath);
        if ($filesize === false || $filesize === 0) {
            throw new GenerationImportException('The uploaded file is empty.');
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (ReaderException $e) {
            throw new GenerationImportException(
                'Could not open the spreadsheet. Use a valid .xlsx / .xls generation file.',
                [$e->getMessage()],
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new GenerationImportException(
                'Unexpected error while opening the spreadsheet.',
                [$e->getMessage()],
                0,
                $e
            );
        }

        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = (int) $sheet->getHighestDataRow();

        if ($highestRow < self::DATA_START_ROW) {
            throw new GenerationImportException(
                'Sheet has too few rows. Expected headers in rows 1–5 and data starting at row 6.',
                ["Found only {$highestRow} row(s)."]
            );
        }

        $this->assertExpectedHeaders($sheet);

        $company = $this->cell($sheet, 1, 2);
        $monthLabel = $this->cell($sheet, 2, 2);

        $rows = [];
        $errors = [];
        $warnings = [];
        $skipped = 0;

        for ($r = self::DATA_START_ROW; $r <= $highestRow; $r++) {
            $label = "Row {$r}";

            $rawAdInitial = $this->cellRaw($sheet, $r, 2);
            $rawAdFinal = $this->cellRaw($sheet, $r, 3);
            $rawBsInitial = $this->cellRaw($sheet, $r, 4);
            $rawBsFinal = $this->cellRaw($sheet, $r, 5);
            $fmtAdInitial = $this->cellFormatted($sheet, $r, 2);
            $fmtAdFinal = $this->cellFormatted($sheet, $r, 3);
            $fmtBsInitial = $this->cellFormatted($sheet, $r, 4);
            $fmtBsFinal = $this->cellFormatted($sheet, $r, 5);
            $rawMainInitial = $this->cellRaw($sheet, $r, 6);
            $rawMainFinal = $this->cellRaw($sheet, $r, 7);
            $rawMainGen = $this->cellRaw($sheet, $r, 8);
            $rawCheckInitial = $this->cellRaw($sheet, $r, 9);
            $rawCheckFinal = $this->cellRaw($sheet, $r, 10);
            $rawCheckGen = $this->cellRaw($sheet, $r, 11);

            $adInitialText = $fmtAdInitial !== '' ? $fmtAdInitial : $this->stringify($rawAdInitial);
            $bsInitialText = $fmtBsInitial !== '' ? $fmtBsInitial : $this->stringify($rawBsInitial);

            if ($this->isTotalLabel($adInitialText) || $this->isTotalLabel($bsInitialText)) {
                $skipped++;
                continue;
            }

            $adInitial = $this->parseAdDate($rawAdInitial, $fmtAdInitial);
            $adFinal = $this->parseAdDate($rawAdFinal, $fmtAdFinal);
            [$bsInitial, $bsInitialErr] = $this->parseBsDate($rawBsInitial, $fmtBsInitial);
            [$bsFinal, $bsFinalErr] = $this->parseBsDate($rawBsFinal, $fmtBsFinal);

            $mainInitial = $this->parseNumber($rawMainInitial);
            $mainFinal = $this->parseNumber($rawMainFinal);
            $mainGen = $this->parseNumber($rawMainGen);
            $checkInitial = $this->parseNumber($rawCheckInitial);
            $checkFinal = $this->parseNumber($rawCheckFinal);
            $checkGen = $this->parseNumber($rawCheckGen);

            $hasAnyValue = $adInitial || $adFinal || $bsInitial || $bsFinal
                || $mainInitial !== null || $mainFinal !== null || $mainGen !== null
                || $checkInitial !== null || $checkFinal !== null || $checkGen !== null;

            if (!$hasAnyValue) {
                continue;
            }

            if ($bsInitialErr) {
                $errors[] = "{$label}: invalid B.S. initial date — {$bsInitialErr}";
            }
            if ($bsFinalErr) {
                $errors[] = "{$label}: invalid B.S. final date — {$bsFinalErr}";
            }

            if (!$bsInitial) {
                $errors[] = "{$label}: B.S. initial date is required (column D).";
            }

            $hasMain = $mainInitial !== null || $mainFinal !== null || ($mainGen !== null && $mainGen != 0.0);
            $hasCheck = $checkInitial !== null || $checkFinal !== null || ($checkGen !== null && $checkGen != 0.0);

            if (!$hasMain && !$hasCheck) {
                $warnings[] = "{$label}: no Main or Check meter values found.";
            }

            if ($hasMain && $mainInitial !== null && $mainFinal !== null && $mainFinal < $mainInitial) {
                $warnings[] = "{$label}: Main final reading is less than initial.";
            }
            if ($hasCheck && $checkInitial !== null && $checkFinal !== null && $checkFinal < $checkInitial) {
                $warnings[] = "{$label}: Check final reading is less than initial.";
            }

            if ($bsInitial) {
                $rows[] = [
                    'ad_initial_date' => $adInitial,
                    'ad_final_date' => $adFinal,
                    'bs_initial_date' => $bsInitial,
                    'bs_final_date' => $bsFinal,
                    'main_initial' => $mainInitial,
                    'main_final' => $mainFinal,
                    'main_generation_kwh' => $mainGen,
                    'check_initial' => $checkInitial,
                    'check_final' => $checkFinal,
                    'check_generation_kwh' => $checkGen,
                    'month_label' => $monthLabel,
                    'company' => $company,
                ];
            }

            if (count($errors) >= self::MAX_ERRORS) {
                $errors[] = 'More errors found — stopped listing after ' . self::MAX_ERRORS . '. Fix the file and re-upload.';
                break;
            }
        }

        if (!empty($errors)) {
            throw new GenerationImportException(
                'Import blocked: ' . count($errors) . ' validation error(s) found. No records were saved.',
                $errors
            );
        }

        if (empty($rows)) {
            throw new GenerationImportException(
                'No generation records found in the file.',
                [
                    'Check that daily rows start at row 6.',
                    'Each day needs a B.S. initial date (column D) and meter readings.',
                    'TOTAL GENERATION rows are skipped automatically.',
                    $originalFilename ? "File: {$originalFilename}" : null,
                ]
            );
        }

        return [
            'rows' => $rows,
            'warnings' => $warnings,
            'skipped' => $skipped,
            'company' => $company,
            'month_label' => $monthLabel,
        ];
    }

    private function assertExpectedHeaders($sheet): void
    {
        $problems = [];

        $r3b = strtoupper((string) ($this->cell($sheet, 3, 2) ?? ''));
        $r3f = strtoupper((string) ($this->cell($sheet, 3, 6) ?? ''));
        $r3i = strtoupper((string) ($this->cell($sheet, 3, 9) ?? ''));
        $r5b = strtoupper((string) ($this->cell($sheet, 5, 2) ?? ''));
        $r5f = strtoupper((string) ($this->cell($sheet, 5, 6) ?? ''));
        $r5h = strtoupper((string) ($this->cell($sheet, 5, 8) ?? ''));

        if (!str_contains($r3b, 'DATE')) {
            $problems[] = 'Row 3 column B should contain "DATE". Found: "' . ($this->cell($sheet, 3, 2) ?? '') . '"';
        }
        if (!str_contains($r3f, 'MAIN')) {
            $problems[] = 'Row 3 column F should contain "MAIN METER". Found: "' . ($this->cell($sheet, 3, 6) ?? '') . '"';
        }
        if (!str_contains($r3i, 'CHECK')) {
            $problems[] = 'Row 3 column I should contain "CHECK METER". Found: "' . ($this->cell($sheet, 3, 9) ?? '') . '"';
        }
        if (!str_contains($r5b, 'INITIAL') && !str_contains($r5b, '12AM')) {
            $problems[] = 'Row 5 column B should look like "INITIAL (12AM)". Found: "' . ($this->cell($sheet, 5, 2) ?? '') . '"';
        }
        if (!str_contains($r5f, 'INITIAL') && !str_contains($r5f, 'READING')) {
            $problems[] = 'Row 5 column F should look like "INITIAL READING". Found: "' . ($this->cell($sheet, 5, 6) ?? '') . '"';
        }
        if (!str_contains($r5h, 'GENERATION') && !str_contains($r5h, 'KWH')) {
            $problems[] = 'Row 5 column H should look like "GENERATION (KWH)". Found: "' . ($this->cell($sheet, 5, 8) ?? '') . '"';
        }

        if (!empty($problems)) {
            throw new GenerationImportException(
                'This does not look like a 24 HOURS GENERATION Excel sheet.',
                array_merge($problems, [
                    'Expected layout: company/month title, DATE + MAIN METER + CHECK METER headers, then daily rows from row 6.',
                ])
            );
        }
    }

    private function parseAdDate(mixed $value, ?string $formatted = null): ?string
    {
        // Prefer display text when Excel stores a serial with custom date format.
        if ($formatted !== null && $formatted !== '') {
            $fromFmt = $this->parseAdDateText($formatted);
            if ($fromFmt) {
                return $fromFmt;
            }
        }

        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (Throwable) {
                // fall through
            }
        }

        return $this->parseAdDateText((string) $value);
    }

    private function parseAdDateText(string $text): ?string
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        // e.g. 17-Sep-26 / 17-Sep-2026
        if (preg_match('/^(\d{1,2})[-\/\s]([A-Za-z]{3})[-\/\s](\d{2,4})$/', $text, $m)) {
            $year = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }
            $ts = strtotime(sprintf('%d-%s-%02d', $year, $m[2], (int) $m[1]));
            if ($ts !== false) {
                return date('Y-m-d', $ts);
            }
        }

        if (preg_match('/^(\d{4})[\/.\-](\d{1,2})[\/.\-](\d{1,2})$/', $text, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]);
        }

        $ts = strtotime($text);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        return $text;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function parseBsDate(mixed $value, ?string $formatted = null): array
    {
        // Nepali BS dates are often stored as Excel serials with a custom YYYY-MM-DD format.
        // Always prefer the formatted display text for BS columns.
        $candidates = array_values(array_filter([
            $formatted,
            is_string($value) ? $value : null,
        ], fn ($v) => $v !== null && trim((string) $v) !== ''));

        foreach ($candidates as $candidate) {
            $text = trim((string) $candidate);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $text)) {
                return [$text, null];
            }
            if (preg_match('/^(\d{4})[\/.\-](\d{1,2})[\/.\-](\d{1,2})$/', $text, $m)) {
                return [sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]), null];
            }
        }

        if ($value === null || $value === '') {
            return [null, null];
        }

        $shown = $formatted ?: $this->stringify($value);

        return [null, "use YYYY-MM-DD (e.g. 2083-05-01). Found \"{$shown}\""];
    }

    private function parseNumber(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $text = trim(str_replace([',', ' '], '', (string) $value));
        if ($text === '' || !is_numeric($text)) {
            return null;
        }

        return (float) $text;
    }

    private function isTotalLabel(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return str_contains(strtoupper($value), 'TOTAL');
    }

    private function cell($sheet, int $row, int $col): ?string
    {
        $raw = $this->cellRaw($sheet, $row, $col);
        $text = $this->stringify($raw);

        return $text === '' ? null : $text;
    }

    private function cellRaw($sheet, int $row, int $col): mixed
    {
        return $sheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
    }

    private function cellFormatted($sheet, int $row, int $col): string
    {
        $fmt = $sheet->getCellByColumnAndRow($col, $row)->getFormattedValue();

        return trim((string) ($fmt ?? ''));
    }

    private function stringify(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_float($value) || is_int($value)) {
            return (string) $value;
        }

        return trim((string) $value);
    }
}
