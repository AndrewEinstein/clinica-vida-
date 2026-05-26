<?php

namespace App\Policies;

use App\Models\User;

class ClinicSettingPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'settings.manage';

    protected ?string $permissionManageKey = 'settings.manage';

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
