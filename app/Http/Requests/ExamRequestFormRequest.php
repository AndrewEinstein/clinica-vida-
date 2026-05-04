<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\ExamRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamRequestFormRequest extends FormRequest
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
            'exam_name' => ['required', 'string', 'max:255'],
            'indication' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(array_keys(ExamRequest::priorityOptions()))],
            'status' => ['required', Rule::in(array_keys(ExamRequest::statusOptions()))],
            'requested_at' => ['nullable', 'date'],
            'result_notes' => ['nullable', 'string'],
        ];
    }
}
