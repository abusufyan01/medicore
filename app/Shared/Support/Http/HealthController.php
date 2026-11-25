<?php

declare(strict_types=1);

namespace App\Shared\Support\Http;

use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/health: public check that the API answers in the standard shape.
 *
 * It deliberately reveals nothing about the environment, versions or database.
 * (Laravel's built-in /up route remains the plain liveness probe for servers.)
 */
final class HealthController
{
    public function __invoke(): JsonResponse
    {
        return ApiResponse::success([
            'status' => 'ok',
            'service' => 'hms-backend',
            'time' => now()->utc()->toIso8601ZuluString(),
        ]);
    }
}
