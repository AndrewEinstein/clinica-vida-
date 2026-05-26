<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\ItTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItTicketRequest extends FormRequest
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
            'requester_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'type' => ['required', Rule::in(array_keys(ItTicket::typeOptions()))],
            'priority' => ['required', Rule::in(array_keys(ItTicket::priorityOptions()))],
            'status' => ['required', Rule::in(array_keys(ItTicket::statusOptions()))],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'resolution_notes' => ['nullable', 'string'],
        ];
    }
}

