<?php

declare(strict_types=1);

use App\Shared\Audit\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'permission:audit_logs.view'])
    ->get('/audit-logs', [AuditLogController::class, 'index'])
    ->name('v1.audit-logs.index');
