<?php

namespace App\Policies;

use App\Models\User;

class ClinicSettingPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'settings.view';

    protected ?string $permissionCreateKey = 'settings.edit';

    protected ?string $permissionUpdateKey = 'settings.edit';

    protected ?string $permissionDeleteKey = 'settings.edit';

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
