<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Models\User;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetService
{
    public function sendResetLink(string $email): void
    {
        // Always returns neutrally regardless of whether the email exists.
        Password::broker()->sendResetLink(['email' => $email]);
    }

    public function reset(string $email, string $token, string $newPassword): void
    {
        $status = Password::broker()->reset(
            [
                'email' => $email,
                'token' => $token,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'must_change_password' => false,
                    'failed_login_count' => 0,
                    'locked_until' => null,
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new DomainException(
                ErrorCode::ValidationFailed,
                'This password reset link is invalid or has expired.',
                ['token' => ['The password reset token is invalid or expired.']]
            );
        }
    }
}
