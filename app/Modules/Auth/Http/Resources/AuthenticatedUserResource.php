<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Resources;

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class AuthenticatedUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing('roles.permissions');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'must_change_password' => $this->must_change_password,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'roles' => $this->roles->map(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'label' => $role->label,
            ])->values()->all(),
            'permissions' => $this->permissionNames(),
        ];
    }
}
