<?php

namespace App\Policies;

use App\Models\User;

class MedicalRecordPolicy extends ClinicScopedPolicy
{
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
