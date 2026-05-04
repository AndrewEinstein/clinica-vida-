@can('print', $row)
    <a class="btn btn-sm btn-outline-secondary" title="Imprimir" href="{{ route('medical-certificates.print', $row) }}" target="_blank"><i class="bi bi-printer me-1"></i>Imprimir</a>
@endcan
@can('export', $row)
    <a class="btn btn-sm btn-outline-success" title="Exportar" href="{{ route('medical-certificates.export', $row) }}"><i class="bi bi-download me-1"></i>Exportar</a>
@endcan
