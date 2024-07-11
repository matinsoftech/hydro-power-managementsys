<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login',[AuthController::class,'attemptApiLogin'])->name('attempt.login');
Route::middleware('auth:sanctum')->group(function(){
    Route::patch('profile',[AuthController::class,'updateProfile'])->name('update.profile');
    Route::post('logout',[AuthController::class,'logout'])->name('logout');

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::apiResource('user', \App\Http\Controllers\Admin\UserController::class);
        Route::apiResource('grid', \App\Http\Controllers\Admin\GridController::class);
        Route::apiResource('fault', \App\Http\Controllers\Admin\FaultController::class);
        Route::apiResource('holiday', \App\Http\Controllers\Admin\HolidayController::class);
        Route::apiResource('station', \App\Http\Controllers\Admin\StationController::class);
        Route::apiResource('meter-reading', \App\Http\Controllers\Admin\MeterReadingController::class);
        Route::post('meter-reading-detail', [\App\Http\Controllers\Admin\MeterReadingController::class, 'apiDetail'])->name('admin.meter-reading.detail');
    });

    Route::get('analytics-data', [\App\Http\Controllers\User\AnalyticsController::class, 'index']);

    Route::middleware('user')->prefix('user')->group(function () {
        Route::post('entry-sys', [\App\Http\Controllers\User\EntrySysController::class, 'store']);
        Route::apiResource('grid', \App\Http\Controllers\User\GridController::class);
        Route::apiResource('fault', \App\Http\Controllers\User\FaultController::class);
        Route::apiResource('holiday', \App\Http\Controllers\User\HolidayController::class);
        Route::apiResource('meter-reading', \App\Http\Controllers\User\MeterReadingController::class);
        Route::post('meter-reading-detail', [\App\Http\Controllers\User\MeterReadingController::class, 'apiDetail'])->name('user.meter-reading.detail');
    });

});

