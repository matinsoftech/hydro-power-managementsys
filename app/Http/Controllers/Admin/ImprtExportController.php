<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GridFailureImportException;
use App\Exceptions\GenerationImportException;
use App\Http\Controllers\Controller;
use App\Models\ExcelData;
use App\Models\GenerationReading;
use App\Models\GridFailure;
use App\Models\ImportBatch;
use App\Services\GenerationImportService;
use App\Services\GridFailureImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ImprtExportController extends Controller
{
    public function import_export(Request $request)
    {
        $failureQuery = GridFailure::query()->orderBy('date')->orderBy('from_hrs')->orderBy('unit');

        if ($request->filled('failure_unit')) {
            $failureQuery->where('unit', (int) $request->input('failure_unit'));
        }
        if ($request->filled('failure_start')) {
            $failureQuery->where('date', '>=', $request->input('failure_start'));
        }
        if ($request->filled('failure_end')) {
            $failureQuery->where('date', '<=', $request->input('failure_end'));
        }

        $failureRows = $failureQuery->paginate(20, ['*'], 'failure_page')->withQueryString();

        $failureImports = ImportBatch::query()
            ->where('type', ImportBatch::TYPE_FAILURE)
            ->with('importer')
            ->latest('imported_at')
            ->paginate(10, ['*'], 'import_page')
            ->withQueryString();

        $generationQuery = GenerationReading::query()
            ->orderBy('bs_initial_date')
            ->orderBy('id');

        if ($request->filled('generation_start')) {
            $generationQuery->where('bs_initial_date', '>=', $request->input('generation_start'));
        }
        if ($request->filled('generation_end')) {
            $generationQuery->where('bs_initial_date', '<=', $request->input('generation_end'));
        }

        $generationTotals = [
            'main_kwh' => (float) (clone $generationQuery)->sum('main_generation_kwh'),
            'check_kwh' => (float) (clone $generationQuery)->sum('check_generation_kwh'),
            'days' => (int) (clone $generationQuery)->distinct()->count('bs_initial_date'),
        ];

        $generationRows = $generationQuery->paginate(20, ['*'], 'generation_page')->withQueryString();

        $generationImports = ImportBatch::query()
            ->where('type', ImportBatch::TYPE_GENERATION)
            ->with('importer')
            ->latest('imported_at')
            ->paginate(10, ['*'], 'gen_import_page')
            ->withQueryString();

        return view('admin.import_export', compact(
            'failureRows',
            'failureImports',
            'generationRows',
            'generationImports',
            'generationTotals'
        ));
    }

    public function importFailure(Request $request, GridFailureImportService $importer)
    {
        $wantsJson = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        try {
            $request->validate([
                'import_file' => [
                    'required',
                    'file',
                    'max:10240',
                    'mimes:xlsx,xls,csv',
                ],
            ], [
                'import_file.required' => 'Please choose a GRID FAIL Excel file to upload.',
                'import_file.file' => 'The upload is not a valid file.',
                'import_file.max' => 'File is too large. Maximum size is 10 MB.',
                'import_file.mimes' => 'Only .xlsx, .xls, or .csv files are allowed.',
            ]);
        } catch (ValidationException $e) {
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'title' => 'Invalid File',
                    'message' => 'Please upload a valid Excel file (.xlsx or .xls).',
                    'details' => collect($e->errors())->flatten()->values()->all(),
                ], 422);
            }

            return redirect()
                ->route('admin.import_export', ['tab' => 'failure'])
                ->withErrors($e->errors())
                ->with('error', 'File validation failed. Fix the issues below and try again.');
        }

        $file = $request->file('import_file');
        $originalName = $file->getClientOriginalName();
        $storedPath = null;
        $startedAt = microtime(true);

        try {
            $parsed = $importer->parse($file->getRealPath(), $originalName);

            $storedPath = $file->storeAs(
                'imports/failures/' . now()->format('Y/m'),
                now()->format('Ymd_His') . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName),
                'local'
            );

            $existingKeys = GridFailure::query()
                ->get(['unit', 'date', 'from_hrs', 'to_hrs', 'synch_hrs', 'duration_hrs', 'reason'])
                ->map(fn (GridFailure $row) => $row->fingerprint())
                ->flip()
                ->all();

            $uniqueRows = [];
            $duplicateCount = 0;

            foreach ($parsed['rows'] as $row) {
                $key = GridFailure::fingerprintFromArray($row);
                if (isset($existingKeys[$key])) {
                    $duplicateCount++;
                    continue;
                }
                $existingKeys[$key] = true;
                $uniqueRows[] = $row;
            }

            $insertedCount = count($uniqueRows);
            $summaryRowsIgnored = (int) ($parsed['skipped'] ?? 0);
            $elapsedSeconds = round(microtime(true) - $startedAt, 1);

            if ($insertedCount === 0) {
                if ($storedPath) {
                    Storage::disk('local')->delete($storedPath);
                }

                $details = [];
                if ($duplicateCount > 0) {
                    $details[] = "{$duplicateCount} event(s) were already in the database, so nothing new was added.";
                }
                if ($summaryRowsIgnored > 0) {
                    $details[] = "{$summaryRowsIgnored} Excel TOTAL / summary row(s) were ignored (these are not outages).";
                }
                if (empty($details)) {
                    $details[] = 'The file did not contain any new failure events.';
                }
                $details[] = "Time taken: {$elapsedSeconds} second(s).";

                $payload = [
                    'success' => false,
                    'title' => 'Nothing New to Import',
                    'message' => 'No new failure records were saved.',
                    'details' => $details,
                    'summary' => [
                        'file' => $originalName,
                        'imported' => 0,
                        'duplicates' => $duplicateCount,
                        'summary_rows_ignored' => $summaryRowsIgnored,
                        'seconds' => $elapsedSeconds,
                    ],
                ];

                return $wantsJson
                    ? response()->json($payload, 422)
                    : redirect()->route('admin.import_export', ['tab' => 'failure'])->with('error', $payload['message']);
            }

            DB::transaction(function () use ($parsed, $originalName, $storedPath, $uniqueRows, $insertedCount, $summaryRowsIgnored, $duplicateCount) {
                $batch = ImportBatch::create([
                    'type' => ImportBatch::TYPE_FAILURE,
                    'original_filename' => $originalName,
                    'stored_path' => $storedPath,
                    'record_count' => $insertedCount,
                    'skipped_count' => $summaryRowsIgnored,
                    'duplicate_count' => $duplicateCount,
                    'imported_by' => Auth::id(),
                    'imported_at' => now(),
                    'notes' => $parsed['month_label'] ?? null,
                ]);

                foreach (array_chunk($uniqueRows, 200) as $chunk) {
                    foreach ($chunk as $row) {
                        $row['import_batch_id'] = $batch->id;
                        GridFailure::create($row);
                    }
                }
            });

            $details = [
                "{$insertedCount} new failure event(s) were saved from \"{$originalName}\".",
            ];
            if ($duplicateCount > 0) {
                $details[] = "{$duplicateCount} duplicate event(s) were skipped because they already exist.";
            }
            if ($summaryRowsIgnored > 0) {
                $details[] = "{$summaryRowsIgnored} Excel TOTAL / summary row(s) were ignored (daily totals, not outages).";
            }
            $details[] = "Time taken: {$elapsedSeconds} second(s).";
            $details[] = 'You can undo this import anytime from Import History.';

            $payload = [
                'success' => true,
                'title' => 'Import Successful',
                'message' => "{$insertedCount} new failure event(s) imported successfully.",
                'details' => $details,
                'summary' => [
                    'file' => $originalName,
                    'imported' => $insertedCount,
                    'duplicates' => $duplicateCount,
                    'summary_rows_ignored' => $summaryRowsIgnored,
                    'seconds' => $elapsedSeconds,
                ],
                'redirect' => route('admin.import_export', ['tab' => 'failure']),
            ];

            return $wantsJson
                ? response()->json($payload)
                : redirect()
                    ->route('admin.import_export', ['tab' => 'failure'])
                    ->with('import_result', $payload);
        } catch (GridFailureImportException $e) {
            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }

            $elapsedSeconds = round(microtime(true) - $startedAt, 1);
            $details = array_values(array_filter($e->details()));
            $details[] = "Time taken: {$elapsedSeconds} second(s).";

            $payload = [
                'success' => false,
                'title' => 'Import Failed',
                'message' => $e->getMessage(),
                'details' => $details,
            ];

            return $wantsJson
                ? response()->json($payload, 422)
                : redirect()
                    ->route('admin.import_export', ['tab' => 'failure'])
                    ->with('error', $e->getMessage())
                    ->with('import_errors', array_values(array_filter($e->details())));
        } catch (Throwable $e) {
            report($e);

            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }

            $elapsedSeconds = round(microtime(true) - $startedAt, 1);
            $payload = [
                'success' => false,
                'title' => 'Import Failed',
                'message' => 'Something went wrong while importing. No records were saved.',
                'details' => array_values(array_filter([
                    'Please try again with a valid GRID FAIL Excel file.',
                    config('app.debug') ? $e->getMessage() : null,
                    "Time taken: {$elapsedSeconds} second(s).",
                ])),
            ];

            return $wantsJson
                ? response()->json($payload, 500)
                : redirect()
                    ->route('admin.import_export', ['tab' => 'failure'])
                    ->with('error', $payload['message'])
                    ->with('import_errors', $payload['details']);
        }
    }

    public function undoFailureImport(ImportBatch $importBatch)
    {
        $tab = $importBatch->type === ImportBatch::TYPE_GENERATION ? 'generation' : 'failure';

        if (!in_array($importBatch->type, [ImportBatch::TYPE_FAILURE, ImportBatch::TYPE_GENERATION], true)) {
            return redirect()
                ->route('admin.import_export', ['tab' => $tab])
                ->with('error', 'Invalid import batch.');
        }

        if (!$importBatch->canUndo()) {
            return redirect()
                ->route('admin.import_export', ['tab' => $tab])
                ->with('error', 'This import was already undone or has no records.');
        }

        $filename = $importBatch->original_filename;
        $count = $importBatch->record_count;

        DB::transaction(function () use ($importBatch) {
            if ($importBatch->type === ImportBatch::TYPE_FAILURE) {
                GridFailure::where('import_batch_id', $importBatch->id)->delete();
            } else {
                GenerationReading::where('import_batch_id', $importBatch->id)->delete();
            }

            $importBatch->update([
                'undone_at' => now(),
                'undone_by' => Auth::id(),
            ]);
        });

        return redirect()
            ->route('admin.import_export', ['tab' => $tab])
            ->with('success', "Undone import \"{$filename}\" — removed {$count} record(s). History kept for audit.");
    }

    public function downloadFailureImport(ImportBatch $importBatch)
    {
        if (!in_array($importBatch->type, [ImportBatch::TYPE_FAILURE, ImportBatch::TYPE_GENERATION], true)) {
            abort(404);
        }

        $tab = $importBatch->type === ImportBatch::TYPE_GENERATION ? 'generation' : 'failure';

        if (!$importBatch->stored_path || !Storage::disk('local')->exists($importBatch->stored_path)) {
            return redirect()
                ->route('admin.import_export', ['tab' => $tab])
                ->with('error', 'Stored file not found for this import.');
        }

        return Storage::disk('local')->download(
            $importBatch->stored_path,
            $importBatch->original_filename
        );
    }

    public function exportFailure(Request $request): StreamedResponse
    {
        $query = GridFailure::query()->orderBy('date')->orderBy('unit')->orderBy('from_hrs');

        if ($request->filled('failure_unit')) {
            $query->where('unit', (int) $request->input('failure_unit'));
        }
        if ($request->filled('failure_start')) {
            $query->where('date', '>=', $request->input('failure_start'));
        }
        if ($request->filled('failure_end')) {
            $query->where('date', '<=', $request->input('failure_end'));
        }

        $rows = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Grid Failures');

        $headers = ['Unit', 'Date', 'From (Hrs)', 'To (Hrs)', 'Synch (Hrs)', 'Duration (Hrs)', 'Reason / Remarks'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue([$i + 1, 1], $header);
        }

        $r = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue([1, $r], 'Unit ' . $row->unit);
            $sheet->setCellValue([2, $r], $row->date);
            $sheet->setCellValue([3, $r], $row->from_hrs);
            $sheet->setCellValue([4, $r], $row->to_hrs);
            $sheet->setCellValue([5, $r], $row->synch_hrs);
            $sheet->setCellValue([6, $r], $row->duration_hrs);
            $sheet->setCellValue([7, $r], $row->reason);
            $r++;
        }

        foreach (range(1, 7) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $filename = 'grid-failures-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadFailureTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sheet1');

        $sheet->setCellValue([1, 1], 'SAIDI POWER COMPANY LTD.');
        $sheet->setCellValue([8, 1], 'SAIDI POWER COMPANY LTD.');
        $sheet->setCellValue([1, 2], "UNIT-1ST GRID FAILURE & FORCE OUTAGE'S RECORD");
        $sheet->setCellValue([8, 2], "UNIT-2ND GRID FAILURE & FORCE OUTAGE'S RECORD");

        $headers = ['DATE', 'FROM (HRS)', 'TO (HRS)', 'SYNCH (HRS)', 'DURATION (HRS)', 'REASON/REMARKS'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue([$i + 1, 3], $header);
            $sheet->setCellValue([$i + 8, 3], $header);
        }

        // Sample Unit 1
        $sheet->setCellValue([1, 4], '2083-05-02');
        $sheet->setCellValue([2, 4], '21:12:00');
        $sheet->setCellValue([3, 4], '21:16:00');
        $sheet->setCellValue([4, 4], '21:27:00');
        $sheet->setCellValue([5, 4], '0:15:00');
        $sheet->setCellValue([6, 4], 'Machine Trip Due To NEA Grid Gone');

        // Sample Unit 2
        $sheet->setCellValue([8, 4], '2083-05-02');
        $sheet->setCellValue([9, 4], '21:12:00');
        $sheet->setCellValue([10, 4], '21:16:00');
        $sheet->setCellValue([11, 4], '21:22:00');
        $sheet->setCellValue([12, 4], '0:10:00');
        $sheet->setCellValue([13, 4], 'Machine Trip Due To NEA Grid Gone');

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'grid-failure-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function destroyFailure(GridFailure $gridFailure)
    {
        $gridFailure->delete();

        return redirect()
            ->route('admin.import_export', ['tab' => 'failure'])
            ->with('success', 'Failure record deleted.');
    }

    public function importGeneration(Request $request, GenerationImportService $importer)
    {
        $wantsJson = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        try {
            $request->validate([
                'import_file' => [
                    'required',
                    'file',
                    'max:10240',
                    'mimes:xlsx,xls,csv',
                ],
            ], [
                'import_file.required' => 'Please choose a generation Excel file to upload.',
                'import_file.file' => 'The upload is not a valid file.',
                'import_file.max' => 'File is too large. Maximum size is 10 MB.',
                'import_file.mimes' => 'Only .xlsx, .xls, or .csv files are allowed.',
            ]);
        } catch (ValidationException $e) {
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'title' => 'Invalid File',
                    'message' => 'Please upload a valid Excel file (.xlsx or .xls).',
                    'details' => collect($e->errors())->flatten()->values()->all(),
                ], 422);
            }

            return redirect()
                ->route('admin.import_export', ['tab' => 'generation'])
                ->withErrors($e->errors())
                ->with('error', 'File validation failed. Fix the issues below and try again.');
        }

        $file = $request->file('import_file');
        $originalName = $file->getClientOriginalName();
        $storedPath = null;
        $startedAt = microtime(true);

        try {
            $parsed = $importer->parse($file->getRealPath(), $originalName);

            $storedPath = $file->storeAs(
                'imports/generation/' . now()->format('Y/m'),
                now()->format('Ymd_His') . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName),
                'local'
            );

            $existingKeys = GenerationReading::query()
                ->get([
                    'bs_initial_date', 'bs_final_date', 'ad_initial_date', 'ad_final_date',
                    'main_initial', 'main_final', 'main_generation_kwh',
                    'check_initial', 'check_final', 'check_generation_kwh',
                ])
                ->map(fn (GenerationReading $row) => $row->fingerprint())
                ->flip()
                ->all();

            $uniqueRows = [];
            $duplicateCount = 0;

            foreach ($parsed['rows'] as $row) {
                $key = GenerationReading::fingerprintFromArray($row);
                if (isset($existingKeys[$key])) {
                    $duplicateCount++;
                    continue;
                }
                $existingKeys[$key] = true;
                $uniqueRows[] = $row;
            }

            $insertedCount = count($uniqueRows);
            $summaryRowsIgnored = (int) ($parsed['skipped'] ?? 0);
            $elapsedSeconds = round(microtime(true) - $startedAt, 1);

            if ($insertedCount === 0) {
                if ($storedPath) {
                    Storage::disk('local')->delete($storedPath);
                }

                $details = [];
                if ($duplicateCount > 0) {
                    $details[] = "{$duplicateCount} day reading(s) were already in the database, so nothing new was added.";
                }
                if ($summaryRowsIgnored > 0) {
                    $details[] = "{$summaryRowsIgnored} Excel TOTAL row(s) were ignored (month totals, not daily readings).";
                }
                if (empty($details)) {
                    $details[] = 'The file did not contain any new generation readings.';
                }
                $details[] = "Time taken: {$elapsedSeconds} second(s).";

                $payload = [
                    'success' => false,
                    'title' => 'Nothing New to Import',
                    'message' => 'No new generation records were saved.',
                    'details' => $details,
                    'summary' => [
                        'file' => $originalName,
                        'imported' => 0,
                        'duplicates' => $duplicateCount,
                        'summary_rows_ignored' => $summaryRowsIgnored,
                        'seconds' => $elapsedSeconds,
                    ],
                ];

                return $wantsJson
                    ? response()->json($payload, 422)
                    : redirect()->route('admin.import_export', ['tab' => 'generation'])->with('error', $payload['message']);
            }

            DB::transaction(function () use ($parsed, $originalName, $storedPath, $uniqueRows, $insertedCount, $summaryRowsIgnored, $duplicateCount) {
                $batch = ImportBatch::create([
                    'type' => ImportBatch::TYPE_GENERATION,
                    'original_filename' => $originalName,
                    'stored_path' => $storedPath,
                    'record_count' => $insertedCount,
                    'skipped_count' => $summaryRowsIgnored,
                    'duplicate_count' => $duplicateCount,
                    'imported_by' => Auth::id(),
                    'imported_at' => now(),
                    'notes' => $parsed['month_label'] ?? null,
                ]);

                foreach (array_chunk($uniqueRows, 200) as $chunk) {
                    foreach ($chunk as $row) {
                        $row['import_batch_id'] = $batch->id;
                        GenerationReading::create($row);
                    }
                }
            });

            $details = [
                "{$insertedCount} new daily reading(s) were saved from \"{$originalName}\".",
            ];
            if ($duplicateCount > 0) {
                $details[] = "{$duplicateCount} duplicate day reading(s) were skipped because they already exist.";
            }
            if ($summaryRowsIgnored > 0) {
                $details[] = "{$summaryRowsIgnored} Excel TOTAL row(s) were ignored (month totals, not daily readings).";
            }
            $details[] = "Time taken: {$elapsedSeconds} second(s).";
            $details[] = 'You can undo this import anytime from Import History.';

            $payload = [
                'success' => true,
                'title' => 'Import Successful',
                'message' => "{$insertedCount} new generation reading(s) imported successfully.",
                'details' => $details,
                'summary' => [
                    'file' => $originalName,
                    'imported' => $insertedCount,
                    'duplicates' => $duplicateCount,
                    'summary_rows_ignored' => $summaryRowsIgnored,
                    'seconds' => $elapsedSeconds,
                ],
                'redirect' => route('admin.import_export', ['tab' => 'generation']),
            ];

            return $wantsJson
                ? response()->json($payload)
                : redirect()
                    ->route('admin.import_export', ['tab' => 'generation'])
                    ->with('import_result', $payload);
        } catch (GenerationImportException $e) {
            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }

            $elapsedSeconds = round(microtime(true) - $startedAt, 1);
            $details = array_values(array_filter($e->details()));
            $details[] = "Time taken: {$elapsedSeconds} second(s).";

            $payload = [
                'success' => false,
                'title' => 'Import Failed',
                'message' => $e->getMessage(),
                'details' => $details,
            ];

            return $wantsJson
                ? response()->json($payload, 422)
                : redirect()
                    ->route('admin.import_export', ['tab' => 'generation'])
                    ->with('error', $e->getMessage())
                    ->with('import_errors', array_values(array_filter($e->details())));
        } catch (Throwable $e) {
            report($e);

            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }

            $elapsedSeconds = round(microtime(true) - $startedAt, 1);
            $payload = [
                'success' => false,
                'title' => 'Import Failed',
                'message' => 'Something went wrong while importing. No records were saved.',
                'details' => array_values(array_filter([
                    'Please try again with a valid 24 HOURS GENERATION Excel file.',
                    config('app.debug') ? $e->getMessage() : null,
                    "Time taken: {$elapsedSeconds} second(s).",
                ])),
            ];

            return $wantsJson
                ? response()->json($payload, 500)
                : redirect()
                    ->route('admin.import_export', ['tab' => 'generation'])
                    ->with('error', $payload['message'])
                    ->with('import_errors', $payload['details']);
        }
    }

    public function exportGeneration(Request $request): StreamedResponse
    {
        $query = GenerationReading::query()->orderBy('bs_initial_date')->orderBy('id');

        if ($request->filled('generation_start')) {
            $query->where('bs_initial_date', '>=', $request->input('generation_start'));
        }
        if ($request->filled('generation_end')) {
            $query->where('bs_initial_date', '<=', $request->input('generation_end'));
        }

        $rows = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Generation');

        $headers = [
            'AD Initial', 'AD Final', 'BS Initial', 'BS Final',
            'Main Initial', 'Main Final', 'Main Generation (kWh)',
            'Check Initial', 'Check Final', 'Check Generation (kWh)',
            'Month Label', 'Company',
        ];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue([$i + 1, 1], $header);
        }

        $r = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue([1, $r], $row->ad_initial_date);
            $sheet->setCellValue([2, $r], $row->ad_final_date);
            $sheet->setCellValue([3, $r], $row->bs_initial_date);
            $sheet->setCellValue([4, $r], $row->bs_final_date);
            $sheet->setCellValue([5, $r], $row->main_initial);
            $sheet->setCellValue([6, $r], $row->main_final);
            $sheet->setCellValue([7, $r], $row->main_generation_kwh);
            $sheet->setCellValue([8, $r], $row->check_initial);
            $sheet->setCellValue([9, $r], $row->check_final);
            $sheet->setCellValue([10, $r], $row->check_generation_kwh);
            $sheet->setCellValue([11, $r], $row->month_label);
            $sheet->setCellValue([12, $r], $row->company);
            $r++;
        }

        foreach (range(1, 12) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $filename = 'generation-readings-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadGenerationTemplate(): StreamedResponse
    {
        $samplePath = storage_path('app/generation_sample.xlsx');
        if (is_readable($samplePath)) {
            return response()->download($samplePath, '24-hours-generation-template.xlsx');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sheet1');

        $sheet->setCellValue([2, 1], 'SAIDI POWER COMPANY Limited');
        $sheet->setCellValue([2, 2], 'GENERATION DATA FOR THE MONTH OF BHADRA -2083');
        $sheet->setCellValue([2, 3], 'DATE');
        $sheet->setCellValue([6, 3], 'MAIN METER');
        $sheet->setCellValue([9, 3], 'CHECK METER');
        $sheet->setCellValue([2, 4], 'A.D.');
        $sheet->setCellValue([4, 4], 'B.S.');
        $sheet->setCellValue([2, 5], 'INITIAL (12AM)');
        $sheet->setCellValue([3, 5], 'FINAL (12AM)');
        $sheet->setCellValue([4, 5], 'INITIAL (12AM)');
        $sheet->setCellValue([5, 5], 'FINAL (12AM)');
        $sheet->setCellValue([6, 5], 'INITIAL READING');
        $sheet->setCellValue([7, 5], 'FINAL READING');
        $sheet->setCellValue([8, 5], 'GENERATION (KWH)');
        $sheet->setCellValue([9, 5], 'INITIAL READING');
        $sheet->setCellValue([10, 5], 'FINAL READING');
        $sheet->setCellValue([11, 5], 'GENERATION (KWH)');

        $sheet->setCellValue([2, 6], '17-Sep-26');
        $sheet->setCellValue([3, 6], '18-Sep-26');
        $sheet->setCellValue([4, 6], '2083-05-01');
        $sheet->setCellValue([5, 6], '2083-05-02');
        $sheet->setCellValue([6, 6], 18556956);
        $sheet->setCellValue([7, 6], 18581075);
        $sheet->setCellValue([8, 6], 24119);
        $sheet->setCellValue([11, 6], 0);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, '24-hours-generation-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function destroyGeneration(GenerationReading $generationReading)
    {
        $generationReading->delete();

        return redirect()
            ->route('admin.import_export', ['tab' => 'generation'])
            ->with('success', 'Generation reading deleted.');
    }

    // ---- Legacy generation CSV import/export (kept for reference) ----

    public function import_file(Request $request)
    {
        try {
            $file = $request->file('import_file');
            $csvData = file_get_contents($file);
            $lines = explode("\n", $csvData);
            $header = str_getcsv(array_shift($lines));
            $ExcelDatas = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                $row = str_getcsv($line);
                array_shift($row);

                $formattedDate = Carbon::createFromFormat('m/d/Y', $row[0])->format('Y-m-d');
                $ExcelDatas[] = [
                    'Date' => $formattedDate ?? null,
                    'Time_or_Hour' => isset($row[1]) ? Carbon::parse($row[1])->format('H:i') : null,
                    'GenerationUnit1_NozelOpen' => $row[2] ?? null,
                    'GenerationUnit1_GeneratorVoltage_AY' => $row[3] ?? null,
                    'GenerationUnit1_GeneratorVoltage_YB' => $row[4] ?? null,
                    'GenerationUnit1_GeneratorVoltage_BR' => $row[5] ?? null,
                    'GenerationUnit1_GeneratorCurrent_I1' => $row[6] ?? null,
                    'GenerationUnit1_GeneratorCurrent_I2' => $row[7] ?? null,
                    'GenerationUnit1_GeneratorCurrent_I3' => $row[8] ?? null,
                    'GenerationUnit1_GeneratorOutput_KW' => $row[9] ?? null,
                    'GenerationUnit1_GeneratorOutput_kVAr' => $row[10] ?? null,
                    'GenerationUnit1_PF_Close' => $row[11] ?? null,
                    'GenerationUnit1_Frequency_HZ' => $row[12] ?? null,
                    'GenerationUnit1_Energy_kWH' => $row[13] ?? null,
                    'GenerationUnit2_NozelOpen' => $row[14] ?? null,
                    'GenerationUnit2_GeneratorVoltage_AY' => $row[15] ?? null,
                    'GenerationUnit2_GeneratorVoltage_YB' => $row[16] ?? null,
                    'GenerationUnit2_GeneratorVoltage_BR' => $row[17] ?? null,
                    'GenerationUnit2_GeneratorCurrent_I1' => $row[18] ?? null,
                    'GenerationUnit2_GeneratorCurrent_I2' => $row[19] ?? null,
                    'GenerationUnit2_GeneratorCurrent_I3' => $row[20] ?? null,
                    'GenerationUnit2_GeneratorOutput_KW' => $row[21] ?? null,
                    'GenerationUnit2_GeneratorOutput_kVAr' => $row[22] ?? null,
                    'GenerationUnit2_PF_Close' => $row[23] ?? null,
                    'GenerationUnit2_Frequency_HZ' => $row[24] ?? null,
                    'GenerationUnit2_Energy_kWH' => $row[25] ?? null,
                    '_11KVSide_LineVoltage_RY' => $row[26] ?? null,
                    '_11KVSide_LineVoltage_YB' => $row[27] ?? null,
                    '_11KVSide_LineVoltage_BR' => $row[28] ?? null,
                    '_11KVSide_LineCurrent_I1' => $row[29] ?? null,
                    '_11KVSide_LineCurrent_I2' => $row[30] ?? null,
                    '_11KVSide_LineCurrent_I3' => $row[31] ?? null,
                    '_11KVSide_Output_KW' => $row[32] ?? null,
                    '_11KVSide_Output_kVAr' => $row[33] ?? null,
                    '_11KVSide_PF_Close' => $row[34] ?? null,
                    '_11KVSide_Frequency_Hz' => $row[35] ?? null,
                    'MainMeterReading_kWH' => $row[36] ?? null,
                    'CheckMeterReading_kWH' => $row[37] ?? null,
                    'MainMeterUnitIn1hr_kWH' => $row[38] ?? null,
                    'CheckMeterUnitIn1hr_kWH' => $row[39] ?? null,
                    'Remark' => $row[40] ?? null,
                ];
            }

            if (!empty($ExcelDatas)) {
                DB::table('excel_data')->insert($ExcelDatas);
            }

            return redirect()->back()->with('success', 'Data imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'File not supported.');
        }
    }

    public function export_file()
    {
        $ExcelDatas = ExcelData::all();
        $csvData = "id,Date,Time_or_Hour,GenerationUnit1_NozelOpen_%, GenerationUnit1_GeneratorVoltage(KV)_A-Y,GenerationUnit1_GeneratorVoltage(KV)_Y-B, GenerationUnit1_GeneratorVoltage(KV)_B-R, GenerationUnit1_GeneratorCurrent(A)_I1, GenerationUnit1_GeneratorCurrent(A)_I2, GenerationUnit1_GeneratorCurrent(A)_I3, GenerationUnit1_GeneratorOutput_KW, GenerationUnit1_GeneratorOutput_kVAr, GenerationUnit1_PF_Close, GenerationUnit1_Frequency_HZ, GenerationUnit1_Energy_kWH, GenerationUnit2_NozelOpen_%, GenerationUnit2_GeneratorVoltage(KV)_A-Y, GenerationUnit2_GeneratorVoltage(KV)_Y-B, GenerationUnit2_GeneratorVoltage(KV)_B-R, GenerationUnit2_GeneratorCurrent(A)_I1,GenerationUnit2_GeneratorCurrent(A)_I2, GenerationUnit2_GeneratorCurrent(A)_I3, GenerationUnit2_GeneratorOutput_KW,GenerationUnit2_GeneratorOutput_kVAr,GenerationUnit2_PF_Close,GenerationUnit2_Frequency_HZ, GenerationUnit2_Energy_kWH,11KVSide_LineVoltage(KV)_R-Y,11KVSide_LineVoltage(KV)_Y-B,11KVSide_LineVoltage(KV)_B-R, 11KVSide_LineCurrent(A)_I1,11KVSide_LineCurrent(A)_I2,11KVSide_LineCurrent(A)_I3,11KVSide_Output_KW,11KVSide_Output_kVAr,11KVSide_PF_Close,11KVSide_Frequency_Hz,MainMeterReading_kWH,CheckMeterReading_kWH,MainMeterUnitIn1hr_kWH,CheckMeterUnitIn1hr_kWH,Remark\n";

        foreach ($ExcelDatas as $ExcelData) {
            $csvData .= "{$ExcelData->id},{$ExcelData->Date},{$ExcelData->Time_or_Hour},{$ExcelData->GenerationUnit1_NozelOpen},{$ExcelData->GenerationUnit1_GeneratorVoltage_AY},{$ExcelData->GenerationUnit1_GeneratorVoltage_YB},{$ExcelData->GenerationUnit1_GeneratorVoltage_BR},{$ExcelData->GenerationUnit1_GeneratorCurrent_I1},{$ExcelData->GenerationUnit1_GeneratorCurrent_I2},{$ExcelData->GenerationUnit1_GeneratorCurrent_I3},{$ExcelData->GenerationUnit1_GeneratorOutput_KW},{$ExcelData->GenerationUnit1_GeneratorOutput_kVAr}, {$ExcelData->GenerationUnit1_PF_Close},{$ExcelData->GenerationUnit1_Frequency_HZ},{$ExcelData->GenerationUnit1_Energy_kWH}, {$ExcelData->GenerationUnit2_NozelOpen}, {$ExcelData->GenerationUnit2_GeneratorVoltage_AY}, {$ExcelData->GenerationUnit2_GeneratorVoltage_YB}, {$ExcelData->GenerationUnit2_GeneratorVoltage_BR}, {$ExcelData->GenerationUnit2_GeneratorCurrent_I1},{$ExcelData->GenerationUnit2_GeneratorCurrent_I2},{$ExcelData->GenerationUnit2_GeneratorCurrent_I3},{$ExcelData->GenerationUnit2_GeneratorOutput_KW},{$ExcelData->GenerationUnit2_GeneratorOutput_kVAr},{$ExcelData->GenerationUnit2_PF_Close},{$ExcelData->GenerationUnit2_Frequency_HZ},{$ExcelData->GenerationUnit2_Energy_kWH},{$ExcelData->_11KVSide_LineVoltagssss_RY},{$ExcelData->_11KVSide_LineVoltage_YB},{$ExcelData->_11KVSide_LineVoltage_BR},{$ExcelData->_11KVSide_LineCurrent_I1},{$ExcelData->_11KVSide_LineCurrent_I2},{$ExcelData->_11KVSide_LineCurrent_I3},{$ExcelData->_11KVSide_Output_KW},{$ExcelData->_11KVSide_Output_kVAr},{$ExcelData->_11KVSide_PF_Close},{$ExcelData->_11KVSide_Frequency_Hz},{$ExcelData->MainMeterReading_kWH},{$ExcelData->CheckMeterReading_kWH},{$ExcelData->MainMeterUnitIn1hr_kWH},{$ExcelData->CheckMeterUnitIn1hr_kWH},{$ExcelData->Remark}\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="excel.csv"');
    }

    public function view($id)
    {
        $data = ExcelData::findOrFail($id);
        return view('admin.view', compact('data'));
    }

    public function edit($id)
    {
        $data = ExcelData::findOrFail($id);
        return view('admin.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = ExcelData::findOrFail($id);
        $data->update($request->all());
        return redirect()->route('admin.import_export')->with('updated', 'Data updated successfully.');
    }

    public function destroy($id)
    {
        ExcelData::findOrFail($id)->delete();
        return redirect()->route('admin.import_export')->with('deleted', 'Data deleted successfully.');
    }
}
