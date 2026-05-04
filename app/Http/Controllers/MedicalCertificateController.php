<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalCertificateRequest;
use App\Models\MedicalCertificate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MedicalCertificateController extends BaseCrudController
{
    protected string $modelClass = MedicalCertificate::class;
    protected string $routeName = 'medical-certificates';
    protected string $viewPrefix = 'medical-certificates';
    protected string $title = 'Atestados';
    protected string $singularTitle = 'Atestado';
    protected array $with = ['clinic', 'patient', 'doctor', 'appointment'];
    protected array $searchable = ['title', 'content', 'attachment_name'];
    protected ?string $rowActionsView = 'medical-certificates.actions';
    protected string $orderBy = 'issued_at';

    public function store(MedicalCertificateRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(MedicalCertificateRequest $request, string $medical_certificate): RedirectResponse
    {
        return $this->updateRecord($request, MedicalCertificate::query()->findOrFail($medical_certificate));
    }

    public function print(MedicalCertificate $medicalCertificate): View
    {
        $this->authorize('print', $medicalCertificate);

        return view('medical-certificates.print', [
            'certificate' => $medicalCertificate->load(['clinic', 'patient', 'doctor', 'appointment']),
        ]);
    }

    public function export(MedicalCertificate $medicalCertificate): Response
    {
        $this->authorize('export', $medicalCertificate);
        $medicalCertificate->load(['clinic', 'patient', 'doctor']);

        $content = implode(PHP_EOL, [
            $medicalCertificate->title,
            'Clinica: '.$medicalCertificate->clinic?->name,
            'Paciente: '.$medicalCertificate->patient?->name,
            'Medico: '.$medicalCertificate->doctor?->name,
            'Emitido em: '.$medicalCertificate->issued_at?->format('d/m/Y H:i'),
            '',
            $medicalCertificate->content,
        ]);

        $fileName = Str::slug($medicalCertificate->title.'-'.$medicalCertificate->patient?->name).'.txt';

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    protected function prepareData(array $data, ?Model $record = null): array
    {
        $data = parent::prepareData($data, $record);
        unset($data['attachment']);
        $data['issued_at'] = $data['issued_at'] ?: now();

        return $data;
    }

    protected function afterSave(Model $record, \Illuminate\Foundation\Http\FormRequest $request): void
    {
        if (! $record instanceof MedicalCertificate || ! $request->hasFile('attachment')) {
            return;
        }

        $file = $request->file('attachment');
        $path = $file->store('medical-certificates');

        $record->update([
            'attachment_path' => $path,
            'attachment_name' => $file->getClientOriginalName(),
        ]);
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Emitido em', 'key' => 'issued_at', 'type' => 'datetime'],
            ['label' => 'Paciente', 'key' => 'patient.name'],
            ['label' => 'Medico', 'key' => 'doctor.name'],
            ['label' => 'Titulo', 'key' => 'title'],
            ['label' => 'Dias', 'key' => 'rest_days'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => MedicalCertificate::statusOptions()],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'patient_id', 'label' => 'Paciente', 'type' => 'select', 'options' => $this->patientOptions(), 'value' => request('patient_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'doctor_id', 'label' => 'Medico', 'type' => 'select', 'options' => $this->doctorOptions(), 'value' => request('doctor_id'), 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'appointment_id', 'label' => 'Consulta', 'type' => 'select', 'options' => ['' => 'Sem consulta'] + $this->appointmentOptions(), 'value' => request('appointment_id'), 'col' => 'col-md-4'],
            ['name' => 'title', 'label' => 'Titulo', 'type' => 'text', 'default' => 'Atestado medico', 'required' => true, 'col' => 'col-md-4'],
            ['name' => 'issued_at', 'label' => 'Data de emissao', 'type' => 'datetime-local', 'default' => now()->format('Y-m-d\TH:i'), 'col' => 'col-md-4'],
            ['name' => 'rest_days', 'label' => 'Dias de afastamento', 'type' => 'number', 'col' => 'col-md-3'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => MedicalCertificate::statusOptions(), 'default' => 'issued', 'col' => 'col-md-3'],
            ['name' => 'attachment', 'label' => 'Importar documento', 'type' => 'file', 'col' => 'col-md-6'],
            ['name' => 'content', 'label' => 'Texto do atestado', 'type' => 'textarea', 'required' => true, 'col' => 'col-12', 'default' => 'Atesto, para os devidos fins, que o(a) paciente necessita de afastamento de suas atividades pelo periodo informado.'],
        ];
    }

    protected function showFields(Model $record): array
    {
        return [
            ['name' => 'clinic.name', 'label' => 'Clinica', 'col' => 'col-md-4'],
            ['name' => 'patient.name', 'label' => 'Paciente', 'col' => 'col-md-4'],
            ['name' => 'doctor.name', 'label' => 'Medico', 'col' => 'col-md-4'],
            ['name' => 'title', 'label' => 'Titulo', 'col' => 'col-md-4'],
            ['name' => 'issued_at', 'label' => 'Emitido em', 'type' => 'datetime-local', 'col' => 'col-md-4'],
            ['name' => 'rest_days', 'label' => 'Dias de afastamento', 'col' => 'col-md-4'],
            ['name' => 'attachment_name', 'label' => 'Arquivo importado', 'col' => 'col-md-6'],
            ['name' => 'content', 'label' => 'Texto do atestado', 'col' => 'col-12'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => MedicalCertificate::statusOptions()],
        ];
    }
}
