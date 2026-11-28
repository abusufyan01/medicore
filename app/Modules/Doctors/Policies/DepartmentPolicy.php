<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Policies;

use App\Modules\Access\Policies\BasePolicy;
use App\Modules\Auth\Models\User;
use App\Modules\Doctors\Models\Department;

class DepartmentPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'departments.view');
    }

    public function view(User $user, Department $department): bool
    {
        return $this->allows($user, 'departments.view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'departments.manage');
    }

    public function update(User $user, Department $department): bool
    {
        return $this->allows($user, 'departments.manage');
    }

    public function delete(User $user, Department $department): bool
    {
        return $this->allows($user, 'departments.manage');
    }
}
