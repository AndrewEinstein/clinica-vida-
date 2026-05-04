<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesClinic;
use App\Models\FinancialTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinancialTransactionRequest extends FormRequest
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
            'patient_id' => ['nullable', 'integer', $this->clinicScopedExists('patients')],
            'appointment_id' => ['nullable', 'integer', $this->clinicScopedExists('appointments')],
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(FinancialTransaction::typeOptions()))],
            'category' => ['nullable', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(array_keys(FinancialTransaction::statusOptions()))],
            'due_date' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'payment_method' => ['nullable', Rule::in(array_keys(FinancialTransaction::paymentMethodOptions()))],
        ];
    }
}
