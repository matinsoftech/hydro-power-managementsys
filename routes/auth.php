<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login',[AuthController::class,'login'])->name('login')->middleware('guest');
Route::post('/login',[AuthController::class,'attemptLogin'])->name('attempt.login');

Route::get('register',[AuthController::class,'register'])->name('register');
Route::post('register',[AuthController::class,'attemptRegister'])->name('attempt.register');

Route::middleware('auth')->group(function(){
    Route::get('/dashboard',[DashboardController::class,'dashboard'])->name('dashboard');

    Route::get('/profile',[AuthController::class,'profile'])->name('profile');
    Route::patch('/profile',[AuthController::class,'updateProfile'])->name('update.profile');
    Route::get('/logout',[AuthController::class,'logout'])->name('logout');
});
