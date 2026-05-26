<?php

namespace App\Policies;

use App\Models\User;

class FinancialTransactionPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'finance.manage';

    protected ?string $permissionManageKey = 'finance.manage';

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
