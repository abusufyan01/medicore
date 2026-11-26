<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Modules\Auth\Models\User;
use App\Shared\Exceptions\ErrorCode;
use App\Shared\Support\Http\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(ErrorCode::Unauthenticated);
        }

        if (! $user->is_active) {
            return ApiResponse::error(ErrorCode::Forbidden, 'Your account has been deactivated.');
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        return ApiResponse::error(ErrorCode::Forbidden);
    }
}
