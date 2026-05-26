<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'appointments.manage';

    protected ?string $permissionManageKey = 'appointments.manage';

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
        User::ROLE_DOCTOR,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
    ];

    public function changeStatus(User $user, Appointment $appointment): bool
    {
        return $this->update($user, $appointment);
    }
}
