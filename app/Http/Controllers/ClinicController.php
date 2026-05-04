<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClinicRequest;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class ClinicController extends BaseCrudController
{
    protected string $modelClass = Clinic::class;
    protected string $routeName = 'clinics';
    protected string $viewPrefix = 'clinics';
    protected string $title = 'Clinicas';
    protected string $singularTitle = 'Clinica';
    protected array $searchable = ['name', 'cnpj', 'email', 'city'];

    public function store(ClinicRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(ClinicRequest $request, string $clinic): RedirectResponse
    {
        return $this->updateRecord($request, Clinic::findOrFail($clinic));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Nome', 'key' => 'name'],
            ['label' => 'CNPJ', 'key' => 'cnpj'],
            ['label' => 'Cidade', 'key' => 'city'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => Clinic::statusOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'name', 'label' => 'Nome da clinica', 'type' => 'text', 'required' => true, 'col' => 'col-md-6'],
            ['name' => 'cnpj', 'label' => 'CNPJ', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Clinic::statusOptions(), 'default' => 'active', 'col' => 'col-md-3'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'col' => 'col-md-4'],
            ['name' => 'phone', 'label' => 'Telefone', 'type' => 'text', 'col' => 'col-md-4'],
            ['name' => 'whatsapp', 'label' => 'WhatsApp', 'type' => 'text', 'col' => 'col-md-4'],
            ['name' => 'address', 'label' => 'Endereco', 'type' => 'text', 'col' => 'col-md-8'],
            ['name' => 'city', 'label' => 'Cidade', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'state', 'label' => 'UF', 'type' => 'text', 'col' => 'col-md-1'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Clinic::statusOptions()],
        ];
    }
}
