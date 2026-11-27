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

it('enforces the role matrix across guest and all five system roles', function () {
    $this->seed(DatabaseSeeder::class);

    $roles = ['admin', 'receptionist', 'doctor', 'accountant', 'patient'];
    $usersByRole = [];
    foreach ($roles as $roleName) {
        $usersByRole[$roleName] = $roleName === 'admin'
            ? User::where('email', config('hms.admin_email'))->firstOrFail()
            : createUserWithRole($roleName);
    }

    $matrix = [
        '/api/v1/users' => ['admin'],
        '/api/v1/settings' => ['admin'],
        '/api/v1/audit-logs' => ['admin'],
        '/api/v1/patients' => ['admin', 'receptionist', 'doctor', 'patient'],
    ];

    foreach ($matrix as $endpoint => $allowedRoles) {
        // Guest -> always 401
        $this->getJson($endpoint)
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');

        foreach ($roles as $roleName) {
            $response = $this->actingAs($usersByRole[$roleName])->getJson($endpoint);

            if (in_array($roleName, $allowedRoles, true)) {
                $response->assertOk();
            } else {
                $response->assertForbidden()
                    ->assertJsonPath('error.code', 'FORBIDDEN');
            }
        }
    }
});