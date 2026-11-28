<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Http\Requests;

use App\Modules\Doctors\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
        /** @var Department $department */
        $department = $this->route('department');

        return [
            'code' => ['sometimes', 'required', 'string', 'min:2', 'max:20', Rule::unique('departments', 'code')->ignore($department->id)],
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:120', Rule::unique('departments', 'name')->ignore($department->id)],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
