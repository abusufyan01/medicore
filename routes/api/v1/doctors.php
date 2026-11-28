<?php

declare(strict_types=1);

use App\Modules\Doctors\Http\Controllers\DepartmentController;
use App\Modules\Doctors\Http\Controllers\DoctorController;
use App\Modules\Doctors\Http\Controllers\SpecializationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::prefix('departments')->group(function (): void {
        Route::get('/', [DepartmentController::class, 'index'])
            ->middleware('permission:departments.view')
            ->name('v1.departments.index');

        Route::post('/', [DepartmentController::class, 'store'])
            ->middleware('permission:departments.manage')
            ->name('v1.departments.store');

        Route::get('/{department}', [DepartmentController::class, 'show'])
            ->middleware('permission:departments.view')
            ->name('v1.departments.show');

        Route::patch('/{department}', [DepartmentController::class, 'update'])
            ->middleware('permission:departments.manage')
            ->name('v1.departments.update');

        Route::delete('/{department}', [DepartmentController::class, 'destroy'])
            ->middleware('permission:departments.manage')
            ->name('v1.departments.destroy');
    });

    Route::prefix('specializations')->group(function (): void {
        Route::get('/', [SpecializationController::class, 'index'])
            ->middleware('permission:specializations.view')
            ->name('v1.specializations.index');

        Route::post('/', [SpecializationController::class, 'store'])
            ->middleware('permission:specializations.manage')
            ->name('v1.specializations.store');

        Route::get('/{specialization}', [SpecializationController::class, 'show'])
            ->middleware('permission:specializations.view')
            ->name('v1.specializations.show');

        Route::patch('/{specialization}', [SpecializationController::class, 'update'])
            ->middleware('permission:specializations.manage')
            ->name('v1.specializations.update');

        Route::delete('/{specialization}', [SpecializationController::class, 'destroy'])
            ->middleware('permission:specializations.manage')
            ->name('v1.specializations.destroy');
    });
    Route::prefix('doctors')->group(function (): void {
        Route::get('/', [DoctorController::class, 'index'])
            ->middleware('permission:doctors.view')
            ->name('v1.doctors.index');

        Route::post('/', [DoctorController::class, 'store'])
            ->middleware('permission:doctors.create')
            ->name('v1.doctors.store');

        Route::get('/{doctor}', [DoctorController::class, 'show'])
            ->middleware('permission:doctors.view')
            ->name('v1.doctors.show');

        Route::patch('/{doctor}', [DoctorController::class, 'update'])
            ->middleware('permission:doctors.update')
            ->name('v1.doctors.update');

        Route::patch('/{doctor}/status', [DoctorController::class, 'updateStatus'])
            ->middleware('permission:doctors.deactivate')
            ->name('v1.doctors.status');
    });
});
