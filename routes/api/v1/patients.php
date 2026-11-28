<?php

declare(strict_types=1);

use App\Modules\Patients\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('patients')
    ->group(function (): void {
        Route::post('/link-account', [PatientController::class, 'linkAccount'])
            ->middleware(['permission:patients.view', 'throttle:patient-link'])
            ->name('v1.patients.link-account');

        Route::get('/', [PatientController::class, 'index'])
            ->middleware('permission:patients.view')
            ->name('v1.patients.index');

        Route::post('/', [PatientController::class, 'store'])
            ->middleware('permission:patients.create')
            ->name('v1.patients.store');

        Route::get('/{patient}', [PatientController::class, 'show'])
            ->middleware('permission:patients.view')
            ->name('v1.patients.show');

        Route::patch('/{patient}', [PatientController::class, 'update'])
            ->middleware('permission:patients.update')
            ->name('v1.patients.update');

        Route::patch('/{patient}/status', [PatientController::class, 'updateStatus'])
            ->middleware('permission:patients.update')
            ->name('v1.patients.status');

        Route::post('/{patient}/invite', [PatientController::class, 'invite'])
            ->middleware('permission:patients.update')
            ->name('v1.patients.invite');

        Route::get('/{patient}/timeline', [PatientController::class, 'timeline'])
            ->middleware('permission:patients.view')
            ->name('v1.patients.timeline');
    });
