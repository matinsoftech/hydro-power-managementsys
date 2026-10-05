<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GridController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FaultController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\MailSettingController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\MeterReadingController;
use App\Http\Controllers\Admin\StationController;
use App\Http\Controllers\Admin\ImprtExportController;
use App\Http\Controllers\Admin\FaultReportController;
use App\Http\Controllers\Admin\GenerationReportController;
use App\Http\Controllers\Admin\GeneratorLogReportController;
use App\Http\Controllers\Admin\PlantLogReportController;


// Notes: @middleware:admin @prefix:admin @as:admin.

// Admin Settings
Route::resource('site-setting', SiteSettingController::class)->only(['index', 'update']);
Route::resource('mail-setting', MailSettingController::class)->only(['index', 'update']);

Route::resource('user', UserController::class);
Route::resource('grid', GridController::class);
Route::resource('fault', FaultController::class);
// Route::post('/fault-store', [FaultController::class, 'store'])->name('admin.fault.created');

// Route::get('/fault-edit/{id}', [FaultController::class, 'edit'])->name('admin.fault.edit');


Route::resource('holiday', HolidayController::class);

Route::resource('station', StationController::class);
Route::post('sub-station',[StationController::class,'getSubStation'])->name('station.getSubStation');

Route::resource('meter-reading', MeterReadingController::class);
Route::get('meter-reading-detail', [MeterReadingController::class, 'detail'])->name('meter-reading.detail');

Route::get('import-export', [ImprtExportController::class, 'import_export'])->name('import_export');
Route::post('import-failure', [ImprtExportController::class, 'importFailure'])->name('import_failure');
Route::get('export-failure', [ImprtExportController::class, 'exportFailure'])->name('export_failure');
Route::get('failure-template', [ImprtExportController::class, 'downloadFailureTemplate'])->name('failure_template');
Route::delete('grid-failure/{gridFailure}', [ImprtExportController::class, 'destroyFailure'])->name('grid_failure.destroy');
Route::post('import-generation', [ImprtExportController::class, 'importGeneration'])->name('import_generation');
Route::get('export-generation', [ImprtExportController::class, 'exportGeneration'])->name('export_generation');
Route::get('generation-template', [ImprtExportController::class, 'downloadGenerationTemplate'])->name('generation_template');
Route::delete('generation-reading/{generationReading}', [ImprtExportController::class, 'destroyGeneration'])->name('generation_reading.destroy');
Route::post('import-generator-log', [ImprtExportController::class, 'importGeneratorLog'])->name('import_generator_log');
Route::get('export-generator-log', [ImprtExportController::class, 'exportGeneratorLog'])->name('export_generator_log');
Route::get('generator-log-template', [ImprtExportController::class, 'downloadGeneratorLogTemplate'])->name('generator_log_template');
Route::delete('generator-log/{generatorDailyLog}', [ImprtExportController::class, 'destroyGeneratorLog'])->name('generator_log.destroy');
Route::post('import-plant-log', [ImprtExportController::class, 'importPlantLog'])->name('import_plant_log');
Route::get('export-plant-log', [ImprtExportController::class, 'exportPlantLog'])->name('export_plant_log');
Route::get('plant-log-template', [ImprtExportController::class, 'downloadPlantLogTemplate'])->name('plant_log_template');
Route::delete('plant-log/{logDay}', [ImprtExportController::class, 'destroyPlantLog'])->name('plant_log.destroy');
Route::post('import-batch/{importBatch}/undo', [ImprtExportController::class, 'undoFailureImport'])->name('import_batch.undo');
Route::get('import-batch/{importBatch}/download', [ImprtExportController::class, 'downloadFailureImport'])->name('import_batch.download');

Route::get('import-file', [ImprtExportController::class, 'import_file'])->name('import_file');
Route::get('export-file', [ImprtExportController::class, 'export_file'])->name('export_file');

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('fault', [FaultReportController::class, 'index'])->name('fault');
    Route::get('fault/export', [FaultReportController::class, 'export'])->name('fault.export');
    Route::get('generation', [GenerationReportController::class, 'index'])->name('generation');
    Route::get('generation/export', [GenerationReportController::class, 'export'])->name('generation.export');
    Route::get('generator', [GeneratorLogReportController::class, 'index'])->name('generator');
    Route::get('generator/export', [GeneratorLogReportController::class, 'export'])->name('generator.export');
    Route::get('log', [PlantLogReportController::class, 'index'])->name('log');
    Route::get('log/export', [PlantLogReportController::class, 'export'])->name('log.export');
});
