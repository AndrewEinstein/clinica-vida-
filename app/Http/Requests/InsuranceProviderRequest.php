<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\InsuranceProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InsuranceProviderRequest extends FormRequest
{
    use ResolvesClinic;

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'clinic_id' => $this->clinicRule(),
            'name' => ['required', 'string', 'max:255'],
            'ans_code' => ['nullable', 'string', 'max:40'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'coverage_notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys(InsuranceProvider::statusOptions()))],
        ];
    }
}
