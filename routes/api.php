<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API routes: everything here is served under /api (see bootstrap/app.php)
|--------------------------------------------------------------------------
| Version 1 lives under /api/v1. Each module adds ONE file in routes/api/v1/
| and it is loaded automatically, so adding a module never edits this file.
| A future breaking change would add routes/api/v2.php beside it.
*/
Route::prefix('v1')->group(function (): void {
    $routeFiles = glob(base_path('routes/api/v1/*.php')) ?: [];

    foreach ($routeFiles as $routeFile) {
        require $routeFile;
    }
});
