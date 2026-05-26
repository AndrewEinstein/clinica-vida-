<?php

namespace App\Policies;

use App\Models\User;

class MedicalRecordPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'medical-records.view';

    protected ?string $permissionCreateKey = 'medical-records.create';

    protected ?string $permissionUpdateKey = 'medical-records.edit';

    protected ?string $permissionDeleteKey = 'medical-records.delete';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
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
