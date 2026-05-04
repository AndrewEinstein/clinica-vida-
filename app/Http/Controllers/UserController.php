<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class UserController extends BaseCrudController
{
    protected string $modelClass = User::class;
    protected string $routeName = 'users';
    protected string $viewPrefix = 'users';
    protected string $title = 'Usuarios';
    protected string $singularTitle = 'Usuario';
    protected array $with = ['clinic'];
    protected array $searchable = ['name', 'email'];

    public function store(UserRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(UserRequest $request, string $user): RedirectResponse
    {
        return $this->updateRecord($request, User::query()->findOrFail($user));
    }

    protected function baseQuery(): Builder
    {
        $query = User::query()->with($this->with);

        if (! auth()->user()->isSuperAdmin()) {
            $query->where('clinic_id', auth()->user()->clinic_id);
        }

        return $query;
    }

    protected function prepareData(array $data, ?Model $record = null): array
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        unset($data['password_confirmation']);

        if (! auth()->user()->isSuperAdmin()) {
            $data['clinic_id'] = auth()->user()->clinic_id;
            $data['role'] = $data['role'] === User::ROLE_SUPER_ADMIN ? User::ROLE_ADMIN : $data['role'];
        }

        if (($data['role'] ?? null) === User::ROLE_SUPER_ADMIN) {
            $data['clinic_id'] = null;
        }

        return $data;
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Nome', 'key' => 'name'],
            ['label' => 'E-mail', 'key' => 'email'],
            ['label' => 'Perfil', 'key' => 'role', 'type' => 'status', 'options' => User::roleOptions()],
            ['label' => 'Clinica', 'key' => 'clinic.name', 'super_admin' => true],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => User::statusOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        $fields = [];

        if (auth()->user()->isSuperAdmin()) {
            $fields[] = ['name' => 'clinic_id', 'label' => 'Clinica', 'type' => 'select', 'options' => ['' => 'Sem clinica (Super Admin)'] + $this->clinicOptions(), 'col' => 'col-md-4'];
        }

        return array_merge($fields, [
            ['name' => 'name', 'label' => 'Nome completo', 'type' => 'text', 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'role', 'label' => 'Perfil', 'type' => 'select', 'options' => User::roleOptions(), 'default' => User::ROLE_RECEPTIONIST, 'col' => 'col-md-3'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => User::statusOptions(), 'default' => 'active', 'col' => 'col-md-3'],
            ['name' => 'password', 'label' => $record?->exists ? 'Nova senha' : 'Senha', 'type' => 'password', 'col' => 'col-md-3'],
            ['name' => 'password_confirmation', 'label' => 'Confirmar senha', 'type' => 'password', 'col' => 'col-md-3'],
        ]);
    }

    protected function filters(): array
    {
        return [
            ['name' => 'role', 'label' => 'Perfil', 'type' => 'select', 'options' => User::roleOptions()],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => User::statusOptions()],
        ];
    }
}
