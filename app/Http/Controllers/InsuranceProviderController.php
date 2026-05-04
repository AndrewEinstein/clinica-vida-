<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsuranceProviderRequest;
use App\Models\InsuranceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class InsuranceProviderController extends BaseCrudController
{
    protected string $modelClass = InsuranceProvider::class;
    protected string $routeName = 'insurance-providers';
    protected string $viewPrefix = 'insurance-providers';
    protected string $title = 'Convenios';
    protected string $singularTitle = 'Convenio';
    protected array $searchable = ['name', 'ans_code', 'contact_name', 'email'];

    public function store(InsuranceProviderRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(InsuranceProviderRequest $request, string $insurance_provider): RedirectResponse
    {
        return $this->updateRecord($request, InsuranceProvider::query()->findOrFail($insurance_provider));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Nome', 'key' => 'name'],
            ['label' => 'Codigo ANS', 'key' => 'ans_code'],
            ['label' => 'Contato', 'key' => 'contact_name'],
            ['label' => 'Telefone', 'key' => 'phone'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => InsuranceProvider::statusOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'name', 'label' => 'Nome do convenio', 'type' => 'text', 'required' => true, 'col' => 'col-md-5'],
            ['name' => 'ans_code', 'label' => 'Codigo ANS', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'contact_name', 'label' => 'Contato', 'type' => 'text', 'col' => 'col-md-4'],
            ['name' => 'phone', 'label' => 'Telefone', 'type' => 'text', 'col' => 'col-md-4'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'col' => 'col-md-4'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => InsuranceProvider::statusOptions(), 'default' => 'active', 'col' => 'col-md-3'],
            ['name' => 'coverage_notes', 'label' => 'Coberturas e observacoes', 'type' => 'textarea', 'col' => 'col-12'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => InsuranceProvider::statusOptions()],
        ];
    }
}
