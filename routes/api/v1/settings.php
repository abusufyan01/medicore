<?php

declare(strict_types=1);

use App\Modules\Settings\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('settings')
    ->group(function (): void {
        Route::get('/', [SettingsController::class, 'index'])
            ->middleware('permission:settings.view')
            ->name('v1.settings.index');

        Route::put('/', [SettingsController::class, 'update'])
            ->middleware('permission:settings.manage')
            ->name('v1.settings.update');
    });
