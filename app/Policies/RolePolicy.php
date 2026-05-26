<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'roles.view';

    protected ?string $permissionCreateKey = 'roles.create';

    protected ?string $permissionUpdateKey = 'roles.edit';

    protected ?string $permissionDeleteKey = 'roles.delete';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
    ];

    protected array $manageRoles = [
        User::ROLE_ADMIN,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
    ];

    public function update(User $user, \Illuminate\Database\Eloquent\Model $model): bool
    {
        if ($model instanceof Role && $model->is_system && ! $user->isSuperAdmin()) {
            return false;
        }

        return parent::update($user, $model);
    }

    public function delete(User $user, \Illuminate\Database\Eloquent\Model $model): bool
    {
        if ($model instanceof Role && $model->is_system && ! $user->isSuperAdmin()) {
            return false;
        }

        return parent::delete($user, $model);
    }
}
