<?php

use App\Http\Controllers\Front\FrontController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/',[FrontController::class,'welcome'])->name('welcome');
Route::get('/map',[FrontController::class,'map'])->name('map');

Route::get('/run', function () {
    // Run the storage:link command
    Artisan::call('storage:link');

    // Run additional Artisan commands
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    // Return a simple message
    return 'Artisan commands have been executed.';
});
