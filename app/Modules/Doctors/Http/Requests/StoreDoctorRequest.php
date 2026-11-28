<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'specialization_id' => ['required', 'integer', 'exists:specializations,id'],
            'license_number' => ['required', 'string', 'min:3', 'max:60', 'unique:doctors,license_number'],
            'qualification' => ['nullable', 'string', 'max:150'],
            'experience_years' => ['sometimes', 'integer', 'min:0', 'max:70'],
            'consultation_fee' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'slot_duration_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
