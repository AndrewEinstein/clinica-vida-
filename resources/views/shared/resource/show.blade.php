@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-1">{{ $title }} #{{ $record->id }}</h2>
        <div class="text-muted">Detalhes do registro selecionado.</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route($routeName.'.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
        @can('update', $record)
            <a href="{{ route($routeName.'.edit', $record) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
        @endcan
    </div>
</div>

<div class="panel p-3">
    <div class="row g-3">
        @foreach($fields as $field)
            @continue(($field['type'] ?? null) === 'hidden')
            @php
                $value = $field['value'] ?? data_get($record, $field['name']);
                $type = $field['type'] ?? 'text';
                if ($type === 'datetime-local' && $value) $value = $value instanceof \Illuminate\Support\Carbon ? $value->format('d/m/Y H:i') : $value;
                if ($type === 'date' && $value) $value = $value instanceof \Illuminate\Support\Carbon ? $value->format('d/m/Y') : $value;
                if ($type === 'number' && str_contains((string) ($field['label'] ?? ''), 'Valor')) $value = 'R$ '.number_format((float) $value, 2, ',', '.');
                if (($field['type'] ?? null) === 'select') $value = ($field['options'][$value] ?? $value);
            @endphp
            <div class="{{ $field['col'] ?? 'col-md-6' }}">
                <div class="text-muted small">{{ $field['label'] }}</div>
                <div class="fw-semibold">{{ $value !== null && $value !== '' ? $value : '-' }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
