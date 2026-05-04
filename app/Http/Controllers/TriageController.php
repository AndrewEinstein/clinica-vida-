<?php

namespace App\Http\Controllers;

use App\Http\Requests\TriageRequest;
use App\Models\Appointment;
use App\Models\Triage;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class TriageController extends BaseCrudController
{
    protected string $modelClass = Triage::class;
    protected string $routeName = 'triages';
    protected string $viewPrefix = 'triages';
    protected string $title = 'Triagem de pacientes';
    protected string $singularTitle = 'Triagem';
    protected array $with = ['clinic', 'patient', 'appointment.doctor', 'professional'];
    protected array $searchable = ['chief_complaint', 'symptoms', 'notes'];
    protected ?string $rowActionsView = 'triages.actions';
    protected string $orderBy = 'triaged_at';

    public function store(TriageRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(TriageRequest $request, string $triage): RedirectResponse
    {
        return $this->updateRecord($request, Triage::query()->findOrFail($triage));
    }

    public function forwardToDoctor(Triage $triage): RedirectResponse
    {
        $this->authorize('forwardToDoctor', $triage);

        if (! $triage->appointment) {
            return back()->with('error', 'Triagem sem consulta vinculada.');
        }

        $triage->update(['status' => Triage::STATUS_FORWARDED]);
        $triage->appointment->update(['status' => Appointment::STATUS_WAITING_DOCTOR]);

        return redirect()->route('medical-care.show', $triage->appointment)->with('success', 'Paciente encaminhado ao medico.');
    }

    protected function prepareData(array $data, ?Model $record = null): array
    {
        $data = parent::prepareData($data, $record);
        $data['professional_id'] = $data['professional_id'] ?? auth()->id();
        $data['triaged_at'] = $data['triaged_at'] ?: now();

        if (! empty($data['weight']) && ! empty($data['height'])) {
            $data['bmi'] = round(((float) $data['weight']) / (((float) $data['height']) ** 2), 2);
        }

        return $data;
    }

    protected function afterSave(Model $record, \Illuminate\Foundation\Http\FormRequest $request): void
    {
        if (! $record instanceof Triage || ! $record->appointment) {
            return;
        }

        $status = match ($record->status) {
            Triage::STATUS_IN_PROGRESS => Appointment::STATUS_IN_TRIAGE,
            Triage::STATUS_COMPLETED => Appointment::STATUS_WAITING_DOCTOR,
            Triage::STATUS_FORWARDED => Appointment::STATUS_WAITING_DOCTOR,
            Triage::STATUS_CANCELLED => Appointment::STATUS_CANCELLED,
            default => Appointment::STATUS_WAITING_TRIAGE,
        };

        $record->appointment->update(['status' => $status]);
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Data/hora', 'key' => 'triaged_at', 'type' => 'datetime'],
            ['label' => 'Paciente', 'key' => 'patient.name'],
            ['label' => 'Consulta', 'key' => 'appointment.scheduled_at', 'type' => 'datetime'],
            ['label' => 'Risco', 'key' => 'risk_classification', 'type' => 'badge', 'options' => Triage::riskOptions(), 'badges' => Triage::riskBadges()],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => Triage::statusOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'patient_id', 'label' => 'Paciente', 'type' => 'select', 'options' => $this->patientOptions(), 'value' => request('patient_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'appointment_id', 'label' => 'Consulta/agendamento vinculado', 'type' => 'select', 'options' => ['' => 'Sem consulta'] + $this->appointmentOptions(), 'value' => request('appointment_id'), 'col' => 'col-md-4'],
            ['name' => 'professional_id', 'label' => 'Profissional responsavel', 'type' => 'select', 'options' => $this->userOptions([User::ROLE_NURSE, User::ROLE_ADMIN]), 'default' => auth()->id(), 'col' => 'col-md-4'],
            ['name' => 'triaged_at', 'label' => 'Data e hora', 'type' => 'datetime-local', 'default' => now()->format('Y-m-d\TH:i'), 'col' => 'col-md-4'],
            ['name' => 'status', 'label' => 'Status da triagem', 'type' => 'select', 'options' => Triage::statusOptions(), 'default' => Triage::STATUS_WAITING, 'col' => 'col-md-4'],
            ['name' => 'risk_classification', 'label' => 'Classificacao de risco', 'type' => 'select', 'options' => Triage::riskOptions(), 'default' => Triage::RISK_NOT_URGENT, 'col' => 'col-md-4'],
            ['name' => 'chief_complaint', 'label' => 'Queixa principal', 'type' => 'textarea', 'required' => true, 'col' => 'col-md-8'],
            ['name' => 'symptoms', 'label' => 'Sintomas', 'type' => 'textarea', 'col' => 'col-12'],
            ['name' => 'blood_pressure', 'label' => 'Pressao arterial', 'type' => 'text', 'col' => 'col-md-3'],
            ['name' => 'heart_rate', 'label' => 'Frequencia cardiaca', 'type' => 'number', 'col' => 'col-md-3'],
            ['name' => 'respiratory_rate', 'label' => 'Frequencia respiratoria', 'type' => 'number', 'col' => 'col-md-3'],
            ['name' => 'temperature', 'label' => 'Temperatura', 'type' => 'number', 'step' => '0.1', 'col' => 'col-md-3'],
            ['name' => 'oxygen_saturation', 'label' => 'Saturacao O2 (%)', 'type' => 'number', 'col' => 'col-md-3'],
            ['name' => 'weight', 'label' => 'Peso (kg)', 'type' => 'number', 'step' => '0.01', 'col' => 'col-md-3', 'class' => 'js-bmi-weight'],
            ['name' => 'height', 'label' => 'Altura (m)', 'type' => 'number', 'step' => '0.01', 'col' => 'col-md-3', 'class' => 'js-bmi-height'],
            ['name' => 'bmi', 'label' => 'IMC calculado', 'type' => 'number', 'step' => '0.01', 'readonly' => true, 'col' => 'col-md-3', 'class' => 'js-bmi-result'],
            ['name' => 'blood_glucose', 'label' => 'Glicemia', 'type' => 'number', 'col' => 'col-md-3'],
            ['name' => 'pain_level', 'label' => 'Nivel de dor (0-10)', 'type' => 'number', 'col' => 'col-md-3'],
            ['name' => 'allergies', 'label' => 'Alergias', 'type' => 'textarea', 'col' => 'col-md-6'],
            ['name' => 'current_medications', 'label' => 'Medicamentos em uso', 'type' => 'textarea', 'col' => 'col-md-6'],
            ['name' => 'pre_existing_conditions', 'label' => 'Doencas pre-existentes', 'type' => 'textarea', 'col' => 'col-md-6'],
            ['name' => 'notes', 'label' => 'Observacoes', 'type' => 'textarea', 'col' => 'col-md-6'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'risk_classification', 'label' => 'Risco', 'type' => 'select', 'options' => Triage::riskOptions()],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Triage::statusOptions()],
        ];
    }
}
