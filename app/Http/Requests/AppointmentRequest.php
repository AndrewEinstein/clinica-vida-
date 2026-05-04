<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
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
            'doctor_id' => ['required', 'integer', $this->clinicScopedExists('doctors')],
            'insurance_provider_id' => ['nullable', 'integer', $this->clinicScopedExists('insurance_providers')],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:10', 'max:480'],
            'type' => ['required', 'string', 'max:80'],
            'reason' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Appointment::statusOptions()))],
            'notes' => ['nullable', 'string'],
            'cancellation_reason' => ['nullable', 'string'],
        ];
    }
}
