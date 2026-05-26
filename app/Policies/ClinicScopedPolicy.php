<?php

namespace App\Policies;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class ClinicScopedPolicy
{
    protected array $viewRoles = [];

    protected array $manageRoles = [];

    protected array $deleteRoles = [];

    protected ?string $permissionViewKey = null;

    protected ?string $permissionManageKey = null;

    public function viewAny(User $user): bool
    {
        return $this->isAllowed($user, $this->permissionViewKey, $this->viewRoles);
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user) && $this->sameClinic($user, $model);
    }

    public function create(User $user): bool
    {
        return $this->isAllowed($user, $this->permissionManageKey, $this->manageRoles);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->isAllowed($user, $this->permissionManageKey, $this->manageRoles) && $this->sameClinic($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->isAllowed($user, $this->permissionManageKey, $this->deleteRoles) && $this->sameClinic($user, $model);
    }

    protected function isAllowed(User $user, ?string $permissionKey, array $roles): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // If there is any mapping defined for this role, prefer the dynamic permission key.
        $hasMapping = \App\Models\RolePermission::query()->where('role', $user->role)->exists();
        if ($hasMapping && $permissionKey) {
            return $user->hasPermission($permissionKey);
        }

        return $user->hasAnyRole($roles);
    }

    protected function sameClinic(User $user, Model $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($model instanceof Clinic) {
            return (int) $model->id === (int) $user->clinic_id;
        }

        return (int) $model->clinic_id === (int) $user->clinic_id;
    }
}
