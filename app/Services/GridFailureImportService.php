<?php

namespace App\Services;

use App\Exceptions\GridFailureImportException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use Throwable;

class GridFailureImportService
{
    private const MAX_ERRORS = 50;

    /**
     * Parse and validate GRID FAIL side-by-side Excel (Unit 1 | Unit 2).
     *
     * @return array{
     *   rows: array<int, array<string, mixed>>,
     *   warnings: array<int, string>,
     *   skipped: int,
     *   company: ?string,
     *   month_label: ?string
     * }
     *
     * @throws GridFailureImportException
     */
    public function parse(string $filePath, ?string $originalFilename = null): array
    {
        if (!is_readable($filePath)) {
            throw new GridFailureImportException('Uploaded file could not be read from temporary storage.');
        }

        $filesize = filesize($filePath);
        if ($filesize === false || $filesize === 0) {
            throw new GridFailureImportException('The uploaded file is empty.');
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (ReaderException $e) {
            throw new GridFailureImportException(
                'Could not open the spreadsheet. Use a valid .xlsx / .xls GRID FAIL file.',
                [$e->getMessage()],
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new GridFailureImportException(
                'Unexpected error while opening the spreadsheet.',
                [$e->getMessage()],
                0,
                $e
            );
        }

        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = (int) $sheet->getHighestDataRow();

        if ($highestRow < 4) {
            throw new GridFailureImportException(
                'Sheet has too few rows. Expected header rows 1–3 and data starting at row 4.',
                ["Found only {$highestRow} row(s)."]
            );
        }

        $this->assertExpectedHeaders($sheet);

        $company = $this->cell($sheet, 1, 1);
        $monthLabel = $this->cell($sheet, 2, 1);

        $rows = [];
        $errors = [];
        $warnings = [];
        $skipped = 0;
        $lastDateUnit1 = null;
        $lastDateUnit2 = null;

        for ($r = 4; $r <= $highestRow; $r++) {
            // Unit 1: A–F
            $unit1 = $this->readUnitBlock($sheet, $r, 1, $lastDateUnit1, $errors, $warnings, $skipped);
            if ($unit1['date']) {
                $lastDateUnit1 = $unit1['date'];
            }
            if ($unit1['row']) {
                $unit1['row']['month_label'] = $monthLabel;
                $unit1['row']['company'] = $company;
                $rows[] = $unit1['row'];
            }

            // Unit 2: H–M
            $unit2 = $this->readUnitBlock($sheet, $r, 2, $lastDateUnit2, $errors, $warnings, $skipped);
            if ($unit2['date']) {
                $lastDateUnit2 = $unit2['date'];
            }
            if ($unit2['row']) {
                $unit2['row']['month_label'] = $monthLabel;
                $unit2['row']['company'] = $company;
                $rows[] = $unit2['row'];
            }

            if (count($errors) >= self::MAX_ERRORS) {
                $errors[] = 'More errors found — stopped listing after ' . self::MAX_ERRORS . '. Fix the file and re-upload.';
                break;
            }
        }

        if (!empty($errors)) {
            throw new GridFailureImportException(
                'Import blocked: ' . count($errors) . ' validation error(s) found. No records were saved.',
                $errors
            );
        }

        if (empty($rows)) {
            throw new GridFailureImportException(
                'No failure records found in the file.',
                [
                    'Check that data starts at row 4.',
                    'Each event needs From / To / Synch / Reason, and a Date (on the row or carried from above).',
                    'TOTAL rows are skipped automatically.',
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
        $headerRow = 3;
        $u1Date = strtoupper((string) ($this->cell($sheet, $headerRow, 1) ?? ''));
        $u1From = strtoupper((string) ($this->cell($sheet, $headerRow, 2) ?? ''));
        $u2Date = strtoupper((string) ($this->cell($sheet, $headerRow, 8) ?? ''));
        $u2From = strtoupper((string) ($this->cell($sheet, $headerRow, 9) ?? ''));

        $problems = [];

        if (!str_contains($u1Date, 'DATE')) {
            $problems[] = 'Unit 1 header (column A, row 3) should contain "DATE". Found: "' . ($this->cell($sheet, $headerRow, 1) ?? '') . '"';
        }
        if (!str_contains($u1From, 'FROM')) {
            $problems[] = 'Unit 1 header (column B, row 3) should contain "FROM". Found: "' . ($this->cell($sheet, $headerRow, 2) ?? '') . '"';
        }
        if (!str_contains($u2Date, 'DATE')) {
            $problems[] = 'Unit 2 header (column H, row 3) should contain "DATE". Found: "' . ($this->cell($sheet, $headerRow, 8) ?? '') . '"';
        }
        if (!str_contains($u2From, 'FROM')) {
            $problems[] = 'Unit 2 header (column I, row 3) should contain "FROM". Found: "' . ($this->cell($sheet, $headerRow, 9) ?? '') . '"';
        }

        if (!empty($problems)) {
            throw new GridFailureImportException(
                'File layout does not match GRID FAIL format (Unit 1 left, Unit 2 right).',
                $problems
            );
        }
    }

    /**
     * @param  array<int, string>  $errors
     * @param  array<int, string>  $warnings
     * @return array{date: ?string, row: ?array<string, mixed>}
     */
    private function readUnitBlock(
        $sheet,
        int $excelRow,
        int $unit,
        ?string $lastDate,
        array &$errors,
        array &$warnings,
        int &$skipped
    ): array {
        $colOffset = $unit === 1 ? 0 : 7; // Unit1: 1-6, Unit2: 8-13

        $rawDate = $this->cell($sheet, $excelRow, 1 + $colOffset);
        $rawFrom = $this->timeCell($sheet, $excelRow, 2 + $colOffset);
        $rawTo = $this->timeCell($sheet, $excelRow, 3 + $colOffset);
        $rawSynch = $this->timeCell($sheet, $excelRow, 4 + $colOffset);
        $rawDuration = $this->timeCell($sheet, $excelRow, 5 + $colOffset);
        $reason = $this->normalizeText($this->cell($sheet, $excelRow, 6 + $colOffset));

        $label = "Row {$excelRow} / Unit {$unit}";

        if ($this->isTotalLabel($this->stringify($rawFrom)) || $this->isTotalLabel($rawDate)) {
            $skipped++;
            return ['date' => null, 'row' => null];
        }

        $date = null;
        $dateError = null;
        if ($rawDate !== null && !$this->isTotalLabel($rawDate)) {
            [$date, $dateError] = $this->parseDateStrict($rawDate);
            if ($dateError) {
                $errors[] = "{$label}: invalid Date \"{$rawDate}\" — use YYYY-MM-DD (e.g. 2083-05-02).";
            }
        }

        $effectiveDate = $date ?: $lastDate;

        [$from, $fromErr] = $this->parseTimeStrict($rawFrom, 'From (Hrs)');
        [$to, $toErr] = $this->parseTimeStrict($rawTo, 'To (Hrs)');
        [$synch, $synchErr] = $this->parseTimeStrict($rawSynch, 'Synch (Hrs)');
        [$duration, $durationErr] = $this->parseTimeStrict($rawDuration, 'Duration (Hrs)');

        foreach ([$fromErr, $toErr, $synchErr, $durationErr] as $fieldError) {
            if ($fieldError) {
                $errors[] = "{$label}: {$fieldError}";
            }
        }

        $isEvent = $from !== null || $to !== null || $synch !== null || $reason !== null;

        if (!$isEvent) {
            // Empty shell / date-only spacer
            if ($date) {
                return ['date' => $date, 'row' => null];
            }
            return ['date' => null, 'row' => null];
        }

        if (!$effectiveDate) {
            $errors[] = "{$label}: event found but Date is missing (set Date on this row or a row above).";
            return ['date' => $date, 'row' => null];
        }

        if ($reason === null) {
            $warnings[] = "{$label}: Reason/Remarks is empty.";
        }

        if ($from === null && $synch === null) {
            $warnings[] = "{$label}: both From and Synch are empty.";
        }

        return [
            'date' => $date,
            'row' => [
                'unit' => $unit,
                'date' => $effectiveDate,
                'from_hrs' => $from,
                'to_hrs' => $to,
                'synch_hrs' => $synch,
                'duration_hrs' => $duration,
                'reason' => $reason,
            ],
        ];
    }

    /**
     * @return array{0: ?string, 1: ?string} [value, error]
     */
    private function parseDateStrict(string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            return [null, null];
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return [$value, null];
        }
        // Allow slightly loose formats like 2083/05/02
        if (preg_match('/^(\d{4})[\/.\-](\d{1,2})[\/.\-](\d{1,2})$/', $value, $m)) {
            return [sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]), null];
        }

        return [null, 'invalid'];
    }

    /**
     * @return array{0: ?string, 1: ?string} [normalized, errorMessage]
     */
    private function parseTimeStrict($value, string $field): array
    {
        if ($value === null || $value === '') {
            return [null, null];
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '' || $this->isTotalLabel($value) || str_starts_with($value, '=')) {
                return [null, null];
            }
            if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value)) {
                $parts = explode(':', $value);
                $h = (int) $parts[0];
                $m = (int) ($parts[1] ?? 0);
                $s = (int) ($parts[2] ?? 0);
                if ($m > 59 || $s > 59 || $h > 999) {
                    return [null, "{$field} value \"{$value}\" is out of range."];
                }
                return [sprintf('%d:%02d:%02d', $h, $m, $s), null];
            }

            return [null, "{$field} value \"{$value}\" is not a valid time (expected H:MM or H:MM:SS)."];
        }

