@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
    <div>
        <h2 class="h4 mb-1">Dashboard - Chamados de TI</h2>
        <div class="text-muted">Indicadores e graficos para acompanhamento.</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('it-tickets.index') }}" class="btn btn-outline-secondary"><i class="bi bi-list-ul me-1"></i>Lista</a>
        @can('create', \App\Models\ItTicket::class)
            <a href="{{ route('it-tickets.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo chamado</a>
        @endcan
    </div>
</div>

<div class="row g-3 mb-3">
    @foreach($cards as $c)
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="panel p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">{{ $c['label'] }}</div>
                        <div class="h4 mb-0">{{ $c['value'] }}</div>
                    </div>
                    <div class="badge text-bg-{{ $c['color'] }} p-2"><i class="bi {{ $c['icon'] }}"></i></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-xl-6">
        <div class="panel p-3">
            <div class="fw-semibold mb-2">Chamados por status</div>
            <canvas id="chartStatus" height="140"></canvas>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="panel p-3">
            <div class="fw-semibold mb-2">Chamados por prioridade</div>
            <canvas id="chartPriority" height="140"></canvas>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="panel p-3">
            <div class="fw-semibold mb-2">Abertos por dia (14 dias)</div>
            <canvas id="chartOpened" height="140"></canvas>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="panel p-3">
            <div class="fw-semibold mb-2">Top categorias</div>
            <canvas id="chartCategory" height="140"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(() => {
  const statusMap = @json(\App\Models\ItTicket::statusOptions());
  const priorityMap = @json(\App\Models\ItTicket::priorityOptions());

  const byStatus = @json($byStatus);
  const statusLabels = Object.keys(byStatus).map(k => statusMap[k] ?? k);
  const statusValues = Object.values(byStatus);

  const byPriority = @json($byPriority);
  const priorityLabels = Object.keys(byPriority).map(k => priorityMap[k] ?? k);
  const priorityValues = Object.values(byPriority);

  const openedByDay = @json($openedByDay);
  const openedLabels = openedByDay.map(i => i.day);
  const openedValues = openedByDay.map(i => i.total);

  const byCategory = @json($byCategory);
  const categoryLabels = Object.keys(byCategory);
  const categoryValues = Object.values(byCategory);

  new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: { labels: statusLabels, datasets: [{ data: statusValues }]},
    options: { plugins: { legend: { position: 'bottom' } } }
  });

  new Chart(document.getElementById('chartPriority'), {
    type: 'bar',
    data: { labels: priorityLabels, datasets: [{ data: priorityValues, backgroundColor: '#0d6efd' }]},
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  new Chart(document.getElementById('chartOpened'), {
    type: 'line',
    data: { labels: openedLabels, datasets: [{ data: openedValues, borderColor: '#0f9f8f', tension: .3 }]},
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  new Chart(document.getElementById('chartCategory'), {
    type: 'bar',
    data: { labels: categoryLabels, datasets: [{ data: categoryValues, backgroundColor: '#198754' }]},
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });
})();
</script>
@endsection

