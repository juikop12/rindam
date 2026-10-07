<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentHealthController;
use App\Http\Controllers\SystemCleanupController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Rindam WBK SIPANDU System Management
|--------------------------------------------------------------------------
*/

// AUTENTIKASI SISTEM (LOGIN & LOGOUT)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// AREA TERPROTEKSI OTENTIKASI & SESI PENGGUNA
Route::middleware('auth')->group(function () {

    // PUSAT KOMANDO & DASHBOARD EKSEKUTIF
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // MODUL 1: PENGELOLAAN DATA SISWA PER SATDIK
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/export-pdf', [StudentController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::get('/import', [StudentController::class, 'importForm'])->name('import');
        Route::get('/import/template', [StudentController::class, 'downloadTemplate'])->name('import.template');
        Route::post('/import', [StudentController::class, 'processImport'])->name('import.process');
        Route::get('/{student}/export-pdf', [StudentController::class, 'exportStudentPdf'])->name('export-student-pdf');
        Route::get('/{student}', [StudentController::class, 'show'])->name('show');
        Route::put('/{student}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
        Route::patch('/{student}/status', [StudentController::class, 'updateStatus'])->name('update-status');
        Route::put('/{student}/status', [StudentController::class, 'updateStatus'])->name('update-status.put');
        Route::post('/{student}/reveal-sensitive', [StudentController::class, 'revealSensitive'])
            ->middleware('throttle:10,1')
            ->name('reveal-sensitive');
    });

    // MODUL 2: PROGRAM PENDIDIKAN PER SATDIK (MENU MANDIRI)
    Route::prefix('programs')->name('programs.')->group(function () {
        Route::get('/', [ProgramController::class, 'index'])->name('index');
        Route::post('/', [ProgramController::class, 'store'])->name('store');
        Route::get('/{program}', [ProgramController::class, 'show'])->name('show');
        Route::put('/{program}', [ProgramController::class, 'update'])->name('update');
        Route::delete('/{program}', [ProgramController::class, 'destroy'])->name('destroy');
        Route::post('/{program}/classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');
    });

    // MODUL 2.1: PENGELOLAAN KOMPI & PELETON SECARA MANUAL
    Route::prefix('classrooms')->name('classrooms.')->group(function () {
        Route::post('/', [ClassroomController::class, 'store'])->name('store');
        Route::put('/{classroom}', [ClassroomController::class, 'update'])->name('update');
        Route::delete('/{classroom}', [ClassroomController::class, 'destroy'])->name('destroy');
    });
    Route::get('/api/programs/{program}/classrooms', [ClassroomController::class, 'apiList'])->name('api.programs.classrooms');

    // MODUL 3: KESEHATAN & REKAM MEDIS SERDIK (MENU TERPISAH)
    Route::prefix('health')->name('health.')->group(function () {
        Route::get('/', [StudentHealthController::class, 'index'])->name('index');
        Route::get('/export-pdf', [StudentHealthController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/{student}/export-pdf', [StudentHealthController::class, 'exportStudentPdf'])->name('export-student-pdf');
        Route::get('/{student}', [StudentHealthController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentHealthController::class, 'edit'])->name('edit');
        Route::put('/{student}', [StudentHealthController::class, 'update'])->name('update');
    });

    Route::get('/audit-logs', [StudentController::class, 'auditLogs'])->name('students.audit-logs');
    Route::get('/audit-logs/export-pdf', [StudentController::class, 'exportAuditLogsPdf'])->name('students.audit-logs.export-pdf');

    // MODUL 4: MANAJEMEN AKUN PENGGUNA & OTORITAS SATDIK (PIMPINAN / SUPER ADMIN)
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

    // MODUL 5: PENGATURAN SISTEM & PEJABAT PIMPINAN
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
        Route::get('/cleanup', [SystemCleanupController::class, 'index'])->name('cleanup');
        Route::post('/cleanup', [SystemCleanupController::class, 'destroy'])->name('cleanup.process');
    });

    // Cascading API dropdown for AJAX Satdik -> Diklat Programs
    Route::get('/api/satdiks/{satdik}/programs', [StudentController::class, 'getSatdikPrograms'])->name('api.satdik.programs');

});
