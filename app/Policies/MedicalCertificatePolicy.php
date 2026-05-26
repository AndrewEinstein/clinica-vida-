<?php

namespace App\Policies;

use App\Models\User;

class MedicalCertificatePolicy extends ClinicScopedPolicy
{
    protected ?string $permissionViewKey = 'medical-certificates.view';

    protected ?string $permissionCreateKey = 'medical-certificates.create';

    protected ?string $permissionUpdateKey = 'medical-certificates.edit';

    protected ?string $permissionDeleteKey = 'medical-certificates.delete';

    protected array $viewRoles = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
        User::ROLE_NURSE,
        User::ROLE_DOCTOR,
    ];

    protected array $manageRoles = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
        User::ROLE_DOCTOR,
    ];

    protected array $deleteRoles = [
        User::ROLE_ADMIN,
        User::ROLE_DOCTOR,
    ];

    public function print(User $user, \App\Models\MedicalCertificate $medicalCertificate): bool
    {
        return $this->view($user, $medicalCertificate);
    }

    public function export(User $user, \App\Models\MedicalCertificate $medicalCertificate): bool
    {
        return $this->view($user, $medicalCertificate);
    }
}
