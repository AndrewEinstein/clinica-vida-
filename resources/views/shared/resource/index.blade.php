@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
    <div>
        <h2 class="h4 mb-1">{{ $title }}</h2>
        <div class="text-muted">Busca, filtros, paginacao e acoes conectadas ao banco.</div>
    </div>
    @can('create', $modelClass)
        <a href="{{ route($routeName.'.create') }}" class="btn btn-primary align-self-md-start">
            <i class="bi bi-plus-lg me-1"></i>{{ $routeName === 'medical-certificates' ? 'Importar atestado' : 'Novo '.$singularTitle }}
        </a>
    @endcan
</div>

<div class="panel p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Busca</label>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar...">
        </div>
        @foreach($filters as $filter)
            <div class="col-md-2">
                <label class="form-label">{{ $filter['label'] }}</label>
                @if(($filter['type'] ?? 'text') === 'select')
                    <select name="{{ $filter['name'] }}" class="form-select">
                        <option value="">Todos</option>
                        @foreach($filter['options'] as $value => $label)
                            <option value="{{ $value }}" @selected((string) request($filter['name']) === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="{{ $filter['type'] ?? 'text' }}" name="{{ $filter['name'] }}" value="{{ request($filter['name']) }}" class="form-control">
                @endif
            </div>
        @endforeach
        <div class="col-md-auto d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search me-1"></i>Filtrar</button>
            <a class="btn btn-outline-secondary" href="{{ route($routeName.'.index') }}"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    @foreach($columns as $column)
                        @continue(($column['super_admin'] ?? false) && ! auth()->user()->isSuperAdmin())
                        <th>{{ $column['label'] }}</th>
                    @endforeach
                    <th class="text-end">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        @foreach($columns as $column)
                            @continue(($column['super_admin'] ?? false) && ! auth()->user()->isSuperAdmin())
                            @php
                                $value = data_get($item, $column['key']);
                                $type = $column['type'] ?? 'text';
                                $display = $value;
                                if ($type === 'datetime' && $value) $display = $value->format('d/m/Y H:i');
                                if ($type === 'date' && $value) $display = $value->format('d/m/Y');
                                if ($type === 'money' && $value !== null) $display = 'R$ '.number_format((float) $value, 2, ',', '.');
                                if (in_array($type, ['status', 'badge'], true)) $display = ($column['options'][$value] ?? $value);
                            @endphp
                            <td>
                                @if($type === 'badge')
                                    <span class="badge text-bg-{{ $column['badges'][$value] ?? 'secondary' }}">{{ $display }}</span>
                                @elseif($type === 'status')
                                    <span class="badge text-bg-light border text-dark">{{ $display ?: '-' }}</span>
                                @else
                                    {{ $display ?: '-' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                @if($rowActionsView)
                                    @include($rowActionsView, ['row' => $item])
                                @endif
                                <a class="btn btn-outline-secondary btn-icon" title="Ver" href="{{ route($routeName.'.show', $item) }}"><i class="bi bi-eye"></i></a>
                                @can('update', $item)
                                    <a class="btn btn-outline-primary btn-icon" title="Editar" href="{{ route($routeName.'.edit', $item) }}"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete', $item)
                                    <button class="btn btn-outline-danger btn-icon" title="Excluir" data-bs-toggle="modal" data-bs-target="#delete-{{ $item->id }}"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                            @can('delete', $item)
                                <div class="modal fade" id="delete-{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar exclusao</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">Deseja remover este registro?</div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form method="POST" action="{{ route($routeName.'.destroy', $item) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" type="submit">Excluir</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="text-center text-muted py-5">Nenhum registro encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $items->links() }}
    </div>
</div>
@endsection
