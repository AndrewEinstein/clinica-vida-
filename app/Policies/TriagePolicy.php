<?php

namespace App\Policies;

use App\Models\Triage;
use App\Models\User;

class TriagePolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'triages.view';

    protected ?string $permissionCreateKey = 'triages.create';

    protected ?string $permissionUpdateKey = 'triages.edit';

    protected ?string $permissionDeleteKey = 'triages.delete';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
        User::ROLE_NURSE,
        User::ROLE_DOCTOR,
    ];

    protected array $manageRoles = [
        User::ROLE_ADMIN,
        User::ROLE_NURSE,
        User::ROLE_RECEPTIONIST,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
    ];

    public function forwardToDoctor(User $user, Triage $triage): bool
    {
        return $this->update($user, $triage);
    }
}
