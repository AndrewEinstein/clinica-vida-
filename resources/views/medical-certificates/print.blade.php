<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $certificate->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f9; }
        .sheet {
            max-width: 820px;
            min-height: 1000px;
            margin: 24px auto;
            background: #fff;
            border: 1px solid #dbe3ea;
            padding: 56px;
        }
        @media print {
            body { background: #fff; }
            .no-print { display: none; }
            .sheet { border: 0; margin: 0; max-width: none; min-height: auto; }
        }
    </style>
</head>
<body>
<div class="no-print text-center mt-3">
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button>
</div>
<main class="sheet">
    <header class="text-center border-bottom pb-4 mb-5">
        <h1 class="h3 mb-1">{{ $certificate->clinic?->name }}</h1>
        <div class="text-muted">{{ $certificate->clinic?->address }} - {{ $certificate->clinic?->city }}/{{ $certificate->clinic?->state }}</div>
    </header>
    <section>
        <h2 class="h4 text-center mb-5">{{ $certificate->title }}</h2>
        <p class="fs-5" style="line-height: 1.8;">{{ $certificate->content }}</p>
        @if($certificate->rest_days)
            <p class="fs-5">Periodo de afastamento: <strong>{{ $certificate->rest_days }} dia(s)</strong>.</p>
        @endif
        <p class="mt-5">{{ $certificate->clinic?->city }}, {{ $certificate->issued_at?->format('d/m/Y') }}.</p>
    </section>
    <footer class="text-center mt-5 pt-5">
        <div style="width: 320px; border-top: 1px solid #222; margin: 0 auto 8px;"></div>
        <strong>{{ $certificate->doctor?->name }}</strong><br>
        CRM {{ $certificate->doctor?->crm }}/{{ $certificate->doctor?->crm_uf }}
    </footer>
</main>
<script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
</script>
</body>
</html>