        if (is_numeric($value)) {
            $seconds = (int) round(((float) $value) * 86400);
            if ($seconds < 0) {
                $seconds = abs($seconds);
            }
            $h = intdiv($seconds, 3600);
            $m = intdiv($seconds % 3600, 60);
            $s = $seconds % 60;
            return [sprintf('%d:%02d:%02d', $h, $m, $s), null];
        }

        return [null, "{$field} has an unsupported value type."];
    }

    private function stringify($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_string($value) || is_numeric($value)) {
            $s = trim((string) $value);
            return $s === '' ? null : $s;
        }
        return null;
    }

    private function cell($sheet, int $row, int $col): ?string
    {
        try {
            $value = $sheet->getCell([$col, $row])->getFormattedValue();
        } catch (Throwable $e) {
            return null;
        }
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function timeCell($sheet, int $row, int $col)
    {
        try {
            $cell = $sheet->getCell([$col, $row]);
        } catch (Throwable $e) {
            return null;
        }

        try {
            $calculated = $cell->getCalculatedValue();
        } catch (Throwable $e) {
            $calculated = null;
        }

        if (is_numeric($calculated)) {
            return (float) $calculated;
        }

        if (is_string($calculated) && $calculated !== '' && !str_starts_with($calculated, '=')) {
            return $calculated;
        }

        try {
            $formatted = $cell->getFormattedValue();
        } catch (Throwable $e) {
            $formatted = null;
        }

        if ($formatted !== null && trim((string) $formatted) !== '' && !str_starts_with(trim((string) $formatted), '=')) {
            return trim((string) $formatted);
        }

        try {
            $raw = $cell->getValue();
        } catch (Throwable $e) {
            return null;
        }

        if (is_string($raw) && str_starts_with($raw, '=')) {
            return null;
        }

        return $raw;
    }

    private function normalizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim(preg_replace('/\s+/', ' ', $value));
        return $value === '' ? null : $value;
    }

    private function isTotalLabel(?string $value): bool
    {
        if ($value === null) {
            return false;
        }
        return strcasecmp(trim($value), 'TOTAL') === 0;
    }
}
