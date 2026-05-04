<?php

namespace App\Http\Requests;

use App\Models\Clinic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClinicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->route('clinic') ? 'update' : 'create', $this->route('clinic') ? Clinic::findOrFail($this->route('clinic')) : Clinic::class) ?? false;
    }

    public function rules(): array
    {
        $clinicId = $this->route('clinic');

        return [
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:20', Rule::unique('clinics', 'cnpj')->ignore($clinicId)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'size:2'],
            'status' => ['required', Rule::in(array_keys(Clinic::statusOptions()))],
        ];
    }
}
