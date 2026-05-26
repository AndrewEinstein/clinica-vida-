<?php

namespace App\Policies;

use App\Models\User;

class InsuranceProviderPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'insurance.view';

    protected ?string $permissionCreateKey = 'insurance.create';

    protected ?string $permissionUpdateKey = 'insurance.edit';

    protected ?string $permissionDeleteKey = 'insurance.delete';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
        User::ROLE_FINANCE,
    ];

    protected array $manageRoles = [
        User::ROLE_ADMIN,
        User::ROLE_FINANCE,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
    ];
}
