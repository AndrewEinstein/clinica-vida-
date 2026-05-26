<?php

namespace App\Policies;

use App\Models\User;

class DoctorPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'doctors.manage';

    protected ?string $permissionManageKey = 'doctors.manage';

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
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
    ];
}
