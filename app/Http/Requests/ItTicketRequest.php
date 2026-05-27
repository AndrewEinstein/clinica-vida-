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
            'urgency' => ['nullable', Rule::in(array_keys(ItTicket::urgencyOptions()))],
            'impact' => ['nullable', Rule::in(array_keys(ItTicket::impactOptions()))],
            'status' => ['required', Rule::in(array_keys(ItTicket::statusOptions()))],
            'category' => ['nullable', 'string', 'max:120'],
            'subcategory' => ['nullable', 'string', 'max:120'],
            'requester_department' => ['nullable', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'resolution_notes' => ['nullable', 'string'],
            'sla_due_at' => ['nullable', 'date'],
            'attachments' => ['array'],
            'attachments.*' => ['file', 'max:5120'],
        ];
    }
}
