<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use Illuminate\Foundation\Http\FormRequest;

class ClinicSettingRequest extends FormRequest
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
            'group' => ['required', 'string', 'max:120'],
            'key' => ['required', 'string', 'max:120', $this->clinicScopedUnique('clinic_settings', 'key', $this->route('setting'))],
            'value' => ['nullable', 'string'],
        ];
    }
}
