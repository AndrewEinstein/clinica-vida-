<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class DoctorController extends BaseCrudController
{
    protected string $modelClass = Doctor::class;
    protected string $routeName = 'doctors';
    protected string $viewPrefix = 'doctors';
    protected string $title = 'Medicos';
    protected string $singularTitle = 'Medico';
    protected array $with = ['clinic', 'user'];
    protected array $searchable = ['name', 'cpf', 'crm', 'specialty', 'email'];

    public function store(DoctorRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(DoctorRequest $request, string $doctor): RedirectResponse
    {
        return $this->updateRecord($request, Doctor::query()->findOrFail($doctor));
    }

    protected function prepareData(array $data, ?Model $record = null): array
    {
        $data = parent::prepareData($data, $record);
        $data['user_id'] = $data['user_id'] ?? null;
        $data['working_hours'] = ['description' => $data['working_hours'] ?? null];

        return $data;
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Nome', 'key' => 'name'],
            ['label' => 'CRM', 'key' => 'crm'],
            ['label' => 'UF', 'key' => 'crm_uf'],
            ['label' => 'Especialidade', 'key' => 'specialty'],
            ['label' => 'Consulta', 'key' => 'consultation_fee', 'type' => 'money'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => Doctor::statusOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        $hours = $record instanceof Doctor ? $record->workingHoursText() : null;

        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'user_id', 'label' => 'Usuario vinculado', 'type' => 'select', 'options' => ['' => 'Sem usuario'] + $this->userOptions([User::ROLE_DOCTOR]), 'col' => 'col-md-4'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Doctor::statusOptions(), 'default' => 'active', 'col' => 'col-md-4'],
            ['name' => 'name', 'label' => 'Nome completo', 'type' => 'text', 'required' => true, 'col' => 'col-md-6'],
            ['name' => 'cpf', 'label' => 'CPF', 'type' => 'text', 'required' => true, 'col' => 'col-md-3'],
            ['name' => 'rg', 'label' => 'RG', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'crm', 'label' => 'CRM', 'type' => 'text', 'required' => true, 'col' => 'col-md-3'],
            ['name' => 'crm_uf', 'label' => 'UF do CRM', 'type' => 'text', 'required' => true, 'col' => 'col-md-2'],
            ['name' => 'specialty', 'label' => 'Especialidade', 'type' => 'text', 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'consultation_fee', 'label' => 'Valor da consulta', 'type' => 'number', 'step' => '0.01', 'required' => true, 'col' => 'col-md-3'],
            ['name' => 'phone', 'label' => 'Telefone', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'whatsapp', 'label' => 'WhatsApp', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'col' => 'col-md-6'],
            ['name' => 'address', 'label' => 'Endereco', 'type' => 'text', 'col' => 'col-md-6'],
            ['name' => 'working_hours', 'label' => 'Dias e horarios de atendimento', 'type' => 'textarea', 'value' => $hours, 'col' => 'col-12'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Doctor::statusOptions()],
        ];
    }
}
