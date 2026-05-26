<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class RoleController extends BaseCrudController
{
    protected string $modelClass = Role::class;
    protected string $routeName = 'roles';
    protected string $viewPrefix = 'roles';
    protected string $title = 'Perfis';
    protected string $singularTitle = 'Perfil';
    protected array $with = ['clinic'];
    protected array $searchable = ['key', 'name'];
    protected string $orderBy = 'name';
    protected string $orderDirection = 'asc';

    public function store(RoleRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(RoleRequest $request, string $role): RedirectResponse
    {
        return $this->updateRecord($request, Role::query()->findOrFail($role));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Chave', 'key' => 'key'],
            ['label' => 'Nome', 'key' => 'name'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => Role::statusOptions()],
            ['label' => 'Clinica', 'key' => 'clinic.name', 'super_admin' => true],
            ['label' => 'Sistema', 'key' => 'is_system', 'type' => 'badge', 'options' => [0 => 'Nao', 1 => 'Sim'], 'badges' => [0 => 'secondary', 1 => 'info']],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        $record = $record ?: new Role;

        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'key', 'label' => 'Chave', 'type' => 'text', 'required' => true, 'help' => 'Ex: suporte_ti, coordenador_ti', 'col' => 'col-md-4', 'readonly' => (bool) ($record->is_system ?? false)],
            ['name' => 'name', 'label' => 'Nome', 'type' => 'text', 'required' => true, 'col' => 'col-md-4', 'readonly' => (bool) ($record->is_system ?? false)],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Role::statusOptions(), 'default' => 'active', 'col' => 'col-md-4'],
            ['name' => 'description', 'label' => 'Descricao', 'type' => 'textarea', 'rows' => 3, 'col' => 'col-md-12'],
        ];
    }
}

