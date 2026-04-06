<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
