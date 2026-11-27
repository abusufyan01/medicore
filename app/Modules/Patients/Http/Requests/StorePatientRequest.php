<?php

declare(strict_types=1);

namespace App\Modules\Patients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePatientRequest extends FormRequest
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
            'user_id' => ['nullable', 'integer', 'exists:users,id', 'unique:patients,user_id'],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
            'national_id' => ['nullable', 'string', 'max:50'],
            'blood_group' => ['nullable', 'string', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'address' => ['nullable', 'string', 'max:1000'],
            'guardian_name' => ['nullable', 'string', 'min:2', 'max:150'],
            'guardian_phone' => ['nullable', 'string', 'min:7', 'max:30'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'allergies' => ['nullable', 'string', 'max:2000'],
            'chronic_conditions' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $dobInput = $this->input('date_of_birth');

            if (! is_string($dobInput) || $dobInput === '') {
                return;
            }

            $dob = Carbon::parse($dobInput);

            if ($dob->age < 18) {
                if (trim((string) $this->input('guardian_name', '')) === '') {
                    $validator->errors()->add('guardian_name', 'Guardian name is required for patients under 18.');
                }

                if (trim((string) $this->input('guardian_phone', '')) === '') {
                    $validator->errors()->add('guardian_phone', 'Guardian phone is required for patients under 18.');
                }
            }
        });
    }
}
