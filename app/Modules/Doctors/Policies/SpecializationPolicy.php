<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Policies;

use App\Modules\Access\Policies\BasePolicy;
use App\Modules\Auth\Models\User;
use App\Modules\Doctors\Models\Specialization;

class SpecializationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'specializations.view');
    }

    public function view(User $user, Specialization $specialization): bool
    {
        return $this->allows($user, 'specializations.view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'specializations.manage');
    }

    public function update(User $user, Specialization $specialization): bool
    {
        return $this->allows($user, 'specializations.manage');
    }

    public function delete(User $user, Specialization $specialization): bool
    {
        return $this->allows($user, 'specializations.manage');
    }
}
