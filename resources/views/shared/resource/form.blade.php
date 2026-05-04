@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-1">{{ $title }}</h2>
        <div class="text-muted">Preencha os campos obrigatorios e salve as alteracoes.</div>
    </div>
    <a href="{{ route($routeName.'.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>

<form method="POST" action="{{ $action }}" class="panel p-3" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    <div class="row g-3">
        @foreach($fields as $field)
            @php
                $name = $field['name'];
                $type = $field['type'] ?? 'text';
                $value = old($name, $field['value'] ?? data_get($record, $name, $field['default'] ?? ''));
                if ($type === 'datetime-local' && $value instanceof \Illuminate\Support\Carbon) $value = $value->format('Y-m-d\TH:i');
                if ($type === 'date' && $value instanceof \Illuminate\Support\Carbon) $value = $value->format('Y-m-d');
                $classes = trim('form-control '.($field['class'] ?? '').' '.($errors->has($name) ? 'is-invalid' : ''));
            @endphp
            @if($type === 'hidden')
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @continue
            @endif
            <div class="{{ $field['col'] ?? 'col-md-6' }}">
                <label class="form-label">{{ $field['label'] }} @if($field['required'] ?? false)<span class="text-danger">*</span>@endif</label>
                @if($type === 'select')
                    <select name="{{ $name }}" class="form-select @error($name) is-invalid @enderror" @if($field['required'] ?? false) required @endif>
                        @foreach(($field['options'] ?? []) as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                @elseif($type === 'textarea')
                    <textarea name="{{ $name }}" rows="4" class="{{ $classes }}" @if($field['required'] ?? false) required @endif @if($field['readonly'] ?? false) readonly @endif>{{ $value }}</textarea>
                @elseif($type === 'file')
                    <input
                        type="file"
                        name="{{ $name }}"
                        class="{{ $classes }}"
                        @if($field['required'] ?? false) required @endif
                    >
                @else
                    <input
                        type="{{ $type }}"
                        name="{{ $name }}"
                        value="{{ $type === 'password' ? '' : $value }}"
                        class="{{ $classes }}"
                        @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
                        @if($field['required'] ?? false) required @endif
                        @if($field['readonly'] ?? false) readonly @endif
                    >
                @endif
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route($routeName.'.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i>Salvar</button>
    </div>
</form>
@endsection
