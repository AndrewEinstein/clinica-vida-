<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    use ResolvesClinic;

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $doctorId = $this->route('doctor');
        $clinicId = $this->clinicId();

        return [
            'clinic_id' => $this->clinicRule(),
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $clinicId ? $query->where('clinic_id', $clinicId) : $query)],
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'max:20', $this->clinicScopedUnique('doctors', 'cpf', $doctorId)],
            'rg' => ['nullable', 'string', 'max:30'],
            'crm' => [
                'required',
                'string',
                'max:30',
                Rule::unique('doctors', 'crm')
                    ->where(fn ($query) => $query->where('clinic_id', $clinicId)->where('crm_uf', $this->input('crm_uf')))
                    ->ignore($doctorId),
            ],
            'crm_uf' => ['required', 'string', 'size:2'],
            'specialty' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'working_hours' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys(Doctor::statusOptions()))],
        ];
    }
}
