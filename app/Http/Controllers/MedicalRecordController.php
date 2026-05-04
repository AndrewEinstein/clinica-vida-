<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalRecordRequest;
use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class MedicalRecordController extends BaseCrudController
{
    protected string $modelClass = MedicalRecord::class;
    protected string $routeName = 'medical-records';
    protected string $viewPrefix = 'medical-records';
    protected string $title = 'Prontuarios';
    protected string $singularTitle = 'Prontuario';
    protected array $with = ['clinic', 'patient', 'doctor', 'appointment'];
    protected array $searchable = ['diagnosis', 'subjective', 'assessment', 'plan'];

    public function store(MedicalRecordRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(MedicalRecordRequest $request, string $medical_record): RedirectResponse
    {
        return $this->updateRecord($request, MedicalRecord::query()->findOrFail($medical_record));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Paciente', 'key' => 'patient.name'],
            ['label' => 'Medico', 'key' => 'doctor.name'],
            ['label' => 'Diagnostico', 'key' => 'diagnosis'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => MedicalRecord::statusOptions()],
            ['label' => 'Criado em', 'key' => 'created_at', 'type' => 'datetime'],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'patient_id', 'label' => 'Paciente', 'type' => 'select', 'options' => $this->patientOptions(), 'value' => request('patient_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'doctor_id', 'label' => 'Medico', 'type' => 'select', 'options' => $this->doctorOptions(), 'value' => request('doctor_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'appointment_id', 'label' => 'Consulta', 'type' => 'select', 'options' => ['' => 'Sem consulta'] + $this->appointmentOptions(), 'value' => request('appointment_id'), 'col' => 'col-md-4'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => MedicalRecord::statusOptions(), 'default' => 'draft', 'col' => 'col-md-4'],
            ['name' => 'diagnosis', 'label' => 'Diagnostico', 'type' => 'text', 'col' => 'col-md-4'],
            ['name' => 'subjective', 'label' => 'Subjetivo / Historia clinica', 'type' => 'textarea', 'col' => 'col-md-6'],
            ['name' => 'objective', 'label' => 'Objetivo / Exame fisico', 'type' => 'textarea', 'col' => 'col-md-6'],
            ['name' => 'assessment', 'label' => 'Avaliacao', 'type' => 'textarea', 'col' => 'col-md-6'],
            ['name' => 'plan', 'label' => 'Plano terapeutico', 'type' => 'textarea', 'col' => 'col-md-6'],
            ['name' => 'notes', 'label' => 'Observacoes', 'type' => 'textarea', 'col' => 'col-12'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => MedicalRecord::statusOptions()],
        ];
    }
}
