<?php

namespace App\Policies;

use App\Models\User;

class DoctorPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'doctors.view';

    protected ?string $permissionCreateKey = 'doctors.create';

    protected ?string $permissionUpdateKey = 'doctors.edit';

    protected ?string $permissionDeleteKey = 'doctors.delete';

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
