<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentHealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Rindam WBK SIPANDU System Management
|--------------------------------------------------------------------------
*/

// PUSAT KOMANDO & DASHBOARD EKSEKUTIF
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// MODUL 1: PENGELOLAAN DATA SISWA PER SATDIK
Route::prefix('students')->name('students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('index');
    Route::get('/create', [StudentController::class, 'create'])->name('create');
    Route::post('/', [StudentController::class, 'store'])->name('store');
    Route::get('/import', [StudentController::class, 'importForm'])->name('import');
    Route::get('/import/template', [StudentController::class, 'downloadTemplate'])->name('import.template');
    Route::post('/import', [StudentController::class, 'processImport'])->name('import.process');
    Route::get('/{student}', [StudentController::class, 'show'])->name('show');
    Route::put('/{student}', [StudentController::class, 'update'])->name('update');
    Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    Route::patch('/{student}/status', [StudentController::class, 'updateStatus'])->name('update-status');
    Route::put('/{student}/status', [StudentController::class, 'updateStatus'])->name('update-status.put');
    Route::post('/{student}/reveal-sensitive', [StudentController::class, 'revealSensitive'])->name('reveal-sensitive');
});

// MODUL 2: PROGRAM PENDIDIKAN PER SATDIK (MENU MANDIRI)
Route::prefix('programs')->name('programs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ProgramController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\ProgramController::class, 'store'])->name('store');
    Route::get('/{program}', [\App\Http\Controllers\ProgramController::class, 'show'])->name('show');
    Route::put('/{program}', [\App\Http\Controllers\ProgramController::class, 'update'])->name('update');
    Route::delete('/{program}', [\App\Http\Controllers\ProgramController::class, 'destroy'])->name('destroy');
    Route::post('/{program}/classrooms', [\App\Http\Controllers\ClassroomController::class, 'store'])->name('classrooms.store');
});

// MODUL 2.1: PENGELOLAAN KOMPI & PELETON SECARA MANUAL
Route::prefix('classrooms')->name('classrooms.')->group(function () {
    Route::post('/', [\App\Http\Controllers\ClassroomController::class, 'store'])->name('store');
    Route::put('/{classroom}', [\App\Http\Controllers\ClassroomController::class, 'update'])->name('update');
    Route::delete('/{classroom}', [\App\Http\Controllers\ClassroomController::class, 'destroy'])->name('destroy');
});
Route::get('/api/programs/{program}/classrooms', [\App\Http\Controllers\ClassroomController::class, 'apiList'])->name('api.programs.classrooms');


// MODUL 3: KESEHATAN & REKAM MEDIS SERDIK (MENU TERPISAH)
Route::prefix('health')->name('health.')->group(function () {
    Route::get('/', [StudentHealthController::class, 'index'])->name('index');
    Route::get('/{student}', [StudentHealthController::class, 'show'])->name('show');
    Route::get('/{student}/edit', [StudentHealthController::class, 'edit'])->name('edit');
    Route::put('/{student}', [StudentHealthController::class, 'update'])->name('update');
});

Route::get('/audit-logs', [StudentController::class, 'auditLogs'])->name('students.audit-logs');

// MODUL 4: PENGATURAN SISTEM & PEJABAT PIMPINAN
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SettingController::class, 'index'])->name('index');
    Route::put('/', [\App\Http\Controllers\SettingController::class, 'update'])->name('update');
});

// Cascading API dropdown for AJAX Satdik -> Diklat Programs
Route::get('/api/satdiks/{satdik}/programs', [StudentController::class, 'getSatdikPrograms'])->name('api.satdik.programs');
