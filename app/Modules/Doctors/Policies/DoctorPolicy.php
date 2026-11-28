<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Policies;

use App\Modules\Access\Policies\BasePolicy;
use App\Modules\Auth\Models\User;
use App\Modules\Doctors\Models\Doctor;

class DoctorPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'doctors.view');
    }

    public function view(User $user, Doctor $doctor): bool
    {
        return $this->allows($user, 'doctors.view');
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) && $this->allows($user, 'doctors.create');
    }

    public function update(User $user, Doctor $doctor): bool
    {
        if ($this->isAdmin($user) && $this->allows($user, 'doctors.update')) {
            return true;
        }

        return $this->allows($user, 'doctors.update') && $this->owns($user, $doctor);
    }

    public function updateStatus(User $user, Doctor $doctor): bool
    {
        return $this->isAdmin($user) && $this->allows($user, 'doctors.deactivate');
    }
}
