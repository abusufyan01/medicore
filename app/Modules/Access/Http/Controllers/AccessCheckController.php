<?php

declare(strict_types=1);

namespace App\Modules\Access\Http\Controllers;

use App\Modules\Auth\Models\User;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccessCheckController
{
    public function rolesView(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success([
            'authorized' => true,
            'permission' => 'roles.view',
            'user_id' => $user->id,
        ]);
    }
}
