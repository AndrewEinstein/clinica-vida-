<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        $roles = array_keys(User::roleOptions());

        if (! $this->user()?->isSuperAdmin()) {
            $roles = array_values(array_diff($roles, [User::ROLE_SUPER_ADMIN]));
        }

        return [
            'clinic_id' => $this->user()?->isSuperAdmin()
                ? ['nullable', 'required_unless:role,'.User::ROLE_SUPER_ADMIN, 'integer', 'exists:clinics,id']
                : ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$userId ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in($roles)],
            'status' => ['required', Rule::in(array_keys(User::statusOptions()))],
        ];
    }
}
