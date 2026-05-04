<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\MedicalCertificate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedicalCertificateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'rest_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'issued_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(MedicalCertificate::statusOptions()))],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,txt'],
        ];
    }
}
