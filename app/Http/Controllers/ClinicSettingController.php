<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClinicSettingRequest;
use App\Models\ClinicSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class ClinicSettingController extends BaseCrudController
{
    protected string $modelClass = ClinicSetting::class;
    protected string $routeName = 'settings';
    protected string $viewPrefix = 'settings';
    protected string $title = 'Configuracoes';
    protected string $singularTitle = 'Configuracao';
    protected array $with = ['clinic'];
    protected array $searchable = ['group', 'key', 'value'];

    public function store(ClinicSettingRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(ClinicSettingRequest $request, string $setting): RedirectResponse
    {
        return $this->updateRecord($request, ClinicSetting::query()->findOrFail($setting));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Grupo', 'key' => 'group'],
            ['label' => 'Chave', 'key' => 'key'],
            ['label' => 'Valor', 'key' => 'value'],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'group', 'label' => 'Grupo', 'type' => 'text', 'default' => 'geral', 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'key', 'label' => 'Chave', 'type' => 'text', 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'value', 'label' => 'Valor', 'type' => 'textarea', 'col' => 'col-12'],
        ];
    }
}
