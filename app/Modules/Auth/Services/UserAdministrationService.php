<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Notifications\UserAccountCreatedNotification;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAdministrationService
{
    /**
     * @param  array{search?: string, role?: string, is_active?: bool|string, per_page?: int|string}  $filters
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $perPage = min(max((int) ($filters['per_page'] ?? 15), 1), 100);

        return User::query()
            ->with('roles')
            ->when(
                isset($filters['search']) && $filters['search'] !== '',
                function (Builder $query) use ($filters): void {
                    $term = '%'.trim((string) $filters['search']).'%';
                    $query->where(function (Builder $q) use ($term): void {
                        $q->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term)
                            ->orWhere('phone', 'like', $term);
                    });
                }
            )
            ->when(
                isset($filters['role']) && $filters['role'] !== '',
                function (Builder $query) use ($filters): void {
                    $roleName = (string) $filters['role'];
                    $query->whereHas('roles', fn (Builder $q) => $q->where('name', $roleName));
                }
            )
            ->when(
                array_key_exists('is_active', $filters) && $filters['is_active'] !== '',
                function (Builder $query) use ($filters): void {
                    $active = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($active !== null) {
                        $query->where('is_active', $active);
                    }
                }
            )
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * @param  array{name: string, email: string, phone?: string|null, roles: list<string>}  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $temporaryPassword = Str::password(12, true, true, false, false);

            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->phone = $data['phone'] ?? null;
            $user->password = $temporaryPassword;
            $user->email_verified_at = now();
            $user->is_active = true;
            $user->must_change_password = true;
            $user->save();

            $roleIds = Role::whereIn('name', $data['roles'])->pluck('id')->all();
            $user->roles()->sync($roleIds);

            $user->notify(new UserAccountCreatedNotification($temporaryPassword));

            return $user->load('roles');
        });
    }

    /**
     * @param  array{name?: string, email?: string, phone?: string|null}  $data
     */
    public function update(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user->load('roles');
    }

    public function updateStatus(User $user, bool $isActive): User
    {
        return DB::transaction(function () use ($user, $isActive): User {
            /** @var User $lockedUser */
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            if (! $isActive && $lockedUser->is_active && $lockedUser->hasRole('admin')) {
                $this->ensureAnotherActiveAdminExists($lockedUser->id);
            }

            $lockedUser->is_active = $isActive;
            $lockedUser->save();

            return $lockedUser->load('roles');
        });
    }

    /**
     * @param  list<string>  $roleNames
     */
    public function syncRoles(User $user, array $roleNames): User
    {
        return DB::transaction(function () use ($user, $roleNames): User {
            /** @var User $lockedUser */
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $removingAdmin = $lockedUser->hasRole('admin') && ! in_array('admin', $roleNames, true);

            if ($removingAdmin && $lockedUser->is_active) {
                $this->ensureAnotherActiveAdminExists($lockedUser->id);
            }

            $roleIds = Role::whereIn('name', $roleNames)->pluck('id')->all();
            $lockedUser->roles()->sync($roleIds);

            return $lockedUser->load('roles');
        });
    }

    /**
     * BR-USR-02: Prevent removing or deactivating the last active Administrator.
     */
    private function ensureAnotherActiveAdminExists(int $excludedUserId): void
    {
        $otherActiveAdmins = User::where('id', '!=', $excludedUserId)
            ->where('is_active', true)
            ->whereHas('roles', fn (Builder $q) => $q->where('name', 'admin'))
            ->lockForUpdate()
            ->count();

        if ($otherActiveAdmins === 0) {
            throw new DomainException(
                ErrorCode::InvalidState,
                'Cannot deactivate or remove the admin role from the last active administrator.'
            );
        }
    }
}
