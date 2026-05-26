<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItTicketRequest;
use App\Models\ItTicket;
use App\Models\ItTicketComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItTicketController extends BaseCrudController
{
    protected string $modelClass = ItTicket::class;
    protected string $routeName = 'it-tickets';
    protected string $viewPrefix = 'it-tickets';
    protected string $title = 'Chamados de TI';
    protected string $singularTitle = 'Chamado';
    protected array $with = ['clinic', 'requester', 'assignedTo'];
    protected array $searchable = ['subject'];

    public function store(ItTicketRequest $request): RedirectResponse
    {
        return $this->storeRecord($request);
    }

    public function update(ItTicketRequest $request, string $it_ticket): RedirectResponse
    {
        /** @var ItTicket $ticket */
        $ticket = ItTicket::query()->findOrFail($it_ticket);

        $before = $ticket->status;
        $response = $this->updateRecord($request, $ticket);

        // Auto timestamps for status transitions
        $ticket->refresh();
        if ($before !== $ticket->status) {
            if ($ticket->status === ItTicket::STATUS_RESOLVED && $ticket->resolved_at === null) {
                $ticket->resolved_at = now();
                $ticket->save();
            }
            if ($ticket->status === ItTicket::STATUS_CLOSED && $ticket->closed_at === null) {
                $ticket->closed_at = now();
                $ticket->save();
            }
        }

        return $response;
    }

    public function show(string $id): View
    {
        /** @var ItTicket $ticket */
        $ticket = $this->findRecord($id);
        $this->authorize('view', $ticket);

        $comments = ItTicketComment::query()
            ->with('user')
            ->where('ticket_id', $ticket->id)
            ->latest()
            ->get();

        return view('it-tickets.show', [
            'title' => 'Chamado #'.$ticket->id,
            'ticket' => $ticket,
            'comments' => $comments,
            'assigneeOptions' => ['' => 'Nao atribuido'] + $this->userOptions(),
        ]);
    }

    public function comment(Request $request, string $it_ticket): RedirectResponse
    {
        /** @var ItTicket $ticket */
        $ticket = ItTicket::query()->findOrFail($it_ticket);
        $this->authorize('comment', $ticket);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ItTicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()?->id,
            'message' => $data['message'],
        ]);

        return back()->with('success', 'Comentario adicionado.');
    }

    protected function columns(): array
    {
        return [
            ['label' => '#', 'key' => 'id'],
            ['label' => 'Assunto', 'key' => 'subject'],
            ['label' => 'Tipo', 'key' => 'type', 'type' => 'badge', 'options' => ItTicket::typeOptions(), 'badges' => [
                ItTicket::TYPE_ERROR => 'danger',
                ItTicket::TYPE_IMPROVEMENT => 'info',
                ItTicket::TYPE_CORRECTION => 'warning',
                ItTicket::TYPE_OTHER => 'secondary',
            ]],
            ['label' => 'Prioridade', 'key' => 'priority', 'type' => 'badge', 'options' => ItTicket::priorityOptions(), 'badges' => [
                ItTicket::PRIORITY_LOW => 'secondary',
                ItTicket::PRIORITY_MEDIUM => 'primary',
                ItTicket::PRIORITY_HIGH => 'warning',
                ItTicket::PRIORITY_URGENT => 'danger',
            ]],
            ['label' => 'Status', 'key' => 'status', 'type' => 'status', 'options' => ItTicket::statusOptions()],
            ['label' => 'Solicitante', 'key' => 'requester.name'],
            ['label' => 'Responsavel', 'key' => 'assignedTo.name'],
            ['label' => 'Criado em', 'key' => 'created_at', 'type' => 'datetime'],
        ];
    }

    protected function fields(?Model $record = null): array
    {
        $record = $record ?: new ItTicket;

        return [
            ['name' => 'clinic_id', 'label' => 'Clinica vinculada', 'type' => auth()->user()->isSuperAdmin() ? 'select' : 'hidden', 'options' => $this->clinicOptions(), 'default' => auth()->user()->clinic_id, 'col' => 'col-md-4'],
            ['name' => 'type', 'label' => 'Tipo', 'type' => 'select', 'options' => ItTicket::typeOptions(), 'default' => ItTicket::TYPE_ERROR, 'col' => 'col-md-4'],
            ['name' => 'priority', 'label' => 'Prioridade', 'type' => 'select', 'options' => ItTicket::priorityOptions(), 'default' => ItTicket::PRIORITY_MEDIUM, 'col' => 'col-md-4'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ItTicket::statusOptions(), 'default' => ItTicket::STATUS_OPEN, 'col' => 'col-md-4'],
            ['name' => 'assigned_user_id', 'label' => 'Responsavel', 'type' => 'select', 'options' => ['' => 'Nao atribuido'] + $this->userOptions(), 'col' => 'col-md-8'],
            ['name' => 'subject', 'label' => 'Assunto', 'type' => 'text', 'required' => true, 'col' => 'col-md-12'],
            ['name' => 'description', 'label' => 'Descricao', 'type' => 'textarea', 'rows' => 4, 'col' => 'col-md-12'],
            ['name' => 'resolution_notes', 'label' => 'Notas de resolucao', 'type' => 'textarea', 'rows' => 4, 'col' => 'col-md-12'],
        ];
    }

    protected function filters(): array
    {
        return [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ItTicket::statusOptions()],
            ['name' => 'type', 'label' => 'Tipo', 'type' => 'select', 'options' => ItTicket::typeOptions()],
            ['name' => 'priority', 'label' => 'Prioridade', 'type' => 'select', 'options' => ItTicket::priorityOptions()],
        ];
    }

    protected function prepareData(array $data, ?Model $record = null): array
    {
        $data = parent::prepareData($data, $record);

        if ($record === null) {
            $data['requester_user_id'] = auth()->id();
        }

        return $data;
    }
}

