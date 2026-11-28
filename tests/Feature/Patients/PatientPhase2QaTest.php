<?php

declare(strict_types=1);

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Patients\Models\Patient;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeUserWithRole(string $roleName): User
{
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('name', $roleName)->firstOrFail());

    return $user;
}

it('enforces T-04 so a patient can only access their own record and never another patient', function () {
    $this->seed(DatabaseSeeder::class);

    $receptionist = makeUserWithRole('receptionist');
    $patientUserA = makeUserWithRole('patient');
    $patientUserB = makeUserWithRole('patient');

    $patientA = Patient::create([
        'patient_number' => 'P-2026-000101',
        'user_id' => $patientUserA->id,
        'name' => 'Patient Alpha',
        'phone' => '03001110001',
        'date_of_birth' => '1992-04-12',
        'gender' => 'male',
        'is_active' => true,
    ]);

    $patientB = Patient::create([
        'patient_number' => 'P-2026-000102',
        'user_id' => $patientUserB->id,
        'name' => 'Patient Beta',
        'phone' => '03001110002',
        'date_of_birth' => '1995-09-20',
        'gender' => 'female',
        'is_active' => true,
    ]);

    // Patient A can read own profile and timeline
    $this->actingAs($patientUserA)
        ->getJson("/api/v1/patients/{$patientA->id}")
        ->assertOk()
        ->assertJsonPath('data.patient_number', 'P-2026-000101');

    $this->actingAs($patientUserA)
        ->getJson("/api/v1/patients/{$patientA->id}/timeline")
        ->assertOk();

    // Patient A index returns only Patient A
    $this->actingAs($patientUserA)
        ->getJson('/api/v1/patients')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $patientA->id);

    // T-04: Patient A is blocked (403) from reading, updating, or viewing timeline of Patient B
    $this->actingAs($patientUserA)
        ->getJson("/api/v1/patients/{$patientB->id}")
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');

    $this->actingAs($patientUserA)
        ->patchJson("/api/v1/patients/{$patientB->id}", ['phone' => '03009999999'])
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');

    $this->actingAs($patientUserA)
        ->getJson("/api/v1/patients/{$patientB->id}/timeline")
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');

    // Receptionist can access both
    $this->actingAs($receptionist)
        ->getJson("/api/v1/patients/{$patientB->id}")
        ->assertOk();
});

it('detects duplicate patients on phone+DOB or name+DOB unless confirm_duplicate is true', function () {
    $this->seed(DatabaseSeeder::class);

    $receptionist = makeUserWithRole('receptionist');

    $this->actingAs($receptionist)
        ->postJson('/api/v1/patients', [
            'name' => 'Bilal Ahmed',
            'phone' => '03007778899',
            'date_of_birth' => '1990-06-15',
            'gender' => 'male',
        ])
        ->assertCreated();

    // Same phone + DOB -> 409 DUPLICATE_PATIENT
    $this->actingAs($receptionist)
        ->postJson('/api/v1/patients', [
            'name' => 'Kamran Ahmed',
            'phone' => '03007778899',
            'date_of_birth' => '1990-06-15',
            'gender' => 'male',
        ])
        ->assertConflict()
        ->assertJsonPath('error.code', 'DUPLICATE_PATIENT');

    // Same name + DOB -> 409 DUPLICATE_PATIENT
    $this->actingAs($receptionist)
        ->postJson('/api/v1/patients', [
            'name' => 'bilal ahmed',
            'phone' => '03210001122',
            'date_of_birth' => '1990-06-15',
            'gender' => 'male',
        ])
        ->assertConflict()
        ->assertJsonPath('error.code', 'DUPLICATE_PATIENT');

    // Explicit confirm_duplicate=true succeeds
    $this->actingAs($receptionist)
        ->postJson('/api/v1/patients', [
            'name' => 'Kamran Ahmed',
            'phone' => '03007778899',
            'date_of_birth' => '1990-06-15',
            'gender' => 'male',
            'confirm_duplicate' => true,
        ])
        ->assertCreated();
});

it('requires guardian_name and guardian_phone for patients under 18', function () {
    $this->seed(DatabaseSeeder::class);

    $receptionist = makeUserWithRole('receptionist');
    $minorDob = now()->subYears(10)->format('Y-m-d');

    // Missing guardian fields -> 422
    $this->actingAs($receptionist)
        ->postJson('/api/v1/patients', [
            'name' => 'Hina Tariq',
            'phone' => '03004443322',
            'date_of_birth' => $minorDob,
            'gender' => 'female',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('error.code', 'VALIDATION_FAILED')
        ->assertJsonValidationErrors(['guardian_name', 'guardian_phone']);

    // With guardian fields -> 201
    $this->actingAs($receptionist)
        ->postJson('/api/v1/patients', [
            'name' => 'Hina Tariq',
            'phone' => '03004443322',
            'date_of_birth' => $minorDob,
            'gender' => 'female',
            'guardian_name' => 'Tariq Mahmood',
            'guardian_phone' => '03004443322',
        ])
        ->assertCreated()
        ->assertJsonPath('data.guardian_name', 'Tariq Mahmood');
});
