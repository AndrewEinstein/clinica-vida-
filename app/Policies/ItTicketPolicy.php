<?php

namespace App\Policies;

use App\Models\ItTicket;
use App\Models\User;

class ItTicketPolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'it-tickets.manage';

    protected ?string $permissionManageKey = 'it-tickets.manage';

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
        User::ROLE_FINANCE,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
    ];

    public function comment(User $user, ItTicket $ticket): bool
    {
        return $this->view($user, $ticket);
    }
}

