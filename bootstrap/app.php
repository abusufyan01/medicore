<?php

use App\Shared\Support\Http\AssignRequestId;
use App\Http\Middleware\CheckPermission;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(function (): void {
                    $routeFiles = glob(base_path('routes/api/v1/*.php')) ?: [];
                    foreach ($routeFiles as $routeFile) {
                        require $routeFile;
                    }
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->api(prepend: [
            AssignRequestId::class,
        ]);
        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (DomainException $e) {
            return ApiResponse::error($e->errorCode, $e->getMessage(), $e->fields);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponse::error(ErrorCode::Unauthenticated);
        });

        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e) {
            return ApiResponse::error(ErrorCode::Forbidden);
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

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) {
            return response()->json([
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'The requested resource was not found.',
                    'fields' => (object) [],
                    'request_id' => $request->header('X-Request-Id'),
                ],
            ], 404);
        });

        $exceptions->render(function (ThrottleRequestsException $e) {
            return ApiResponse::error(ErrorCode::RateLimited);
        });

        $exceptions->render(function (Throwable $e) {
            if (config('app.debug')) {
                return null;
            }

            return ApiResponse::error(ErrorCode::ServerError);
        });
    })->create();
