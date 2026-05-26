<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_DOCTOR = 'doctor';
    public const ROLE_RECEPTIONIST = 'receptionist';
    public const ROLE_NURSE = 'nurse';
    public const ROLE_FINANCE = 'finance';

    protected $fillable = [
        'clinic_id',
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function roleOptions(): array
    {
        $system = [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Administrador da clinica',
            self::ROLE_DOCTOR => 'Medico',
            self::ROLE_RECEPTIONIST => 'Recepcionista',
            self::ROLE_NURSE => 'Triagem/Enfermagem',
            self::ROLE_FINANCE => 'Financeiro',
        ];

        // If roles table exists, merge clinic-specific roles for the current user (or super admin chosen clinic).
        try {
            if (Schema::hasTable('roles') && auth()->check()) {
                $user = auth()->user();
                $clinicId = $user?->clinic_id;

                $custom = Role::query()
                    ->where('status', 'active')
                    ->where(function ($q) use ($clinicId, $user) {
                        $q->where('is_system', true);
                        if ($clinicId) {
                            $q->orWhere('clinic_id', $clinicId);
                        }
                    })
                    ->orderByDesc('is_system')
                    ->orderBy('name')
                    ->pluck('name', 'key')
                    ->toArray();

                // Prefer DB names for system roles if present (but keep fallback).
                return array_replace($system, $custom);
            }
        } catch (\Throwable $e) {
            // Ignore if DB not ready yet.
        }

        return $system;
    }

    public static function statusOptions(): array
    {
        return [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class, 'role', 'role');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function roleLabel(): string
    {
        return self::roleOptions()[$this->role] ?? $this->role;
    }

    public function hasPermission(string $key): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Clinic Admin: allow everything inside the clinic by default unless you want to restrict later.
        if ($this->hasRole(self::ROLE_ADMIN)) {
            return true;
        }

        return RolePermission::query()
            ->where('role', $this->role)
            ->whereHas('permission', fn ($q) => $q->where('key', $key))
            ->exists();
    }
}
