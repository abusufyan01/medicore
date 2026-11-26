<?php

declare(strict_types=1);

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

uses(RefreshDatabase::class);

/**
 * Public endpoints that intentionally do not require authentication or permission middleware.
 *
 * @return list<string>
 */
function publicApiPaths(): array
{
    return [
        'api/v1/health',
        'api/v1/auth/login',
        'api/v1/auth/forgot-password',
        'api/v1/auth/reset-password',
        'api/v1/auth/register',
        'api/v1/auth/verify-email/{id}/{hash}',
        'api/v1/auth/resend-verification',
    ];
}

/**
 * Self-service authenticated endpoints available to any logged-in user regardless of role.
 *
 * @return list<string>
 */
function selfServiceAuthenticatedPaths(): array
{
    return [
        'api/v1/auth/me',
        'api/v1/auth/logout',
    ];
}

it('enforces auth:sanctum and permission middleware on every protected v1 route', function () {
    $routes = collect(RouteFacade::getRoutes()->getRoutes())
        ->filter(fn (Route $route): bool => str_starts_with($route->uri(), 'api/v1/'));

    expect($routes)->not->toBeEmpty();

    foreach ($routes as $route) {
        $uri = $route->uri();

        if (in_array($uri, publicApiPaths(), true)) {
            continue;
        }

        $middlewares = $route->gatherMiddleware();

        expect($middlewares)->toContain('auth:sanctum');

        if (in_array($uri, selfServiceAuthenticatedPaths(), true)) {
            continue;
        }

        $hasPermissionGate = collect($middlewares)
            ->contains(fn (string $m): bool => str_starts_with($m, 'permission:'));

        expect($hasPermissionGate)->toBeTrue();
    }
});

it('returns 401 for guests, 403 for unauthorized roles, and 200 for authorized roles', function () {
    $this->seed(DatabaseSeeder::class);

    // 1. Guest -> 401
    $this->getJson('/api/v1/access/check')
        ->assertUnauthorized()
        ->assertJsonPath('error.code', 'UNAUTHENTICATED');

    // 2. Patient (lacks roles.view) -> 403
    $patient = User::factory()->create();
    $patient->roles()->attach(Role::where('name', 'patient')->firstOrFail());

    $this->actingAs($patient)
        ->getJson('/api/v1/access/check')
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');

    // 3. Admin (has roles.view) -> 200
    $admin = User::where('email', config('hms.admin_email'))->firstOrFail();

    $this->actingAs($admin)
        ->getJson('/api/v1/access/check')
        ->assertOk()
        ->assertJsonPath('data.authorized', true);
});
