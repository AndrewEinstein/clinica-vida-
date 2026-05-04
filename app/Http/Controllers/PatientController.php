<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class PatientController extends BaseCrudController
{
    protected string $modelClass = Patient::class;
    protected string $routeName = 'patients';
    protected string $viewPrefix = 'patients';
    protected string $title = 'Pacientes';
    protected string $singularTitle = 'Paciente';
    protected array $with = ['clinic', 'insuranceProvider'];
    protected array $searchable = ['name', 'cpf', 'rg', 'email', 'phone'];

    public function store(PatientRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(PatientRequest $request, string $patient): RedirectResponse
    {
        return $this->updateRecord($request, Patient::query()->findOrFail($patient));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Nome', 'key' => 'name'],
            ['label' => 'CPF', 'key' => 'cpf'],
            ['label' => 'Telefone', 'key' => 'phone'],
            ['label' => 'Convenio', 'key' => 'insuranceProvider.name'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => Patient::statusOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'insurance_provider_id', 'label' => 'Convenio', 'type' => 'select', 'options' => ['' => 'Particular'] + $this->insuranceOptions(), 'col' => 'col-md-4'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Patient::statusOptions(), 'default' => 'active', 'col' => 'col-md-4'],
            ['name' => 'name', 'label' => 'Nome completo', 'type' => 'text', 'required' => true, 'col' => 'col-md-6'],
            ['name' => 'cpf', 'label' => 'CPF', 'type' => 'text', 'required' => true, 'col' => 'col-md-3'],
            ['name' => 'rg', 'label' => 'RG', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'birth_date', 'label' => 'Data de nascimento', 'type' => 'date', 'col' => 'col-md-3'],
            ['name' => 'sex', 'label' => 'Sexo', 'type' => 'select', 'options' => ['' => 'Nao informado'] + Patient::sexOptions(), 'col' => 'col-md-3'],
            ['name' => 'phone', 'label' => 'Telefone', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'whatsapp', 'label' => 'WhatsApp', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'col' => 'col-md-6'],
            ['name' => 'address', 'label' => 'Endereco', 'type' => 'text', 'col' => 'col-md-6'],
            ['name' => 'notes', 'label' => 'Observacoes', 'type' => 'textarea', 'col' => 'col-12'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Patient::statusOptions()],
            ['name' => 'sex', 'label' => 'Sexo', 'type' => 'select', 'options' => Patient::sexOptions()],
        ];
    }
}
