<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'users.manage';

    protected ?string $permissionManageKey = 'users.manage';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
    ];

    protected array $manageRoles = [
        User::ROLE_ADMIN,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
    ];
}
