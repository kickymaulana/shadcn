<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\Master\SubDepartemenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\FormulirController;



Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('testing', [DashboardController::class, 'testing'])->name('testing');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware('auth', 'role:admin')->group(function () {
    // ... route lainnya
    Route::get('master/users', [UserController::class, 'index'])->name('users.index');
    Route::get('master/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('master/users/create', [UserController::class, 'store'])->name('users.store');
    Route::get('master/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('master/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('master/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('master/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('master/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('master/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('master/roles/create', [RoleController::class, 'store'])->name('roles.store');
    Route::get('master/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('master/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('master/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('master/departemens', [DepartemenController::class, 'index'])->name('departemens.index');
    Route::get('master/departemens/create', [DepartemenController::class, 'create'])->name('departemens.create');
    Route::post('master/departemens/create', [DepartemenController::class, 'store'])->name('departemens.store');
    Route::get('master/departemens/{departemen}', [DepartemenController::class, 'show'])->name('departemens.show');
    Route::get('master/departemens/{departemen}/edit', [DepartemenController::class, 'edit'])->name('departemens.edit');
    Route::put('master/departemens/{departemen}', [DepartemenController::class, 'update'])->name('departemens.update');
    Route::delete('master/departemens/{departemen}', [DepartemenController::class, 'destroy'])->name('departemens.destroy');

    Route::get('master/sub-departemens', [SubDepartemenController::class, 'index'])->name('sub.departemens.index');
    Route::get('master/sub-departemens/create', [SubDepartemenController::class, 'create'])->name('sub.departemens.create');
    Route::post('master/sub-departemens/create', [SubDepartemenController::class, 'store'])->name('sub.departemens.store');
    Route::get('master/sub-departemens/{subDepartemen}', [SubDepartemenController::class, 'show'])->name('sub.departemens.show');
    Route::get('master/sub-departemens/{subDepartemen}/edit', [SubDepartemenController::class, 'edit'])->name('sub.departemens.edit');
    Route::put('master/sub-departemens/{subDepartemen}', [SubDepartemenController::class, 'update'])->name('sub.departemens.update');
    Route::delete('master/sub-departemens/{subDepartemen}', [SubDepartemenController::class, 'destroy'])->name('sub.departemens.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('samples', [SampleController::class, 'index'])->name('samples.index');
    Route::get('samples/create', [SampleController::class, 'create'])->name('samples.create');
    Route::post('samples/create', [SampleController::class, 'store'])->name('samples.store');
    Route::get('samples/{sample}', [SampleController::class, 'show'])->name('samples.show');
    Route::get('samples/{sample}/edit', [SampleController::class, 'edit'])->name('samples.edit');
    Route::put('samples/{sample}', [SampleController::class, 'update'])->name('samples.update');
    Route::delete('samples/{sample}', [SampleController::class, 'destroy'])->name('samples.destroy');

    Route::get('formulirs', [FormulirController::class, 'index'])->name('formulirs.index');
});
