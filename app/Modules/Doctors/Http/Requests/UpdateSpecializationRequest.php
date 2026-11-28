<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Http\Requests;

use App\Modules\Doctors\Models\Specialization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecializationRequest extends FormRequest
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
        /** @var Specialization $specialization */
        $specialization = $this->route('specialization');

        return [
            'department_id' => ['sometimes', 'nullable', 'integer', 'exists:departments,id'],
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:120', Rule::unique('specializations', 'name')->ignore($specialization->id)],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
