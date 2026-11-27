<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Auth\Models\User;
use App\Modules\Patients\Models\Patient;
use App\Modules\Patients\Policies\PatientPolicy;
use App\Shared\Exceptions\ErrorCode;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request): Limit {
            $email = strtolower((string) $request->input('email', ''));

            return Limit::perMinute(5)
                ->by($email.'|'.$request->ip())
                ->response(fn () => ApiResponse::error(ErrorCode::RateLimited));
        });

        ResetPassword::createUrlUsing(function (object $notifiable, string $token): string {
            /** @var string $frontendUrl */
            $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
            $email = $notifiable instanceof User ? $notifiable->email : '';

            return rtrim($frontendUrl, '/').'/reset-password?token='.$token.'&email='.urlencode($email);
        });

        Gate::policy(
            Patient::class,
            PatientPolicy::class,
        );
    }
}
