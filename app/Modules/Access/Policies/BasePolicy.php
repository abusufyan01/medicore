<?php

declare(strict_types=1);

namespace App\Modules\Access\Policies;

use App\Modules\Auth\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    use HandlesAuthorization;

    protected function isAdmin(User $user): bool
    {
        return $user->is_active && $user->hasRole('admin');
    }

    protected function allows(User $user, string $permission): bool
    {
        return $user->is_active && $user->hasPermission($permission);
    }

    /**
     * Verify whether the authenticated user owns the target model via a foreign key column.
     */
    protected function owns(User $user, Model $record, string $ownerColumn = 'user_id'): bool
    {
        if (! $user->is_active) {
            return false;
        }

        $ownerId = $record->getAttribute($ownerColumn);

        return is_numeric($ownerId) && (int) $ownerId === $user->id;
    }

    /**
     * Allow if the user is a non-patient staff member with the permission,
     * OR if they are a patient who owns the record.
     */
    protected function allowsStaffOrOwner(
        User $user,
        string $permission,
        Model $record,
        string $ownerColumn = 'user_id',
    ): bool {
        if (! $this->allows($user, $permission)) {
            return false;
        }

        if ($user->hasRole('patient') && $user->roles()->count() === 1) {
            return $this->owns($user, $record, $ownerColumn);
        }

        return true;
    }
}
