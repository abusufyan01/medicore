<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Resources\AuthenticatedUserResource;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Services\AuthService;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        /** @var string $email */
        $email = $request->validated('email');
        /** @var string $password */
        $password = $request->validated('password');

        $user = $this->authService->login($email, $password, $request);

        return ApiResponse::success(new AuthenticatedUserResource($user));
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success(new AuthenticatedUserResource($user));
    }

    public function logout(Request $request): Response
    {
        $this->authService->logout($request);

        return ApiResponse::noContent();
    }
}
