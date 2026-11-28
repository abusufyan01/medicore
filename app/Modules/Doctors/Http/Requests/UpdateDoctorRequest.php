<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Http\Requests;

use App\Modules\Doctors\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Doctor $doctor */
        $doctor = $this->route('doctor');

        return [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:150'],
            'phone' => ['sometimes', 'required', 'string', 'min:7', 'max:30'],
            'department_id' => ['sometimes', 'required', 'integer', 'exists:departments,id'],
            'specialization_id' => ['sometimes', 'required', 'integer', 'exists:specializations,id'],
            'license_number' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:60',
                Rule::unique('doctors', 'license_number')->ignore($doctor->id),
            ],
            'qualification' => ['sometimes', 'nullable', 'string', 'max:150'],
            'experience_years' => ['sometimes', 'integer', 'min:0', 'max:70'],
            'consultation_fee' => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999999.99'],
            'slot_duration_minutes' => ['sometimes', 'required', 'integer', 'min:5', 'max:120'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
