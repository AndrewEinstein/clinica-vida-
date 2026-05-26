<?php

namespace App\Policies;

use App\Models\User;

class ExamRequestPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'exam-requests.manage';

    protected ?string $permissionManageKey = 'exam-requests.manage';

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
