<?php

namespace App\Policies;

use App\Models\User;

class ExamRequestPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'exam-requests.view';

    protected ?string $permissionCreateKey = 'exam-requests.create';

    protected ?string $permissionUpdateKey = 'exam-requests.edit';

    protected ?string $permissionDeleteKey = 'exam-requests.delete';

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
