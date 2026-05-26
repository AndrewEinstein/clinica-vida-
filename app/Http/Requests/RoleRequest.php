<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    use ResolvesClinic;

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'clinic_id' => $this->clinicRule(),
            'key' => [
                'required',
                'string',
                'max:64',
                'regex:/^[a-z0-9_\\-\\.]+$/',
                $this->clinicScopedUnique('roles', 'key', $roleId),
            ],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}

