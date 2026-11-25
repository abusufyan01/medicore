<?php

declare(strict_types=1);

use App\Modules\Access\Models\Permission;
use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds all five system roles and all permissions from Section 8', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Role::pluck('name')->all())->toEqualCanonicalizing([
        'admin',
        'receptionist',
        'doctor',
        'accountant',
        'patient',
    ]);

    expect(Role::where('is_system', true)->count())->toBe(5);
    expect(Permission::count())->toBeGreaterThanOrEqual(45);
});

it('seeds the initial active admin user with every permission', function () {
    config()->set('hms.admin_email', 'admin@hospital.test');
    config()->set('hms.admin_password', 'StrongPass123!');

    $this->seed(DatabaseSeeder::class);

    $admin = User::where('email', 'admin@hospital.test')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->is_active)->toBeTrue()
        ->and($admin->hasRole('admin'))->toBeTrue()
        ->and($admin->permissionNames())->toEqualCanonicalizing(
            Permission::pluck('name')->all()
        );
});

it('assigns expected permissions and excludes forbidden ones for non-admin roles', function () {
    $this->seed(DatabaseSeeder::class);

    $receptionist = User::factory()->create();
    $receptionist->roles()->attach(Role::where('name', 'receptionist')->firstOrFail());

    expect($receptionist->hasPermission('patients.create'))->toBeTrue()
        ->and($receptionist->hasPermission('appointments.checkin'))->toBeTrue()
        ->and($receptionist->hasPermission('consultations.create'))->toBeFalse()
        ->and($receptionist->hasPermission('users.create'))->toBeFalse();

    $doctor = User::factory()->create();
    $doctor->roles()->attach(Role::where('name', 'doctor')->firstOrFail());

    expect($doctor->hasPermission('consultations.create'))->toBeTrue()
        ->and($doctor->hasPermission('prescriptions.create'))->toBeTrue()
        ->and($doctor->hasPermission('invoices.create'))->toBeFalse();
});