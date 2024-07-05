<?php

use App\Http\Controllers\Admin\FaultController;
use App\Http\Controllers\Admin\GridController;
use App\Http\Controllers\Admin\HolidayController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MailSettingController;
use App\Http\Controllers\Admin\SiteSettingController;

// Notes: @middleware:admin @prefix:admin @as:admin.

// Admin Settings
Route::resource('site-setting', SiteSettingController::class)->only(['index', 'update']);
Route::resource('mail-setting', MailSettingController::class)->only(['index', 'update']);

Route::resource('user', UserController::class);
Route::resource('grid', GridController::class);
Route::resource('fault', FaultController::class);
Route::resource('holiday', HolidayController::class);
