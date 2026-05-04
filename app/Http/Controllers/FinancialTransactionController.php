<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialTransactionRequest;
use App\Models\FinancialTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class FinancialTransactionController extends BaseCrudController
{
    protected string $modelClass = FinancialTransaction::class;
    protected string $routeName = 'finance';
    protected string $viewPrefix = 'finance';
    protected string $title = 'Financeiro';
    protected string $singularTitle = 'Lancamento financeiro';
    protected array $with = ['clinic', 'patient', 'appointment'];
    protected array $searchable = ['description', 'category', 'payment_method'];
    protected string $orderBy = 'due_date';

    public function store(FinancialTransactionRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(FinancialTransactionRequest $request, string $finance): RedirectResponse
    {
        return $this->updateRecord($request, FinancialTransaction::query()->findOrFail($finance));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Descricao', 'key' => 'description'],
            ['label' => 'Paciente', 'key' => 'patient.name'],
            ['label' => 'Tipo', 'key' => 'type', 'type' => 'status', 'options' => FinancialTransaction::typeOptions()],
            ['label' => 'Valor', 'key' => 'amount', 'type' => 'money'],
            ['label' => 'Pagamento', 'key' => 'payment_method', 'type' => 'status', 'options' => FinancialTransaction::paymentMethodOptions()],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => FinancialTransaction::statusOptions()],
            ['label' => 'Vencimento', 'key' => 'due_date', 'type' => 'date'],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'patient_id', 'label' => 'Paciente', 'type' => 'select', 'options' => ['' => 'Sem paciente'] + $this->patientOptions(), 'value' => request('patient_id'), 'col' => 'col-md-4'],
            ['name' => 'appointment_id', 'label' => 'Consulta', 'type' => 'select', 'options' => ['' => 'Sem consulta'] + $this->appointmentOptions(), 'value' => request('appointment_id'), 'col' => 'col-md-4'],
            ['name' => 'description', 'label' => 'Descricao', 'type' => 'text', 'required' => true, 'col' => 'col-md-6'],
            ['name' => 'category', 'label' => 'Categoria', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'amount', 'label' => 'Valor', 'type' => 'number', 'step' => '0.01', 'required' => true, 'col' => 'col-md-3'],
            ['name' => 'type', 'label' => 'Tipo', 'type' => 'select', 'options' => FinancialTransaction::typeOptions(), 'default' => 'revenue', 'col' => 'col-md-3'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => FinancialTransaction::statusOptions(), 'default' => 'pending', 'col' => 'col-md-3'],
            ['name' => 'due_date', 'label' => 'Vencimento', 'type' => 'date', 'col' => 'col-md-3'],
            ['name' => 'paid_at', 'label' => 'Pago em', 'type' => 'datetime-local', 'col' => 'col-md-3'],
            ['name' => 'payment_method', 'label' => 'Forma de pagamento', 'type' => 'select', 'options' => ['' => 'Selecione'] + FinancialTransaction::paymentMethodOptions(), 'col' => 'col-md-4'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'type', 'label' => 'Tipo', 'type' => 'select', 'options' => FinancialTransaction::typeOptions()],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => FinancialTransaction::statusOptions()],
            ['name' => 'payment_method', 'label' => 'Pagamento', 'type' => 'select', 'options' => FinancialTransaction::paymentMethodOptions()],
        ];
    }
}
