<?php

declare(strict_types=1);

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Doctors\Models\Department;
use App\Modules\Doctors\Models\Specialization;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows only Admin to create doctors while Receptionist can view and filter them', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::where('email', config('hms.admin_email'))->firstOrFail();

    $receptionist = User::factory()->create();
    $receptionist->roles()->attach(Role::where('name', 'receptionist')->firstOrFail());

    $department = Department::where('code', 'CARD')->firstOrFail();
    $specialization = Specialization::where('department_id', $department->id)->firstOrFail();

    $payload = [
        'name' => 'Dr. Zainab Khalid',
        'email' => 'zainab.cardio@hospital.local',
        'phone' => '03001239876',
        'department_id' => $department->id,
        'specialization_id' => $specialization->id,
        'license_number' => 'PMDC-55102-P',
        'qualification' => 'MBBS, FCPS (Cardiology)',
        'experience_years' => 9,
        'consultation_fee' => 2500.00,
        'slot_duration_minutes' => 20,
    ];

    // Receptionist cannot create -> 403
    $this->actingAs($receptionist)
        ->postJson('/api/v1/doctors', $payload)
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');

    // Admin creates -> 201
    $created = $this->actingAs($admin)
        ->postJson('/api/v1/doctors', $payload)
        ->assertCreated()
        ->assertJsonPath('data.license_number', 'PMDC-55102-P')
        ->assertJsonPath('data.consultation_fee', '2500.00');

    $doctorId = $created->json('data.id');

    // Receptionist can list, filter, and view -> 200
    $this->actingAs($receptionist)
        ->getJson("/api/v1/doctors?specialization_id={$specialization->id}&q=Zainab&is_active=1")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $doctorId);

    // Admin deactivates doctor safely -> 200
    $this->actingAs($admin)
        ->patchJson("/api/v1/doctors/{$doctorId}/status", ['is_active' => false])
        ->assertOk()
        ->assertJsonPath('data.is_active', false);
});
