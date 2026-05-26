<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Ensure generated URLs use HTTPS when running behind a TLS-terminating proxy (e.g. Render).
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        foreach ([
            \App\Models\Clinic::class => \App\Policies\ClinicPolicy::class,
            \App\Models\User::class => \App\Policies\UserPolicy::class,
            \App\Models\Doctor::class => \App\Policies\DoctorPolicy::class,
            \App\Models\Patient::class => \App\Policies\PatientPolicy::class,
            \App\Models\Appointment::class => \App\Policies\AppointmentPolicy::class,
            \App\Models\Triage::class => \App\Policies\TriagePolicy::class,
            \App\Models\MedicalRecord::class => \App\Policies\MedicalRecordPolicy::class,
            \App\Models\Prescription::class => \App\Policies\PrescriptionPolicy::class,
            \App\Models\MedicalCertificate::class => \App\Policies\MedicalCertificatePolicy::class,
            \App\Models\ExamRequest::class => \App\Policies\ExamRequestPolicy::class,
            \App\Models\FinancialTransaction::class => \App\Policies\FinancialTransactionPolicy::class,
            \App\Models\InsuranceProvider::class => \App\Policies\InsuranceProviderPolicy::class,
            \App\Models\ClinicSetting::class => \App\Policies\ClinicSettingPolicy::class,
        ] as $model => $policy) {
            Gate::policy($model, $policy);
        }

        Gate::before(function (User $user): ?bool {
            return $user->isSuperAdmin() ? true : null;
        });

        Blade::if('activeRoute', function (string|array $routes): bool {
            return request()->routeIs(...(array) $routes);
        });
    }
}
