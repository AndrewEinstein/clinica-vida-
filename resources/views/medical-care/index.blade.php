@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-1">Atendimento medico</h2>
        <div class="text-muted">Fila de pacientes encaminhados para consulta.</div>
    </div>
</div>

<div class="panel p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Paciente</label>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar paciente...">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Todos</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-auto">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search me-1"></i>Filtrar</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Medico</th>
                    <th>Consulta</th>
                    <th>Risco</th>
                    <th>Status</th>
                    <th class="text-end">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->patient?->name }}</td>
                        <td>{{ $appointment->doctor?->name }}</td>
                        <td>{{ $appointment->scheduled_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($appointment->triage)
                                <span class="badge text-bg-{{ $appointment->triage->riskBadge() }}">{{ $appointment->triage->riskLabel() }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td><span class="badge text-bg-{{ $appointment->statusBadge() }}">{{ $appointment->statusLabel() }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('medical-care.show', $appointment) }}" class="btn btn-sm btn-primary"><i class="bi bi-heart-pulse me-1"></i>Atender</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">Nenhum paciente na fila medica.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $appointments->links() }}</div>
</div>
@endsection
