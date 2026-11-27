<?php

declare(strict_types=1);

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

uses(RefreshDatabase::class);

it('enforces auth:sanctum and permission middleware on every protected v1 route', function () {
    $publicPaths = [
        'api/v1/health',
        'api/v1/auth/login',
        'api/v1/auth/forgot-password',
        'api/v1/auth/reset-password',
        'api/v1/auth/register',
        'api/v1/auth/verify-email/{id}/{hash}',
        'api/v1/auth/resend-verification',
    ];

    $selfServicePaths = [
        'api/v1/auth/me',
        'api/v1/auth/logout',
    ];

    $routes = collect(RouteFacade::getRoutes()->getRoutes())
        ->filter(fn (Route $route): bool => str_starts_with($route->uri(), 'api/v1/'));

    expect($routes)->not->toBeEmpty();

    foreach ($routes as $route) {
        $uri = $route->uri();

        if (in_array($uri, $publicPaths, true)) {
            continue;
        }

        $middlewares = $route->gatherMiddleware();

        expect(in_array('auth:sanctum', $middlewares, true))
            ->toBeTrue("Route [{$uri}] is missing auth:sanctum middleware.");

        if (in_array($uri, $selfServicePaths, true)) {
            continue;
        }

        $hasPermissionGate = collect($middlewares)
            ->contains(fn (string $m): bool => str_starts_with($m, 'permission:'));

        expect($hasPermissionGate)
            ->toBeTrue("Route [{$uri}] is missing a permission:<name> middleware gate.");
    }
});

it('enforces the Phase 1 role matrix across guest and all five system roles', function () {
    $this->seed(DatabaseSeeder::class);

    $createUserWithRole = function (string $roleName): User {
        $user = User::factory()->create();
        $role = Role::where('name', $roleName)->firstOrFail();
        $user->roles()->attach($role);

        return $user;
    };

    $roles = [
        'admin' => User::where('email', config('hms.admin_email'))->firstOrFail(),
        'receptionist' => $createUserWithRole('receptionist'),
        'doctor' => $createUserWithRole('doctor'),
        'accountant' => $createUserWithRole('accountant'),
        'patient' => $createUserWithRole('patient'),
    ];

    $matrix = [
        '/api/v1/users' => ['admin'],
        '/api/v1/settings' => ['admin'],
        '/api/v1/audit-logs' => ['admin', 'accountant'],
    ];

    foreach ($matrix as $endpoint => $allowedRoles) {
        // Guest -> 401
        $this->app['auth']->forgetGuards();
        $this->getJson($endpoint)
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');

        // All 5 system roles -> 200 if allowed, 403 otherwise
        foreach ($roles as $roleName => $user) {
            $response = $this->actingAs($user)->getJson($endpoint);

            if (in_array($roleName, $allowedRoles, true)) {
                $response->assertOk();
            } else {
                $response->assertForbidden()
                    ->assertJsonPath('error.code', 'FORBIDDEN');
            }
        }
    }
}); 
