<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\GridController;
use App\Http\Controllers\User\FaultController;
use App\Http\Controllers\User\HolidayController;
use App\Http\Controllers\User\MeterReadingController;


Route::resource('grid', GridController::class);
Route::resource('fault', FaultController::class);
Route::resource('holiday', HolidayController::class);

Route::resource('meter-reading', MeterReadingController::class);
Route::get('meter-reading-detail', [MeterReadingController::class, 'detail'])->name('meter-reading.detail');
