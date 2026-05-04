<?php

namespace App\Concerns;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClinic
{
    protected static function bootBelongsToClinic(): void
    {
        static::creating(function ($model): void {
            $user = auth()->user();

            if ($user && ! $user->isSuperAdmin() && empty($model->clinic_id)) {
                $model->clinic_id = $user->clinic_id;
            }
        });

        static::addGlobalScope('clinic', function (Builder $builder): void {
            if (app()->runningInConsole()) {
                return;
            }

            $user = auth()->user();

            if ($user && ! $user->isSuperAdmin()) {
                $builder->where($builder->getModel()->getTable().'.clinic_id', $user->clinic_id);
            }
        });
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
