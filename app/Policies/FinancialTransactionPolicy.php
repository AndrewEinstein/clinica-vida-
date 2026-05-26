<?php

namespace App\Policies;

use App\Models\User;

class FinancialTransactionPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'finance.view';

    protected ?string $permissionCreateKey = 'finance.create';

    protected ?string $permissionUpdateKey = 'finance.edit';

    protected ?string $permissionDeleteKey = 'finance.delete';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
        User::ROLE_FINANCE,
    ];

    protected array $manageRoles = [
        User::ROLE_ADMIN,
        User::ROLE_FINANCE,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
        User::ROLE_FINANCE,
    ];
}
