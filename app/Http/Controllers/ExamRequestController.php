<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequestFormRequest;
use App\Models\ExamRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class ExamRequestController extends BaseCrudController
{
    protected string $modelClass = ExamRequest::class;
    protected string $routeName = 'exam-requests';
    protected string $viewPrefix = 'exam-requests';
    protected string $title = 'Exames';
    protected string $singularTitle = 'Solicitacao de exame';
    protected array $with = ['clinic', 'patient', 'doctor', 'appointment'];
    protected array $searchable = ['exam_name', 'indication', 'result_notes'];
    protected string $orderBy = 'requested_at';

    public function store(ExamRequestFormRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(ExamRequestFormRequest $request, string $exam_request): RedirectResponse
    {
        return $this->updateRecord($request, ExamRequest::query()->findOrFail($exam_request));
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Paciente', 'key' => 'patient.name'],
            ['label' => 'Exame', 'key' => 'exam_name'],
            ['label' => 'Prioridade', 'key' => 'priority', 'type' => 'status', 'options' => ExamRequest::priorityOptions()],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => ExamRequest::statusOptions()],
            ['label' => 'Solicitado em', 'key' => 'requested_at', 'type' => 'datetime'],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'patient_id', 'label' => 'Paciente', 'type' => 'select', 'options' => $this->patientOptions(), 'value' => request('patient_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'doctor_id', 'label' => 'Medico', 'type' => 'select', 'options' => $this->doctorOptions(), 'value' => request('doctor_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'appointment_id', 'label' => 'Consulta', 'type' => 'select', 'options' => ['' => 'Sem consulta'] + $this->appointmentOptions(), 'value' => request('appointment_id'), 'col' => 'col-md-4'],
            ['name' => 'exam_name', 'label' => 'Exame solicitado', 'type' => 'text', 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'requested_at', 'label' => 'Data da solicitacao', 'type' => 'datetime-local', 'default' => now()->format('Y-m-d\TH:i'), 'col' => 'col-md-4'],
            ['name' => 'priority', 'label' => 'Prioridade', 'type' => 'select', 'options' => ExamRequest::priorityOptions(), 'default' => 'routine', 'col' => 'col-md-4'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ExamRequest::statusOptions(), 'default' => 'requested', 'col' => 'col-md-4'],
            ['name' => 'indication', 'label' => 'Indicacao clinica', 'type' => 'textarea', 'col' => 'col-md-6'],
            ['name' => 'result_notes', 'label' => 'Resultado/observacoes', 'type' => 'textarea', 'col' => 'col-md-6'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'priority', 'label' => 'Prioridade', 'type' => 'select', 'options' => ExamRequest::priorityOptions()],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ExamRequest::statusOptions()],
        ];
    }
}
