<?php

namespace App\Policies;

use App\Models\User;

class PatientPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'patients.manage';

    protected ?string $permissionManageKey = 'patients.manage';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
        User::ROLE_NURSE,
        User::ROLE_DOCTOR,
        User::ROLE_FINANCE,
    ];

    protected array $manageRoles = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
        User::ROLE_NURSE,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
    ];
}
