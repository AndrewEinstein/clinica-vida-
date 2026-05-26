<?php

namespace App\Policies;

use App\Models\User;

class PrescriptionPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'prescriptions.view';

    protected ?string $permissionCreateKey = 'prescriptions.create';

    protected ?string $permissionUpdateKey = 'prescriptions.edit';

    protected ?string $permissionDeleteKey = 'prescriptions.delete';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
        User::ROLE_NURSE,
        User::ROLE_DOCTOR,
    ];

    protected array $manageRoles = [
        User::ROLE_DOCTOR,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
        User::ROLE_DOCTOR,
    ];
}
