@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
    <div>
        <h2 class="h4 mb-1">Chamado #{{ $ticket->id }}</h2>
        <div class="text-muted">{{ $ticket->subject }}</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @can('update', $ticket)
            <a href="{{ route('it-tickets.edit', $ticket) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
        @endcan
        <a href="{{ route('it-tickets.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-5">
        <div class="panel p-3 h-100">
            <h3 class="h6 mb-3">Detalhes</h3>
            <dl class="row mb-0">
                <dt class="col-5">Tipo</dt><dd class="col-7">{{ \App\Models\ItTicket::typeOptions()[$ticket->type] ?? $ticket->type }}</dd>
                <dt class="col-5">Prioridade</dt><dd class="col-7">{{ \App\Models\ItTicket::priorityOptions()[$ticket->priority] ?? $ticket->priority }}</dd>
                <dt class="col-5">Urgencia</dt><dd class="col-7">{{ \App\Models\ItTicket::urgencyOptions()[$ticket->urgency] ?? ($ticket->urgency ?: '-') }}</dd>
                <dt class="col-5">Impacto</dt><dd class="col-7">{{ \App\Models\ItTicket::impactOptions()[$ticket->impact] ?? ($ticket->impact ?: '-') }}</dd>
                <dt class="col-5">Status</dt><dd class="col-7"><span class="badge text-bg-light border text-dark">{{ \App\Models\ItTicket::statusOptions()[$ticket->status] ?? $ticket->status }}</span></dd>
                <dt class="col-5">Solicitante</dt><dd class="col-7">{{ $ticket->requester?->name ?? '-' }}</dd>
                <dt class="col-5">Setor</dt><dd class="col-7">{{ $ticket->requester_department ?? '-' }}</dd>
                <dt class="col-5">Categoria</dt><dd class="col-7">{{ $ticket->category ?? '-' }}</dd>
                <dt class="col-5">Subcategoria</dt><dd class="col-7">{{ $ticket->subcategory ?? '-' }}</dd>
                <dt class="col-5">Responsavel</dt><dd class="col-7">{{ $ticket->assignedTo?->name ?? '-' }}</dd>
                <dt class="col-5">Criado em</dt><dd class="col-7">{{ $ticket->created_at?->format('d/m/Y H:i') }}</dd>
                <dt class="col-5">Prazo/SLA</dt><dd class="col-7">{{ $ticket->sla_due_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                <dt class="col-5">Resolvido em</dt><dd class="col-7">{{ $ticket->resolved_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                <dt class="col-5">Fechado em</dt><dd class="col-7">{{ $ticket->closed_at?->format('d/m/Y H:i') ?? '-' }}</dd>
            </dl>

            @if($ticket->description)
                <hr>
                <div class="small text-muted mb-1">Descricao</div>
                <div class="text-body" style="white-space: pre-wrap;">{{ $ticket->description }}</div>
            @endif

            @if($ticket->internal_notes && auth()->user()->hasPermission('it-tickets.edit'))
                <hr>
                <div class="small text-muted mb-1">Observacoes internas (TI)</div>
                <div class="text-body" style="white-space: pre-wrap;">{{ $ticket->internal_notes }}</div>
            @endif

            @if($ticket->resolution_notes)
                <hr>
                <div class="small text-muted mb-1">Notas de resolucao</div>
                <div class="text-body" style="white-space: pre-wrap;">{{ $ticket->resolution_notes }}</div>
            @endif

            <hr>
            <div class="small text-muted mb-2">Anexos</div>
            <div class="vstack gap-2">
                @forelse(($attachments ?? []) as $att)
                    <div class="d-flex align-items-center justify-content-between border rounded p-2 bg-white">
                        <div class="text-truncate">
                            <i class="bi bi-paperclip me-1"></i>
                            {{ $att->original_name ?? basename($att->path) }}
                            <div class="text-muted small">{{ $att->created_at?->format('d/m/Y H:i') }}{{ $att->user ? ' • '.$att->user->name : '' }}</div>
                        </div>
                        <a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/'.$att->path) }}" target="_blank" rel="noopener">Abrir</a>
                    </div>
                @empty
                    <div class="text-muted">Nenhum anexo.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="panel p-3 mb-3">
            <h3 class="h6 mb-3">Linha do tempo</h3>
            <div class="vstack gap-2">
                @forelse(($events ?? []) as $ev)
                    <div class="border rounded p-3 bg-white">
                        <div class="d-flex justify-content-between">
                            <div class="fw-semibold">
                                @php
                                    $label = match($ev->type) {
                                        'created' => 'Chamado aberto',
                                        'assigned' => 'Responsavel alterado',
                                        'status_changed' => 'Status alterado',
                                        'comment' => 'Comentario',
                                        'attachment' => 'Anexo adicionado',
                                        'internal_note' => 'Observacao interna atualizada',
                                        default => $ev->type,
                                    };
                                @endphp
                                {{ $label }}
                            </div>
                            <div class="text-muted small">{{ $ev->created_at?->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="text-muted small">
                            {{ $ev->user?->name ?? 'Sistema' }}
                            @if($ev->type === 'status_changed')
                                @php $from = $ev->meta['from'] ?? null; $to = $ev->meta['to'] ?? null; @endphp
                                • {{ \App\Models\ItTicket::statusOptions()[$from] ?? $from }} → {{ \App\Models\ItTicket::statusOptions()[$to] ?? $to }}
                            @endif
                            @if($ev->type === 'assigned')
                                @php $to = $ev->meta['to'] ?? null; @endphp
                                • {{ $to ? ('Atribuido (ID '.$to.')') : 'Sem responsavel' }}
                            @endif
                            @if($ev->type === 'attachment')
                                @php $name = $ev->meta['name'] ?? null; @endphp
                                @if($name) • {{ $name }} @endif
                            @endif
                            @if($ev->type === 'comment')
                                @php $vis = $ev->meta['visibility'] ?? 'public'; @endphp
                                • {{ $vis === 'internal' ? 'Interno (TI)' : 'Publico' }}
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-muted">Nenhum evento registrado.</div>
                @endforelse
            </div>
        </div>

        <div class="panel p-3 mb-3">
            <h3 class="h6 mb-3">Atualizar status</h3>
            @can('update', $ticket)
                <form class="row g-2 align-items-end" method="POST" action="{{ route('it-tickets.update', $ticket) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="clinic_id" value="{{ $ticket->clinic_id }}">
                    <input type="hidden" name="type" value="{{ $ticket->type }}">
                    <input type="hidden" name="priority" value="{{ $ticket->priority }}">
                    <input type="hidden" name="subject" value="{{ $ticket->subject }}">
                    <input type="hidden" name="description" value="{{ $ticket->description }}">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Models\ItTicket::statusOptions() as $k => $label)
                                <option value="{{ $k }}" @selected($ticket->status === $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Responsavel</label>
                        <select name="assigned_user_id" class="form-select">
                            @foreach($assigneeOptions as $id => $label)
                                <option value="{{ $id }}" @selected((string) $ticket->assigned_user_id === (string) $id)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-check2-circle me-1"></i>Salvar</button>
                    </div>
                </form>
            @else
                <div class="text-muted">Sem permissao para atualizar este chamado.</div>
            @endcan
        </div>

        <div class="panel p-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="h6 mb-0">Comentarios</h3>
            </div>

            @can('comment', $ticket)
                <form method="POST" action="{{ route('it-tickets.comment', $ticket) }}" class="mb-3" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Visibilidade</label>
                            <select name="visibility" class="form-select">
                                <option value="public">Publico</option>
                                @if(auth()->user()->hasPermission('it-tickets.edit'))
                                    <option value="internal">Interno (TI)</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Anexos</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                        </div>
                    </div>
                    <div class="mb-2">
                        <textarea name="message" class="form-control" rows="3" placeholder="Descreva o que aconteceu, o que voce esperava e como reproduzir..." required></textarea>
                    </div>
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-chat-dots me-1"></i>Adicionar comentario</button>
                </form>
            @endcan

            <div class="vstack gap-2">
                @forelse($comments as $comment)
                    @continue($comment->visibility === 'internal' && ! auth()->user()->hasPermission('it-tickets.edit'))
                    <div class="border rounded p-3 bg-white">
                        <div class="d-flex justify-content-between">
                            <div class="fw-semibold">{{ $comment->user?->name ?? 'Sistema' }}</div>
                            <div class="text-muted small">{{ $comment->created_at?->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($comment->visibility === 'internal')
                            <div class="text-muted small mt-1">Interno (TI)</div>
                        @endif
                        <div class="mt-2" style="white-space: pre-wrap;">{{ $comment->message }}</div>
                    </div>
                @empty
                    <div class="text-muted">Nenhum comentario ainda.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
