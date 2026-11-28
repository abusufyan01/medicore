<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Access\Models\Permission;
use App\Modules\Access\Models\Role;
use App\Modules\Doctors\Models\Department;
use App\Modules\Doctors\Models\Specialization;
use Illuminate\Database\Seeder;

class DepartmentSpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $viewDept = Permission::firstOrCreate(['name' => 'departments.view'], ['group' => 'doctors']);
        $manageDept = Permission::firstOrCreate(['name' => 'departments.manage'], ['group' => 'doctors']);
        $viewSpec = Permission::firstOrCreate(['name' => 'specializations.view'], ['group' => 'doctors']);
        $manageSpec = Permission::firstOrCreate(['name' => 'specializations.manage'], ['group' => 'doctors']);

        // All roles can read; only Admin can manage
        foreach (['admin', 'receptionist', 'doctor', 'accountant', 'patient'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role !== null) {
                $role->permissions()->syncWithoutDetaching([$viewDept->id, $viewSpec->id]);
            }
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole !== null) {
            $adminRole->permissions()->syncWithoutDetaching([$manageDept->id, $manageSpec->id]);
        }

        $starterData = [
            'GENM' => [
                'name' => 'General Medicine',
                'description' => 'Primary outpatient consultations and internal medicine care',
                'specializations' => [
                    ['name' => 'Internal Medicine', 'description' => 'Adult preventive and chronic disease management'],
                    ['name' => 'Family Medicine', 'description' => 'Comprehensive primary care across all ages'],
                ],
            ],
            'CARD' => [
                'name' => 'Cardiology',
                'description' => 'Heart and vascular diagnostics, outpatient care, and procedures',
                'specializations' => [
                    ['name' => 'Clinical Cardiology', 'description' => 'Non-invasive cardiac evaluation and heart failure care'],
                    ['name' => 'Interventional Cardiology', 'description' => 'Catheter-based coronary and structural heart procedures'],
                ],
            ],
            'PEDI' => [
                'name' => 'Pediatrics',
                'description' => 'Medical care for infants, children, and adolescents',
                'specializations' => [
                    ['name' => 'General Pediatrics', 'description' => 'Childhood illnesses, growth monitoring, and immunizations'],
                    ['name' => 'Neonatology', 'description' => 'Newborn intensive and premature infant care'],
                ],
            ],
            'ORTH' => [
                'name' => 'Orthopedics',
                'description' => 'Musculoskeletal, bone, joint, and trauma surgery',
                'specializations' => [
                    ['name' => 'Orthopedic Surgery', 'description' => 'Fracture care, joint replacement, and sports injuries'],
                ],
            ],
            'DERM' => [
                'name' => 'Dermatology',
                'description' => 'Skin, hair, and nail diagnostics and outpatient treatments',
                'specializations' => [
                    ['name' => 'Clinical Dermatology', 'description' => 'Medical dermatology and skin disorder management'],
                ],
            ],
            'GYNE' => [
                'name' => 'Obstetrics & Gynecology',
                'description' => 'Women’s reproductive health, maternity, and prenatal care',
                'specializations' => [
                    ['name' => 'Obstetrics & Gynecology', 'description' => 'Maternal care, obstetrics, and gynecological health'],
                ],
            ],
        ];

        foreach ($starterData as $code => $item) {
            $department = Department::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'is_active' => true,
                ]
            );

            foreach ($item['specializations'] as $spec) {
                Specialization::firstOrCreate(
                    ['name' => $spec['name']],
                    [
                        'department_id' => $department->id,
                        'description' => $spec['description'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
