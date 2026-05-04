@can('forwardToDoctor', $row)
    @if($row->appointment && $row->status !== \App\Models\Triage::STATUS_FORWARDED)
        <form method="POST" action="{{ route('triages.forward-to-doctor', $row) }}">
            @csrf
            <button class="btn btn-outline-danger btn-icon" title="Encaminhar ao medico" type="submit"><i class="bi bi-heart-pulse"></i></button>
        </form>
    @endif
@endcan
