@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-1">Relatorios</h2>
        <div class="text-muted">Indicadores por periodo, clinica, status e risco.</div>
    </div>
</div>

<div class="panel p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        @if(auth()->user()->isSuperAdmin())
            <div class="col-md-4">
                <label class="form-label">Clinica</label>
                <select name="clinic_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach($clinics as $id => $name)
                        <option value="{{ $id }}" @selected((int) $clinicId === (int) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-3">
            <label class="form-label">De</label>
            <input type="date" name="from" value="{{ $from }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Ate</label>
            <input type="date" name="to" value="{{ $to }}" class="form-control">
        </div>
        <div class="col-md-auto">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-funnel me-1"></i>Aplicar</button>
        </div>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="metric">
            <div class="text-muted small">Receita paga</div>
            <div class="h3 mb-0">R$ {{ number_format((float) $revenue, 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="metric">
            <div class="text-muted small">Despesas pagas</div>
            <div class="h3 mb-0">R$ {{ number_format((float) $expenses, 2, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="panel p-3 h-100">
            <h3 class="h5">Consultas por status</h3>
            @foreach(\App\Models\Appointment::statusOptions() as $status => $label)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>{{ $label }}</span>
                    <strong>{{ $appointmentsByStatus[$status] ?? 0 }}</strong>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel p-3 h-100">
            <h3 class="h5">Triagens por risco</h3>
            @foreach(\App\Models\Triage::riskOptions() as $risk => $label)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>{{ $label }}</span>
                    <strong>{{ $triagesByRisk[$risk] ?? 0 }}</strong>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="panel mt-3">
    <div class="p-3 border-bottom">
        <h3 class="h5 mb-0">Lancamentos recentes</h3>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Descricao</th>
                    <th>Paciente</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $transaction)
                    <tr>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ $transaction->patient?->name ?? '-' }}</td>
                        <td>{{ \App\Models\FinancialTransaction::typeOptions()[$transaction->type] ?? $transaction->type }}</td>
                        <td>R$ {{ number_format((float) $transaction->amount, 2, ',', '.') }}</td>
                        <td>{{ \App\Models\FinancialTransaction::statusOptions()[$transaction->status] ?? $transaction->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Sem lancamentos no periodo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
