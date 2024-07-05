<?php

use App\Http\Controllers\Front\FrontController;
use Illuminate\Support\Facades\Route;

Route::get('/',[FrontController::class,'welcome'])->name('welcome');
