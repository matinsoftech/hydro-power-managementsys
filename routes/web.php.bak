<?php

use App\Http\Controllers\Front\FrontController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\ImprtExportController;

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


// Define additional routes for view, edit, update, and delete
Route::get('admin/view/{id}', [ImprtExportController::class, 'view'])->name('admin.view');
Route::get('admin/edit/{id}', [ImprtExportController::class, 'edit'])->name('admin.edit');
Route::put('admin/update/{id}', [ImprtExportController::class, 'update'])->name('admin.update');
Route::get('admin/destroy/{id}', [ImprtExportController::class, 'destroy'])->name('admin.destroy');
