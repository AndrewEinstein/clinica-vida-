<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\Prescription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrescriptionRequest extends FormRequest
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
            'patient_id' => ['required', 'integer', $this->clinicScopedExists('patients')],
            'appointment_id' => ['nullable', 'integer', $this->clinicScopedExists('appointments')],
            'doctor_id' => ['required', 'integer', $this->clinicScopedExists('doctors')],
            'medications' => ['required', 'string'],
            'instructions' => ['nullable', 'string'],
            'issued_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(Prescription::statusOptions()))],
        ];
    }
}
