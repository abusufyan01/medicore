<?php

declare(strict_types=1);

use App\Modules\Access\Http\Controllers\AccessCheckController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'permission:roles.view'])
    ->get('/access/check', [AccessCheckController::class, 'rolesView'])
    ->name('v1.access.check');
