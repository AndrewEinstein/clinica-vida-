<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    use ResolvesClinic;

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $patientId = $this->route('patient');

        return [
            'clinic_id' => $this->clinicRule(),
            'insurance_provider_id' => ['nullable', 'integer', $this->clinicScopedExists('insurance_providers')],
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'max:20', $this->clinicScopedUnique('patients', 'cpf', $patientId)],
            'rg' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date'],
            'sex' => ['nullable', Rule::in(array_keys(Patient::sexOptions()))],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys(Patient::statusOptions()))],
        ];
    }
}
