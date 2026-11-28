<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Http\Resources;

use App\Modules\Doctors\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Doctor
 */
class DoctorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing(['user', 'department', 'specialization']);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'department_id' => $this->department_id,
            'department' => [
                'id' => $this->department->id,
                'code' => $this->department->code,
                'name' => $this->department->name,
            ],
            'specialization_id' => $this->specialization_id,
            'specialization' => [
                'id' => $this->specialization->id,
                'name' => $this->specialization->name,
            ],
            'license_number' => $this->license_number,
            'qualification' => $this->qualification,
            'experience_years' => $this->experience_years,
            'consultation_fee' => $this->consultation_fee,
            'slot_duration_minutes' => $this->slot_duration_minutes,
            'bio' => $this->bio,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
