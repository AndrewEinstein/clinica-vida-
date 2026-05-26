@extends('layouts.app')

@section('title', 'Perfis e Permissoes')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 class="h4 mb-0">Perfis e Permissoes</h1>
        <div class="text-muted small">Defina quais funcoes cada perfil pode acessar.</div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if(!empty($setupError))
            <div class="alert alert-warning mb-3" role="alert">
                {{ $setupError }}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.role-permissions.update') }}">
            @csrf

            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-4">
                    <label class="form-label">Perfil</label>
                    <select name="role" class="form-select" required onchange="window.location='{{ route('settings.role-permissions.index') }}?role='+encodeURIComponent(this.value)">
                        @foreach($roles as $roleKey => $roleLabel)
                            <option value="{{ $roleKey }}" {{ (string) $selectedRole === (string) $roleKey ? 'selected' : '' }}>{{ $roleLabel }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-lg-8">
                    <button type="submit" class="btn btn-primary">
                        Salvar permissoes
                    </button>
                </div>
            </div>

            <hr class="my-4">

            @php
                $byGroup = $permissions->groupBy(fn($p) => $p->group ?: 'Geral');
                $selected = $assigned[$selectedRole] ?? [];
            @endphp

            <div class="row g-4">
                @foreach($byGroup as $group => $items)
                    <div class="col-12 col-lg-6">
                        <div class="border rounded p-3 h-100">
                            <div class="fw-semibold mb-2">{{ $group }}</div>
                            @foreach($items as $perm)
                                @php
                                    $checked = in_array($perm->key, $selected, true);
                                @endphp
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->key }}" id="perm_{{ $perm->id }}" {{ $checked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $perm->id }}">
                                        {{ $perm->name }}
                                        <span class="text-muted small">({{ $perm->key }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
</div>
@endsection
