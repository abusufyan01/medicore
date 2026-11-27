<?php

declare(strict_types=1);

namespace App\Modules\Patients\Policies;

use App\Modules\Access\Policies\BasePolicy;
use App\Modules\Auth\Models\User;
use App\Modules\Patients\Models\Patient;

class PatientPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'patients.view');
    }

    public function view(User $user, Patient $patient): bool
    {
        if (! $this->allows($user, 'patients.view')) {
            return false;
        }

        if ($this->isAdmin($user) || $user->hasRole('receptionist')) {
            return true;
        }

        if ($user->hasRole('doctor')) {
            // Stub: will check assigned appointments/admissions in Phases 3 and 7.
            return false;
        }

        if ($user->hasRole('patient')) {
            return $this->owns($user, $patient);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'patients.create');
    }

    public function update(User $user, Patient $patient): bool
    {
        if (! $this->allows($user, 'patients.update')) {
            return false;
        }

        if ($this->isAdmin($user) || $user->hasRole('receptionist')) {
            return true;
        }

        if ($user->hasRole('patient')) {
            return $this->owns($user, $patient);
        }

        return false;
    }

    public function updateStatus(User $user, Patient $patient): bool
    {
        if (! $this->allows($user, 'patients.update')) {
            return false;
        }

        return $this->isAdmin($user) || $user->hasRole('receptionist');
    }
}
