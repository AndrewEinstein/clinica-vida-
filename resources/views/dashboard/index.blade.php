@extends('layouts.app')

@section('content')
<div class="row g-3 mb-4">
    @foreach($cards as $card)
        <div class="col-sm-6 col-xl-3">
            <div class="metric">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                        <div class="h3 mb-0 mt-2">{{ $card['value'] }}</div>
                    </div>
                    <div class="metric-icon text-bg-{{ $card['color'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="panel">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Consultas de hoje</h2>
                <a href="{{ route('appointments.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-outline-primary">Ver agenda</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Medico</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointmentsToday as $appointment)
                            <tr>
                                <td>{{ $appointment->scheduled_at->format('H:i') }}</td>
                                <td>{{ $appointment->patient?->name }}</td>
                                <td>{{ $appointment->doctor?->name }}</td>
                                <td><span class="badge text-bg-{{ $appointment->statusBadge() }}">{{ $appointment->statusLabel() }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-outline-secondary">Abrir</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma consulta para hoje.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="panel">
            <div class="p-3 border-bottom">
                <h2 class="h5 mb-0">Alertas de emergencia</h2>
            </div>
            <div class="list-group list-group-flush">
                @forelse($emergencies as $triage)
                    <a class="list-group-item list-group-item-action" href="{{ route('triages.show', $triage) }}">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $triage->patient?->name }}</strong>
                            <span class="badge text-bg-danger">{{ $triage->riskLabel() }}</span>
                        </div>
                        <div class="text-muted small">{{ $triage->chief_complaint }}</div>
                    </a>
                @empty
                    <div class="p-4 text-center text-muted">Sem alertas criticos.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
