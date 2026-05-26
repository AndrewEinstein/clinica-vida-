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
                $selected = $assigned[$selectedRole] ?? [];
            @endphp

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="min-width: 220px;">Modulo</th>
                            @foreach($actions as $actionKey => $actionLabel)
                                <th class="text-center" style="min-width: 110px;">{{ $actionLabel }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matrix as $module => $cols)
                            <tr>
                                <td class="fw-semibold">{{ strtoupper(str_replace(['-', '_'], ' ', $module)) }}</td>
                                @foreach($actions as $actionKey => $actionLabel)
                                    @php
                                        $perm = $cols[$actionKey] ?? null;
                                        $checked = $perm ? in_array($perm->key, $selected, true) : false;
                                    @endphp
                                    <td class="text-center">
                                        @if($perm)
                                            <div class="form-check d-inline-block m-0">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->key }}" id="perm_{{ $perm->id }}" {{ $checked ? 'checked' : '' }}>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 1 + count($actions) }}" class="text-muted">Nenhuma permissao cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection
