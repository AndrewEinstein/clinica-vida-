<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'users.view';

    protected ?string $permissionCreateKey = 'users.create';

    protected ?string $permissionUpdateKey = 'users.edit';

    protected ?string $permissionDeleteKey = 'users.delete';

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
