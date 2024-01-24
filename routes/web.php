<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AktivitasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/',[DashboardController::class, 'index'])->name('index');

    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/role',[RoleController::class, 'index'])->name('role');
        Route::post('/role',[RoleController::class, 'store'])->name('role.store');
        Route::patch('/role',[RoleController::class, 'update'])->name('role.update');
        Route::delete('/role',[RoleController::class, 'delete'])->name('role.delete');

        Route::get('/user',[UserController::class, 'index'])->name('user');
        Route::post('/user',[UserController::class, 'store'])->name('user.store');
        Route::post('/user/assign',[UserController::class, 'assign'])->name('user.assign');
        Route::delete('/user',[UserController::class, 'delete'])->name('user.delete');
    });

    Route::middleware(['role:Admin|Atasan'])->group(function () {
        Route::get('/jadwal',[JadwalController::class, 'index'])->name('jadwal');
        Route::post('/jadwal',[JadwalController::class, 'store'])->name('jadwal.store');
        Route::patch('/jadwal',[JadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal',[JadwalController::class, 'delete'])->name('jadwal.delete');
    });

    Route::get('/aktivitas',[AktivitasController::class, 'index'])->name('aktivitas');
    Route::post('/aktivitas',[AktivitasController::class, 'store'])->name('aktivitas.store');
    Route::patch('/aktivitas',[AktivitasController::class, 'update'])->name('aktivitas.update');
    Route::delete('/aktivitas',[AktivitasController::class, 'delete'])->name('aktivitas.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
