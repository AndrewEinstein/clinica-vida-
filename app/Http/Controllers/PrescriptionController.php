<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class PrescriptionController extends BaseCrudController
{
    protected string $modelClass = Prescription::class;
    protected string $routeName = 'prescriptions';
    protected string $viewPrefix = 'prescriptions';
    protected string $title = 'Receita medica';
    protected string $singularTitle = 'Receita medica';
    protected array $with = ['clinic', 'patient', 'doctor', 'appointment'];
    protected array $searchable = ['medications', 'instructions'];
    protected string $orderBy = 'issued_at';

    public function store(PrescriptionRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(PrescriptionRequest $request, string $prescription): RedirectResponse
    {
        return $this->updateRecord($request, Prescription::query()->findOrFail($prescription));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Paciente', 'key' => 'patient.name'],
            ['label' => 'Medico', 'key' => 'doctor.name'],
            ['label' => 'Emitida em', 'key' => 'issued_at', 'type' => 'datetime'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => Prescription::statusOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'patient_id', 'label' => 'Paciente', 'type' => 'select', 'options' => $this->patientOptions(), 'value' => request('patient_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'doctor_id', 'label' => 'Medico', 'type' => 'select', 'options' => $this->doctorOptions(), 'value' => request('doctor_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'appointment_id', 'label' => 'Consulta', 'type' => 'select', 'options' => ['' => 'Sem consulta'] + $this->appointmentOptions(), 'value' => request('appointment_id'), 'col' => 'col-md-4'],
            ['name' => 'issued_at', 'label' => 'Data de emissao', 'type' => 'datetime-local', 'default' => now()->format('Y-m-d\TH:i'), 'col' => 'col-md-4'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Prescription::statusOptions(), 'default' => 'issued', 'col' => 'col-md-4'],
            ['name' => 'medications', 'label' => 'Medicamentos', 'type' => 'textarea', 'required' => true, 'col' => 'col-12'],
            ['name' => 'instructions', 'label' => 'Orientacoes', 'type' => 'textarea', 'col' => 'col-12'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Prescription::statusOptions()],
        ];
    }
}
