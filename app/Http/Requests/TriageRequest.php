<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\Triage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TriageRequest extends FormRequest
{
    use ResolvesClinic;

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $triageId = $this->route('triage');
        $clinicId = $this->clinicId();

        return [
            'clinic_id' => $this->clinicRule(),
            'patient_id' => ['required', 'integer', $this->clinicScopedExists('patients')],
            'appointment_id' => ['nullable', 'integer', $this->clinicScopedExists('appointments'), Rule::unique('triages', 'appointment_id')->ignore($triageId)],
            'professional_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $clinicId ? $query->where('clinic_id', $clinicId) : $query)],
            'triaged_at' => ['nullable', 'date'],
            'chief_complaint' => ['required', 'string'],
            'symptoms' => ['nullable', 'string'],
            'blood_pressure' => ['nullable', 'string', 'max:30'],
            'heart_rate' => ['nullable', 'integer', 'min:0', 'max:300'],
            'respiratory_rate' => ['nullable', 'integer', 'min:0', 'max:120'],
            'temperature' => ['nullable', 'numeric', 'min:25', 'max:45'],
            'oxygen_saturation' => ['nullable', 'integer', 'min:0', 'max:100'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0.2', 'max:2.8'],
            'blood_glucose' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'pain_level' => ['nullable', 'integer', 'min:0', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'pre_existing_conditions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'risk_classification' => ['required', Rule::in(array_keys(Triage::riskOptions()))],
            'status' => ['required', Rule::in(array_keys(Triage::statusOptions()))],
        ];
    }
}
