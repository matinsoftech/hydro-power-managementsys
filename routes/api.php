<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login',[AuthController::class,'attemptApiLogin'])->name('api.attempt.login');
Route::middleware('auth:sanctum')->group(function(){
    Route::patch('profile',[AuthController::class,'updateProfile'])->name('api.update.profile');
    Route::post('logout',[AuthController::class,'logout'])->name('api.logout');

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::apiResource('user', \App\Http\Controllers\Admin\UserController::class)->names('api.admin.user');
        Route::apiResource('grid', \App\Http\Controllers\Admin\GridController::class)->names('api.admin.grid');
        Route::apiResource('fault', \App\Http\Controllers\Admin\FaultController::class)->names('api.admin.fault');
        Route::apiResource('holiday', \App\Http\Controllers\Admin\HolidayController::class)->names('api.admin.holiday');
        Route::apiResource('station', \App\Http\Controllers\Admin\StationController::class)->names('api.admin.station');
        Route::apiResource('meter-reading', \App\Http\Controllers\Admin\MeterReadingController::class)->names('api.admin.meter-reading');
        Route::post('meter-reading-detail', [\App\Http\Controllers\Admin\MeterReadingController::class, 'apiDetail'])->name('api.admin.meter-reading.detail');
    });

    Route::get('analytics-data', [\App\Http\Controllers\User\AnalyticsController::class, 'index']);
    Route::get('users',[\App\Http\Controllers\Admin\UserController::class,'apiIndex']);

    Route::middleware('user')->prefix('user')->group(function () {
        Route::post('entry-sys', [\App\Http\Controllers\User\EntrySysController::class, 'store']);
        Route::apiResource('grid', \App\Http\Controllers\User\GridController::class)->names('api.user.grid');
        Route::apiResource('fault', \App\Http\Controllers\User\FaultController::class)->names('api.user.fault');
        Route::apiResource('holiday', \App\Http\Controllers\User\HolidayController::class)->names('api.user.holiday');
        Route::apiResource('meter-reading', \App\Http\Controllers\User\MeterReadingController::class)->names('api.user.meter-reading');
        Route::post('meter-reading-detail', [\App\Http\Controllers\User\MeterReadingController::class, 'apiDetail'])->name('api.user.meter-reading.detail');
    });

});
