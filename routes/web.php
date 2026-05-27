<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\ClinicSettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ExamRequestController;
use App\Http\Controllers\FinancialTransactionController;
use App\Http\Controllers\InsuranceProviderController;
use App\Http\Controllers\ItTicketController;
use App\Http\Controllers\ItTicketDashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MedicalCareController;
use App\Http\Controllers\MedicalCertificateController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TriageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/dashboard', fn () => redirect()->route('dashboard'));

    Route::resource('clinics', ClinicController::class);
    Route::resource('users', UserController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('patients', PatientController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('appointments/{appointment}/finalize', [AppointmentController::class, 'finalize'])->name('appointments.finalize');
    Route::post('appointments/{appointment}/create-triage', [AppointmentController::class, 'createTriage'])->name('appointments.create-triage');
    Route::post('appointments/{appointment}/forward-to-doctor', [AppointmentController::class, 'forwardToDoctor'])->name('appointments.forward-to-doctor');

    Route::resource('triages', TriageController::class);
    Route::post('triages/{triage}/forward-to-doctor', [TriageController::class, 'forwardToDoctor'])->name('triages.forward-to-doctor');

    Route::resource('medical-care', MedicalCareController::class)->only(['index', 'show', 'update']);
    Route::resource('medical-records', MedicalRecordController::class);
    Route::get('medical-certificates/{medicalCertificate}/print', [MedicalCertificateController::class, 'print'])->name('medical-certificates.print');
    Route::get('medical-certificates/{medicalCertificate}/export', [MedicalCertificateController::class, 'export'])->name('medical-certificates.export');
    Route::resource('medical-certificates', MedicalCertificateController::class);
    Route::resource('prescriptions', PrescriptionController::class);
    Route::resource('exam-requests', ExamRequestController::class);
    Route::resource('finance', FinancialTransactionController::class);
    Route::resource('insurance-providers', InsuranceProviderController::class);
    Route::get('it-tickets/dashboard', ItTicketDashboardController::class)->name('it-tickets.dashboard');
    Route::resource('it-tickets', ItTicketController::class);
    Route::post('it-tickets/{it_ticket}/comment', [ItTicketController::class, 'comment'])->name('it-tickets.comment');
    Route::resource('roles', RoleController::class);

    Route::get('settings/role-permissions', [RolePermissionsController::class, 'index'])->name('settings.role-permissions.index');
    Route::post('settings/role-permissions', [RolePermissionsController::class, 'update'])->name('settings.role-permissions.update');

    // Keep after the more specific settings/* routes to avoid route conflicts with settings/{setting}.
    Route::resource('settings', ClinicSettingController::class);

    Route::resource('reports', ReportController::class)->only(['index']);
});
