<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Access\Models\Permission;
use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsByGroup = [
            'users' => ['users.view', 'users.create', 'users.update', 'users.deactivate'],
            'roles' => ['roles.view', 'roles.manage'],
            'patients' => ['patients.view', 'patients.search', 'patients.create', 'patients.update', 'patients.deactivate'],
            'doctors' => ['doctors.view', 'doctors.create', 'doctors.update', 'doctors.deactivate'],
            'schedules' => ['schedules.view', 'schedules.manage'],
            'appointments' => ['appointments.view', 'appointments.create', 'appointments.reschedule', 'appointments.cancel', 'appointments.checkin'],
            'consultations' => ['consultations.view', 'consultations.create', 'consultations.update', 'consultations.complete'],
            'prescriptions' => ['prescriptions.view', 'prescriptions.create', 'prescriptions.cancel'],
            'medical_records' => ['medical_records.view', 'medical_records.create', 'medical_records.update'],
            'services' => ['services.view', 'services.manage'],
            'invoices' => ['invoices.view', 'invoices.create', 'invoices.update', 'invoices.issue', 'invoices.cancel'],
            'payments' => ['payments.view', 'payments.create', 'payments.refund'],
            'expenses' => ['expenses.view', 'expenses.create', 'expenses.void'],
            'wards' => ['wards.view', 'wards.manage'],
            'beds' => ['beds.view', 'beds.manage'],
            'admissions' => ['admissions.view', 'admissions.create', 'admissions.update', 'admissions.discharge'],
            'reports' => ['reports.clinical', 'reports.financial', 'reports.operational'],
            'audit_logs' => ['audit_logs.view'],
            'settings' => ['settings.view', 'settings.manage'],
            'notifications' => ['notifications.view'],
        ];

        $allPermissionIds = [];
        $permissionMap = [];

        foreach ($permissionsByGroup as $group => $names) {
            foreach ($names as $name) {
                $perm = Permission::firstOrCreate(
                    ['name' => $name],
                    ['group' => $group]
                );
                $permissionMap[$name] = $perm->id;
                $allPermissionIds[] = $perm->id;
            }
        }

        $roles = [
            'admin' => [
                'label' => 'Administrator',
                'permissions' => array_keys($permissionMap), // Admin holds all permissions
            ],
            'receptionist' => [
                'label' => 'Receptionist',
                'permissions' => [
                    'patients.view', 'patients.search', 'patients.create', 'patients.update',
                    'doctors.view', 'schedules.view',
                    'appointments.view', 'appointments.create', 'appointments.reschedule', 'appointments.cancel', 'appointments.checkin',
                    'services.view',
                    'invoices.view', 'invoices.create', 'invoices.update', 'invoices.issue',
                    'payments.view', 'payments.create',
                    'wards.view', 'beds.view',
                    'admissions.view', 'admissions.create', 'admissions.update', 'admissions.discharge',
                    'reports.operational',
                    'settings.view',
                    'notifications.view',
                ],
            ],
            'doctor' => [
                'label' => 'Doctor',
                'permissions' => [
                    'patients.view', 'patients.search',
                    'doctors.view', 'schedules.view', 'schedules.manage',
                    'appointments.view', 'appointments.cancel',
                    'consultations.view', 'consultations.create', 'consultations.update', 'consultations.complete',
                    'prescriptions.view', 'prescriptions.create', 'prescriptions.cancel',
                    'medical_records.view', 'medical_records.create', 'medical_records.update',
                    'services.view',
                    'wards.view', 'beds.view',
                    'admissions.view', 'admissions.update', 'admissions.discharge',
                    'reports.clinical',
                    'notifications.view',
                ],
            ],
            'accountant' => [
                'label' => 'Accountant',
                'permissions' => [
                    'patients.view', 'patients.search',
                    'doctors.view',
                    'appointments.view',
                    'services.view', 'services.manage',
                    'invoices.view', 'invoices.create', 'invoices.update', 'invoices.issue', 'invoices.cancel',
                    'payments.view', 'payments.create', 'payments.refund',
                    'expenses.view', 'expenses.create', 'expenses.void',
                    'reports.financial',
                    'settings.view',
                    'notifications.view',
                ],
            ],
            'patient' => [
                'label' => 'Patient',
                'permissions' => [
                    'patients.view', 'patients.create', 'patients.update',
                    'doctors.view', 'schedules.view',
                    'appointments.view', 'appointments.create', 'appointments.reschedule', 'appointments.cancel',
                    'consultations.view',
                    'prescriptions.view',
                    'medical_records.view',
                    'invoices.view',
                    'payments.view', 'payments.create',
                    'admissions.view',
                    'notifications.view',
                ],
            ],
        ];

        foreach ($roles as $slug => $definition) {
            $role = Role::updateOrCreate(
                ['name' => $slug],
                ['label' => $definition['label'], 'is_system' => true]
            );

            $ids = array_map(fn (string $p): int => $permissionMap[$p], $definition['permissions']);
            $role->permissions()->sync($ids);
        }

        // Seed first Admin account from config/env
        /** @var string $adminEmail */
        $adminEmail = config('hms.admin_email');
        /** @var string $adminPassword */
        $adminPassword = config('hms.admin_password');
        /** @var string $adminName */
        $adminName = config('hms.admin_name');

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => $adminPassword,
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
