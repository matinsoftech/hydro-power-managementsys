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
     * Parse a monthly generator log sheet.
     * Each day sheet has Generator 1 and Generator 2 blocks at the bottom,
     * plus the totals row (running, outage, initial, final, generation).
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
                'Could not open the spreadsheet. Use a valid .xlsx / .xls Generator Meter Import file.',
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
        $events = [];
        $warnings = [];
        $skipped = 0;
        $company = null;
        $dates = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $blocks = $this->findGeneratorBlocks($sheet);
            if (!isset($blocks[1]) && !isset($blocks[2])) {
                $skipped++;
                $warnings[] = 'Sheet "' . $sheet->getTitle() . '" skipped — no Generator 1 or Generator 2 block.';
                continue;
            }

            $date = $this->sheetDate($sheet);
            if ($date === null) {
                throw new GeneratorLogImportException(
                    'Could not read the BS date for sheet "' . $sheet->getTitle() . '".',
                    ['Name the sheet YYYY-MM-DD (e.g. 2083-05-01), or put DATE:DD/MM/YYYY in cell A2.']
                );
            }

            $dates[] = $date;
            $sheetCompany = $this->text($sheet, 1, 1);
            if ($company === null && $sheetCompany) {
                $company = $sheetCompany;
            }

            foreach ([1, 2] as $generator) {
                if (!isset($blocks[$generator])) {
                    $warnings[] = "Sheet {$date}: Generator {$generator} block was not found.";
                    continue;
                }

                [$col, $row] = $blocks[$generator];
                $summary = $this->readSummary($sheet, $col, $row);
                $days[] = [
                    'generator' => $generator,
                    'date' => $date,
                    'total_running' => $summary['total_running'],
                    'total_outage' => $summary['total_outage'],
                    'initial_reading' => $summary['initial_reading'],
                    'final_reading' => $summary['final_reading'],
                    'total_generation_kwh' => $summary['total_generation_kwh'],
                    'company' => $sheetCompany,
                    'month_label' => substr($date, 0, 7),
                ];

                foreach ($this->readEvents($sheet, $col, $row) as $event) {
                    $event['generator'] = $generator;
                    $event['date'] = $date;
                    $events[] = $event;
                }
            }
        }

        if ($days === []) {
            throw new GeneratorLogImportException(
                'No Generator 1 or Generator 2 blocks were found for Generator Meter Import.',
                [
                    'Each day sheet should have GENERATOR-1 and GENERATOR-2 at the bottom.',
                    'Columns: S.NO, TO, RESUME, SYNCH, TOTAL OUTAGE, REASON.',
                    'Totals: TOTAL RUNNING, TOTAL OUTAGE, INITIAL READING, FINAL READING, TOTAL GENERATION.',
                    $originalFilename ? "File: {$originalFilename}" : null,
                ]
            );
        }

        sort($dates);
        $monthLabel = $dates[0] === $dates[count($dates) - 1]
            ? $dates[0]
            : $dates[0] . ' → ' . $dates[count($dates) - 1];

        return [
            'days' => $days,
            'events' => $events,
            'warnings' => $warnings,
            'skipped' => $skipped,
            'company' => $company,
            'month_label' => $monthLabel,
        ];
    }

    /**
     * @return array<int, array{0: int, 1: int}>
     */
    private function findGeneratorBlocks(Worksheet $sheet): array
    {
        $highestRow = min(80, (int) $sheet->getHighestRow());
        $highestCol = min(60, Coordinate::columnIndexFromString($sheet->getHighestColumn() ?: 'A'));
        $found = [];

        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 1; $col <= $highestCol; $col++) {
                $label = strtoupper(str_replace([' ', '_'], '', (string) $this->text($sheet, $col, $row)));
                if ($label === 'GENERATOR-1' || $label === 'GENERATOR1') {
                    $found[1] = [$col, $row];
                }
                if ($label === 'GENERATOR-2' || $label === 'GENERATOR2') {
                    $found[2] = [$col, $row];
                }

                $serial = strtoupper(str_replace([' ', '_'], '', (string) $this->text($sheet, $col, $row)));
                $to = strtoupper((string) $this->text($sheet, $col + 1, $row));
                if (($serial === 'S.NO.' || $serial === 'S.NO' || $serial === 'SNO') && str_contains($to, 'TO')) {
                    $title = strtoupper(str_replace([' ', '_'], '', (string) $this->text($sheet, $col, $row - 1)));
                    $generator = str_contains($title, '2') ? 2 : ($col >= 15 ? 2 : 1);
                    $found[$generator] = $found[$generator] ?? [$col, $row - 1];
                }
            }
        }

        return $found;
    }

    private function sheetDate(Worksheet $sheet): ?string
    {
        $title = trim($sheet->getTitle());
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $title, $match) && $this->isBsDate($match[1], $match[2], $match[3])) {
            return sprintf('%04d-%02d-%02d', $match[1], $match[2], $match[3]);
        }

        $raw = (string) $this->text($sheet, 1, 2);
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/', $raw, $match) && $this->isBsDate($match[3], $match[2], $match[1])) {
            return sprintf('%04d-%02d-%02d', $match[3], $match[2], $match[1]);
        }

        return null;
    }

    private function isBsDate(string $year, string $month, string $day): bool
    {
        $year = (int) $year;
        $month = (int) $month;
        $day = (int) $day;

        return $year >= 2000 && $year <= 2200 && $month >= 1 && $month <= 12 && $day >= 1 && $day <= 32;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readEvents(Worksheet $sheet, int $startCol, int $titleRow): array
    {
        $events = [];
        for ($offset = 2; $offset <= 10; $offset++) {
            $row = $titleRow + $offset;
            $serialRaw = $this->text($sheet, $startCol, $row);
            if ($serialRaw === null || !preg_match('/^\d+$/', $serialRaw)) {
                break;
            }

            $tripTo = $this->time($sheet, $startCol + 1, $row);
            $resume = $this->time($sheet, $startCol + 2, $row);
            $synch = $this->time($sheet, $startCol + 3, $row);
            $outage = $this->time($sheet, $startCol + 4, $row);
            $reason = $this->text($sheet, $startCol + 5, $row);

            $hasTime = $tripTo || $resume || $synch;
            $hasOutage = $outage && $outage !== '0:00:00';
            if (!$hasTime && !$hasOutage && $reason === null) {
                continue;
            }

            $events[] = [
                'serial' => (int) $serialRaw,
                'trip_to' => $tripTo,
                'resume_hrs' => $resume,
                'synch_hrs' => $synch,
                'outage_hrs' => $outage,
                'reason' => $reason,
            ];
        }

        return $events;
    }

    /**
     * @return array{
     *   total_running: ?string,
     *   total_outage: ?string,
     *   initial_reading: ?float,
     *   final_reading: ?float,
     *   total_generation_kwh: ?float
     * }
     */
    private function readSummary(Worksheet $sheet, int $startCol, int $titleRow): array
    {
        $labelRow = null;
        for ($row = $titleRow + 1; $row <= $titleRow + 15; $row++) {
            $label = strtoupper((string) $this->text($sheet, $startCol, $row));
            if (str_contains($label, 'TOTAL RUNNING')) {
                $labelRow = $row;
                break;
            }
        }

        if ($labelRow === null) {
            return [
                'total_running' => null,
                'total_outage' => null,
                'initial_reading' => null,
                'final_reading' => null,
                'total_generation_kwh' => null,
            ];
        }

        $valueRow = $labelRow + 1;
        $initial = $this->number($sheet, $startCol + 7, $valueRow);
        $final = $this->number($sheet, $startCol + 9, $valueRow);
        $generation = $this->number($sheet, $startCol + 11, $valueRow);
        if ($generation === null && $initial !== null && $final !== null) {
            $generation = round($final - $initial, 3);
        }
        if ($generation === 0.0 && $initial === null && $final === null) {
            $generation = null;
        }

        return [
            'total_running' => $this->time($sheet, $startCol, $valueRow),
            'total_outage' => $this->time($sheet, $startCol + 2, $valueRow),
            'initial_reading' => $initial,
            'final_reading' => $final,
            'total_generation_kwh' => $generation,
        ];
    }

    private function text(Worksheet $sheet, int $col, int $row): ?string
    {
        $value = trim(preg_replace('/\s+/', ' ', (string) $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row)->getFormattedValue()) ?? '');

        return $value === '' ? null : $value;
    }

    private function time(Worksheet $sheet, int $col, int $row): ?string
    {
        $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row);
        $formatted = trim((string) $cell->getFormattedValue());
        if ($formatted === '') {
            return null;
        }

        if (preg_match('/^(\d{1,3}):(\d{2})(?::(\d{2}))?$/', $formatted, $match)) {
            return sprintf('%d:%02d:%02d', (int) $match[1], (int) $match[2], (int) ($match[3] ?? 0));
        }

        $raw = $cell->getValue();
        if (is_numeric($raw)) {
            $seconds = (int) round(((float) $raw) * 86400);
            $seconds = abs($seconds) % 86400;

            return sprintf('%d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60);
        }

        return $formatted;
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
            return round((float) $raw, 3);
        }

        $text = str_replace([',', ' '], '', trim((string) $cell->getFormattedValue()));

        return is_numeric($text) ? round((float) $text, 3) : null;
    }
}
