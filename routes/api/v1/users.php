<?php

declare(strict_types=1);

use App\Modules\Auth\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('users')
    ->group(function (): void {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('v1.users.index');

        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:users.create')
            ->name('v1.users.store');

        Route::get('/{user}', [UserController::class, 'show'])
            ->middleware('permission:users.view')
            ->name('v1.users.show');

        Route::patch('/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.update')
            ->name('v1.users.update');

        Route::patch('/{user}/status', [UserController::class, 'updateStatus'])
            ->middleware('permission:users.deactivate')
            ->name('v1.users.status');

        Route::put('/{user}/roles', [UserController::class, 'syncRoles'])
            ->middleware('permission:roles.manage')
            ->name('v1.users.roles');
    });
