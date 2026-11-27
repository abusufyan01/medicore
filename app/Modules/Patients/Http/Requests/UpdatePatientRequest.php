<?php

declare(strict_types=1);

namespace App\Modules\Patients\Http\Requests;

use App\Modules\Patients\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
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
        /** @var Patient $patient */
        $patient = $this->route('patient');

        return [
            'user_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
                Rule::unique('patients', 'user_id')->ignore($patient->id),
            ],
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:150'],
            'phone' => ['sometimes', 'required', 'string', 'min:7', 'max:30'],
            'email' => ['sometimes', 'nullable', 'string', 'email', 'max:255'],
            'date_of_birth' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'gender' => ['sometimes', 'required', 'string', Rule::in(['male', 'female', 'other'])],
            'national_id' => ['sometimes', 'nullable', 'string', 'max:50'],
            'blood_group' => ['sometimes', 'nullable', 'string', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'address' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'emergency_contact_name' => ['sometimes', 'nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'allergies' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'chronic_conditions' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
