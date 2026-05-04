<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentController extends BaseCrudController
{
    protected string $modelClass = Appointment::class;
    protected string $routeName = 'appointments';
    protected string $viewPrefix = 'appointments';
    protected string $title = 'Agenda medica';
    protected string $singularTitle = 'Consulta';
    protected array $with = ['clinic', 'patient', 'doctor', 'insuranceProvider', 'triage'];
    protected array $searchable = ['type', 'reason', 'notes'];
    protected ?string $rowActionsView = 'appointments.actions';
    protected string $orderBy = 'scheduled_at';
    protected string $orderDirection = 'desc';

    public function store(AppointmentRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(AppointmentRequest $request, string $appointment): RedirectResponse
    {
        return $this->updateRecord($request, Appointment::query()->findOrFail($appointment));
    }

    public function confirm(Appointment $appointment): RedirectResponse
    {
        $this->authorize('changeStatus', $appointment);
        $appointment->update(['status' => Appointment::STATUS_CONFIRMED]);

        return back()->with('success', 'Consulta confirmada.');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('changeStatus', $appointment);
        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'cancellation_reason' => $request->input('cancellation_reason'),
        ]);

        return back()->with('success', 'Consulta cancelada.');
    }

    public function finalize(Appointment $appointment): RedirectResponse
    {
        $this->authorize('changeStatus', $appointment);
        $appointment->update(['status' => Appointment::STATUS_FINISHED]);

        return back()->with('success', 'Consulta finalizada.');
    }

    public function createTriage(Appointment $appointment): RedirectResponse
    {
        $this->authorize('view', $appointment);
        $this->authorize('create', \App\Models\Triage::class);

        $appointment->update(['status' => Appointment::STATUS_WAITING_TRIAGE]);

        return redirect()->route('triages.create', [
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
        ]);
    }

    public function forwardToDoctor(Appointment $appointment): RedirectResponse
    {
        $this->authorize('changeStatus', $appointment);

        if ($appointment->triage) {
            $appointment->triage->update(['status' => \App\Models\Triage::STATUS_FORWARDED]);
        }

        $appointment->update(['status' => Appointment::STATUS_WAITING_DOCTOR]);

        return redirect()->route('medical-care.show', $appointment)->with('success', 'Paciente encaminhado ao medico.');
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Data/hora', 'key' => 'scheduled_at', 'type' => 'datetime'],
            ['label' => 'Paciente', 'key' => 'patient.name'],
            ['label' => 'Medico', 'key' => 'doctor.name'],
            ['label' => 'Tipo', 'key' => 'type'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'badge', 'options' => Appointment::statusOptions(), 'badges' => Appointment::badgeOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'patient_id', 'label' => 'Paciente', 'type' => 'select', 'options' => $this->patientOptions(), 'value' => request('patient_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'doctor_id', 'label' => 'Medico', 'type' => 'select', 'options' => $this->doctorOptions(), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'insurance_provider_id', 'label' => 'Convenio', 'type' => 'select', 'options' => ['' => 'Particular'] + $this->insuranceOptions(), 'col' => 'col-md-4'],
            ['name' => 'scheduled_at', 'label' => 'Data e hora', 'type' => 'datetime-local', 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'duration_minutes', 'label' => 'Duracao (min)', 'type' => 'number', 'default' => 30, 'col' => 'col-md-2'],
            ['name' => 'type', 'label' => 'Tipo', 'type' => 'text', 'default' => 'Consulta', 'required' => true, 'col' => 'col-md-2'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Appointment::statusOptions(), 'default' => Appointment::STATUS_SCHEDULED, 'col' => 'col-md-4'],
            ['name' => 'reason', 'label' => 'Motivo', 'type' => 'text', 'col' => 'col-md-8'],
            ['name' => 'cancellation_reason', 'label' => 'Motivo do cancelamento', 'type' => 'textarea', 'col' => 'col-md-4'],
            ['name' => 'notes', 'label' => 'Observacoes', 'type' => 'textarea', 'col' => 'col-12'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Appointment::statusOptions()],
            ['name' => 'from', 'label' => 'De', 'type' => 'date', 'column' => 'scheduled_at', 'operator' => 'date>='],
            ['name' => 'to', 'label' => 'Ate', 'type' => 'date', 'column' => 'scheduled_at', 'operator' => 'date<='],
        ];
    }
}
