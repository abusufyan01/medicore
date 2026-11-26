<?php

declare(strict_types=1);

namespace App\Modules\Access\Http\Resources;

use App\Modules\Access\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Role
 */
class RoleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'is_system' => $this->is_system,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
