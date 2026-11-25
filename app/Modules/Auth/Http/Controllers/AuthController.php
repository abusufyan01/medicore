<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Http\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\RegisterPatientRequest;
use App\Modules\Auth\Http\Requests\ResetPasswordRequest;
use App\Modules\Auth\Http\Resources\AuthenticatedUserResource;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Auth\Services\PasswordResetService;
use App\Modules\Auth\Services\PatientRegistrationService;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly PasswordResetService $passwordResetService,
        private readonly PatientRegistrationService $patientRegistrationService,
    ) {}

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

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        /** @var string $email */
        $email = $request->validated('email');

        $this->passwordResetService->sendResetLink($email);

        return ApiResponse::success([
            'message' => 'If an account exists for that email address, a password reset link has been sent.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        /** @var string $email */
        $email = $request->validated('email');
        /** @var string $token */
        $token = $request->validated('token');
        /** @var string $password */
        $password = $request->validated('password');

        $this->passwordResetService->reset($email, $token, $password);

        return ApiResponse::success([
            'message' => 'Your password has been reset successfully.',
        ]);
    }

    public function register(RegisterPatientRequest $request): JsonResponse
    {
        /** @var array{name: string, email: string, phone: string, password: string} $validated */
        $validated = $request->validated();

        $user = $this->patientRegistrationService->register($validated);

        return ApiResponse::created([
            'user' => new AuthenticatedUserResource($user),
            'message' => 'Registration successful. Please verify your email address before logging in.',
        ]);
    }

    public function verifyEmail(int $id, string $hash): JsonResponse
    {
        $user = $this->patientRegistrationService->verifyEmail($id, $hash);

        return ApiResponse::success([
            'user' => new AuthenticatedUserResource($user),
            'message' => 'Email verified successfully. You may now log in.',
        ]);
    }

    public function resendVerification(ForgotPasswordRequest $request): JsonResponse
    {
        /** @var string $email */
        $email = $request->validated('email');

        $this->patientRegistrationService->resendVerification($email);

        return ApiResponse::success([
            'message' => 'If an unverified account exists for that email, a verification link has been sent.',
        ]);
    }
}
