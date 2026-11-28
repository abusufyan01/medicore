<?php

declare(strict_types=1);

namespace App\Modules\Patients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitePatientPortalRequest extends FormRequest
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
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
        ];
    }
}
