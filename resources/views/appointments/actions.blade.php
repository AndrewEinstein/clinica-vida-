@can('changeStatus', $row)
    @if($row->status === \App\Models\Appointment::STATUS_SCHEDULED)
        <form method="POST" action="{{ route('appointments.confirm', $row) }}">
            @csrf
            <button class="btn btn-outline-success btn-icon" title="Confirmar" type="submit"><i class="bi bi-check2"></i></button>
        </form>
    @endif
    @if(! $row->triage && ! in_array($row->status, [\App\Models\Appointment::STATUS_CANCELLED, \App\Models\Appointment::STATUS_FINISHED], true))
        <form method="POST" action="{{ route('appointments.create-triage', $row) }}">
            @csrf
            <button class="btn btn-outline-warning btn-icon" title="Criar triagem" type="submit"><i class="bi bi-clipboard2-plus"></i></button>
        </form>
    @endif
    @if($row->triage)
        <a class="btn btn-outline-info btn-icon" title="Ver triagem" href="{{ route('triages.show', $row->triage) }}"><i class="bi bi-clipboard2-pulse"></i></a>
        <form method="POST" action="{{ route('appointments.forward-to-doctor', $row) }}">
            @csrf
            <button class="btn btn-outline-danger btn-icon" title="Encaminhar ao medico" type="submit"><i class="bi bi-heart-pulse"></i></button>
        </form>
    @endif
    @if(! in_array($row->status, [\App\Models\Appointment::STATUS_CANCELLED, \App\Models\Appointment::STATUS_FINISHED], true))
        <button class="btn btn-outline-dark btn-icon" title="Cancelar" data-bs-toggle="modal" data-bs-target="#cancel-appointment-{{ $row->id }}"><i class="bi bi-x-lg"></i></button>
        <form method="POST" action="{{ route('appointments.finalize', $row) }}">
            @csrf
            <button class="btn btn-outline-success btn-icon" title="Finalizar" type="submit"><i class="bi bi-flag"></i></button>
        </form>
        <div class="modal fade" id="cancel-appointment-{{ $row->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content text-start">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancelar consulta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('appointments.cancel', $row) }}">
                        @csrf
                        <div class="modal-body">
                            <label class="form-label">Motivo do cancelamento</label>
                            <textarea name="cancellation_reason" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Voltar</button>
                            <button class="btn btn-danger" type="submit">Cancelar consulta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endcan
