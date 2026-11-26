<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Models\User;
use App\Shared\Audit\AuditLogger;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private const MAX_FAILED_ATTEMPTS = 5;

    private const LOCKOUT_MINUTES = 15;

    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function login(string $email, string $password, Request $request): User
    {
        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if ($user === null) {
            throw new DomainException(ErrorCode::Unauthenticated, 'Invalid credentials.');
        }

        if ($user->locked_until !== null && $user->locked_until->isFuture()) {
            throw new DomainException(
                ErrorCode::Forbidden,
                'Account is temporarily locked due to too many failed login attempts.'
            );
        }

        if (! $user->is_active) {
            throw new DomainException(ErrorCode::Forbidden, 'Your account has been deactivated.');
        }

        if (! Hash::check($password, $user->password)) {
            $attempts = $user->failed_login_count + 1;
            $user->failed_login_count = $attempts;

            if ($attempts >= self::MAX_FAILED_ATTEMPTS) {
                $user->locked_until = now()->addMinutes(self::LOCKOUT_MINUTES);
            }

            $user->save();

            $this->auditLogger->log(
                event: 'login_failed',
                auditable: $user,
                auditableId: $user->id,
                newValues: ['failed_login_count' => $attempts],
                userId: $user->id,
            );

            throw new DomainException(ErrorCode::Unauthenticated, 'Invalid credentials.');
        }

        if (! $user->hasVerifiedEmail()) {
            throw new DomainException(
                ErrorCode::Forbidden,
                'Please verify your email address before logging in.'
            );
        }

        $user->failed_login_count = 0;
        $user->locked_until = null;
        $user->last_login_at = now();
        $user->save();

        Auth::guard('web')->login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $this->auditLogger->log(
            event: 'login',
            auditable: $user,
            auditableId: $user->id,
            userId: $user->id,
        );

        return $user->load('roles.permissions');
    }

    public function logout(Request $request): void
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }
}
