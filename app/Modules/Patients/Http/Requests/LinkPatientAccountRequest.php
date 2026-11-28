<?php

declare(strict_types=1);

namespace App\Modules\Patients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkPatientAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'patient_number' => ['required', 'string', 'max:30'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
