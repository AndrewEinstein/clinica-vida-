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

    public function viewAny(User $user): bool
    {
        return $this->hasAnyRole($user, $this->viewRoles);
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user) && $this->sameClinic($user, $model);
    }

    public function create(User $user): bool
    {
        return $this->hasAnyRole($user, $this->manageRoles);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->hasAnyRole($user, $this->manageRoles) && $this->sameClinic($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->hasAnyRole($user, $this->deleteRoles) && $this->sameClinic($user, $model);
    }

    protected function hasAnyRole(User $user, array $roles): bool
    {
        return $user->isSuperAdmin() || $user->hasAnyRole($roles);
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
