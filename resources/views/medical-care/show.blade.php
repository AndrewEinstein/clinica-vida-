@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
    <div>
        <h2 class="h4 mb-1">Atendimento #{{ $appointment->id }}</h2>
        <div class="text-muted">{{ $appointment->patient?->name }} com {{ $appointment->doctor?->name }}</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <form method="POST" action="{{ route('medical-care.update', $appointment) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="start">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-play-fill me-1"></i>Iniciar</button>
        </form>
        <form method="POST" action="{{ route('medical-care.update', $appointment) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="finish">
            <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle me-1"></i>Concluir</button>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-4">
        <div class="panel p-3 h-100">
            <h3 class="h5">Paciente</h3>
            <dl class="row mb-0">
                <dt class="col-5">Nome</dt><dd class="col-7">{{ $appointment->patient?->name }}</dd>
                <dt class="col-5">CPF</dt><dd class="col-7">{{ $appointment->patient?->cpf }}</dd>
                <dt class="col-5">Nascimento</dt><dd class="col-7">{{ $appointment->patient?->birth_date?->format('d/m/Y') ?? '-' }}</dd>
                <dt class="col-5">Telefone</dt><dd class="col-7">{{ $appointment->patient?->phone ?? '-' }}</dd>
                <dt class="col-5">Convenio</dt><dd class="col-7">{{ $appointment->patient?->insuranceProvider?->name ?? 'Particular' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="panel p-3 h-100">
            <h3 class="h5">Consulta</h3>
            <dl class="row mb-0">
                <dt class="col-5">Data</dt><dd class="col-7">{{ $appointment->scheduled_at?->format('d/m/Y H:i') }}</dd>
                <dt class="col-5">Tipo</dt><dd class="col-7">{{ $appointment->type }}</dd>
                <dt class="col-5">Motivo</dt><dd class="col-7">{{ $appointment->reason ?? '-' }}</dd>
                <dt class="col-5">Status</dt><dd class="col-7"><span class="badge text-bg-{{ $appointment->statusBadge() }}">{{ $appointment->statusLabel() }}</span></dd>
            </dl>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="panel p-3 h-100">
            <h3 class="h5">Triagem</h3>
            @if($appointment->triage)
                <div class="mb-2"><span class="badge text-bg-{{ $appointment->triage->riskBadge() }}">{{ $appointment->triage->riskLabel() }}</span></div>
                <div class="small text-muted">Queixa principal</div>
                <p class="mb-2">{{ $appointment->triage->chief_complaint }}</p>
                <div class="small text-muted">Sintomas</div>
                <p class="mb-0">{{ $appointment->triage->symptoms ?: '-' }}</p>
            @else
                <div class="text-muted">Sem triagem vinculada.</div>
            @endif
        </div>
    </div>
</div>

@if($appointment->triage)
    <div class="panel p-3 mt-3">
        <h3 class="h5">Sinais vitais</h3>
        <div class="row g-3">
            @foreach([
                'Pressao arterial' => $appointment->triage->blood_pressure,
                'FC' => $appointment->triage->heart_rate,
                'FR' => $appointment->triage->respiratory_rate,
                'Temperatura' => $appointment->triage->temperature,
                'Saturacao O2' => $appointment->triage->oxygen_saturation,
                'Peso' => $appointment->triage->weight,
                'Altura' => $appointment->triage->height,
                'IMC' => $appointment->triage->bmi,
                'Glicemia' => $appointment->triage->blood_glucose,
                'Dor' => $appointment->triage->pain_level,
            ] as $label => $value)
                <div class="col-6 col-md-3 col-xl-2">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fw-semibold">{{ $value ?? '-' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="panel p-3 mt-3">
    <div class="d-flex flex-wrap gap-2">
        @can('create', \App\Models\MedicalRecord::class)
            <a class="btn btn-primary" href="{{ route('medical-records.create', ['patient_id' => $appointment->patient_id, 'doctor_id' => $appointment->doctor_id, 'appointment_id' => $appointment->id]) }}"><i class="bi bi-journal-plus me-1"></i>Criar prontuario</a>
        @endcan
        @can('create', \App\Models\MedicalCertificate::class)
            <a class="btn btn-outline-primary" href="{{ route('medical-certificates.create', ['patient_id' => $appointment->patient_id, 'doctor_id' => $appointment->doctor_id, 'appointment_id' => $appointment->id]) }}"><i class="bi bi-file-medical me-1"></i>Criar atestado</a>
        @endcan
        @can('create', \App\Models\Prescription::class)
            <a class="btn btn-outline-primary" href="{{ route('prescriptions.create', ['patient_id' => $appointment->patient_id, 'doctor_id' => $appointment->doctor_id, 'appointment_id' => $appointment->id]) }}"><i class="bi bi-capsule me-1"></i>Criar receita medica</a>
        @endcan
        @can('create', \App\Models\ExamRequest::class)
            <a class="btn btn-outline-primary" href="{{ route('exam-requests.create', ['patient_id' => $appointment->patient_id, 'doctor_id' => $appointment->doctor_id, 'appointment_id' => $appointment->id]) }}"><i class="bi bi-file-earmark-medical me-1"></i>Solicitar exame</a>
        @endcan
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="panel p-3 h-100">
            <h3 class="h5">Prescricoes</h3>
            <div class="list-group list-group-flush">
                @forelse($prescriptions as $prescription)
                    <a href="{{ route('prescriptions.show', $prescription) }}" class="list-group-item list-group-item-action px-0">
                        <strong>{{ $prescription->issued_at?->format('d/m/Y H:i') ?? 'Sem data' }}</strong>
                        <div class="text-muted small">{{ str($prescription->medications)->limit(90) }}</div>
                    </a>
                @empty
                    <div class="text-muted">Nenhuma prescricao.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel p-3 h-100">
            <h3 class="h5">Exames</h3>
            <div class="list-group list-group-flush">
                @forelse($examRequests as $exam)
                    <a href="{{ route('exam-requests.show', $exam) }}" class="list-group-item list-group-item-action px-0">
                        <strong>{{ $exam->exam_name }}</strong>
                        <div class="text-muted small">{{ $exam->requested_at?->format('d/m/Y H:i') ?? 'Sem data' }}</div>
                    </a>
                @empty
                    <div class="text-muted">Nenhum exame solicitado.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
