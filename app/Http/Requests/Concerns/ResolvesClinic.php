<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

trait ResolvesClinic
{
    protected function clinicId(): ?int
    {
        $user = $this->user();

        if (! $user) {
            return null;
        }

        if ($user->isSuperAdmin()) {
            return $this->integer('clinic_id') ?: null;
        }

        return (int) $user->clinic_id;
    }

    protected function clinicRule(): array
    {
        if ($this->user()?->isSuperAdmin()) {
            return ['required', 'integer', 'exists:clinics,id'];
        }

        return ['nullable', 'integer'];
    }

    protected function clinicScopedExists(string $table, string $column = 'id')
    {
        $clinicId = $this->clinicId();

        return Rule::exists($table, $column)->where(function (Builder $query) use ($clinicId): void {
            if ($clinicId) {
                $query->where('clinic_id', $clinicId);
            }
        });
    }

    protected function clinicScopedUnique(string $table, string $column, mixed $ignore = null)
    {
        $clinicId = $this->clinicId();
        $rule = Rule::unique($table, $column)->where(function (Builder $query) use ($clinicId): void {
            if ($clinicId) {
                $query->where('clinic_id', $clinicId);
            }
        });

        return $ignore ? $rule->ignore($ignore) : $rule;
    }
}
