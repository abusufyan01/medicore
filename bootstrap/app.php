<?php

use App\Http\Middleware\CheckPermission;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use App\Shared\Support\Http\ApiResponse;
use App\Shared\Support\Http\AssignRequestId;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Runs first on every request, so even early failures carry a request id.
        $middleware->prepend(AssignRequestId::class);
        $middleware->statefulApi();
        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (DomainException $e) {
            return ApiResponse::error($e->errorCode, $e->getMessage(), $e->fields ?? []);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponse::error(ErrorCode::Unauthenticated);
        });

        $exceptions->render(function (ValidationException $e) {
            /** @var array<string, array<int, string>> $errors */
            $errors = $e->errors();

            return ApiResponse::error(
                ErrorCode::ValidationFailed,
                $e->getMessage(),
                $errors,
            );
        });
    })->create();
