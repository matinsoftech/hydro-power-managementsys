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

Route::get('import_export', [ImprtExportController::class, 'import_export'])->name('import_export');
Route::post('import_file', [ImprtExportController::class, 'import_file'])->name('import_file');
Route::get('export_file', [ImprtExportController::class, 'export_file'])->name('export_file');


