<?php

namespace App\Policies;

use App\Models\User;

class PatientPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'patients.view';

    protected ?string $permissionCreateKey = 'patients.create';

    protected ?string $permissionUpdateKey = 'patients.edit';

    protected ?string $permissionDeleteKey = 'patients.delete';

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
