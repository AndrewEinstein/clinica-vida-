<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Clinica Vida+') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar: #20262e;
            --sidebar-muted: #a8b3c2;
            --accent: #0f9f8f;
            --surface: #f4f7f9;
            --line: #dbe3ea;
        }
        body {
            background: var(--surface);
            font-size: 0.95rem;
        }
        .app-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr);
        }
        .sidebar {
            background: var(--sidebar);
            color: #fff;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .brand {
            height: 72px;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 0 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            font-weight: 700;
        }
        .brand-mark {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: var(--accent);
        }
        .mobile-sidebar {
            width: min(86vw, 320px);
            background: var(--sidebar);
            color: #fff;
        }
        .mobile-sidebar.offcanvas {
            left: 0;
            right: auto;
            width: min(86vw, 320px) !important;
            transform: translateX(-100%);
            box-shadow: 16px 0 40px rgba(16, 24, 40, .18);
        }
        .mobile-sidebar.offcanvas.show,
        .mobile-sidebar.offcanvas.showing {
            transform: none !important;
            visibility: visible !important;
        }
        .mobile-sidebar .offcanvas-body {
            overflow-y: auto;
        }
        .mobile-sidebar .brand {
            justify-content: space-between;
        }
        .mobile-sidebar .btn-close {
            opacity: .9;
        }
        .nav-section {
            color: var(--sidebar-muted);
            font-size: .72rem;
            text-transform: uppercase;
            padding: 1.15rem 1rem .35rem;
            letter-spacing: 0;
        }
        .sidebar .nav-link,
        .mobile-sidebar .nav-link {
            color: #dce5ed;
            border-radius: 8px;
            margin: .1rem .75rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            min-height: 40px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active,
        .mobile-sidebar .nav-link:hover,
        .mobile-sidebar .nav-link.active {
            color: #fff;
            background: rgba(15,159,143,.22);
        }
        .content {
            min-width: 0;
        }
        .topbar {
            min-height: 72px;
            background: #fff;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-title {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }
        .menu-toggle {
            flex: 0 0 auto;
        }
        .user-name {
            display: inline-block;
            max-width: 160px;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: bottom;
            white-space: nowrap;
        }
        .main {
            padding: 1.5rem;
        }
        .panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(26, 36, 52, .04);
        }
        .metric {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            padding: 1rem;
            min-height: 116px;
        }
        .metric-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: grid;
            place-items: center;
        }
        .table > :not(caption) > * > * {
            vertical-align: middle;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .btn-icon {
            width: 34px;
            height: 34px;
            display: inline-grid;
            place-items: center;
            padding: 0;
        }
        @media (max-width: 991.98px) {
            .app-shell {
                grid-template-columns: 1fr;
            }
            .brand {
                height: 60px;
                padding: 0 .9rem;
            }
            .brand-mark {
                width: 36px;
                height: 36px;
            }
            .topbar {
                position: sticky;
                top: 0;
                z-index: 1020;
                min-height: auto;
                padding: .9rem 1rem;
                align-items: center;
                gap: .75rem;
            }
            .main {
                padding: 1rem;
            }
            .metric {
                min-height: 104px;
            }
        }
        @media (max-width: 575.98px) {
            body {
                font-size: .9rem;
            }
            .brand {
                height: 56px;
            }
            .brand span:last-child {
                font-size: 1rem;
            }
            .mobile-sidebar .nav-link {
                min-height: 36px;
                padding: .4rem .65rem;
                font-size: .86rem;
            }
            .topbar {
                align-items: center;
            }
            .topbar .dropdown-toggle {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .user-name {
                display: none;
            }
            .main {
                padding: .75rem;
            }
            .panel {
                border-radius: 8px;
            }
            .metric {
                min-height: auto;
                padding: .85rem;
            }
            .table {
                font-size: .86rem;
            }
            .btn {
                white-space: normal;
            }
        }
    </style>
</head>
<body>
@auth
    <div class="app-shell">
        <aside class="sidebar d-none">
            <div class="brand">
                <span class="brand-mark"><i class="bi bi-heart-pulse-fill"></i></span>
                <span>Clinica Vida+</span>
            </div>
            @include('layouts.partials.sidebar-nav')
        </aside>
        <div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="brand">
                <span class="d-flex align-items-center gap-2">
                    <span class="brand-mark"><i class="bi bi-heart-pulse-fill"></i></span>
                    <span id="mobileSidebarLabel">Clinica Vida+</span>
                </span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar menu"></button>
            </div>
            <div class="offcanvas-body p-0">
                @include('layouts.partials.sidebar-nav')
            </div>
        </div>
        <div class="content">
            <header class="topbar">
                <div class="topbar-title">
                    <button class="btn btn-outline-secondary btn-icon menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Abrir menu">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <div class="text-muted small">{{ auth()->user()->clinic?->name ?? 'Todas as clinicas' }}</div>
                        <h1 class="h5 mb-0">{{ $title ?? 'Painel' }}</h1>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><span class="user-name">{{ auth()->user()->name }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <span class="dropdown-item-text small text-muted">{{ auth()->user()->roleLabel() }}</span>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Sair</button>
                        </form>
                    </div>
                </div>
            </header>
            <main class="main">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Revise os campos destacados.</strong>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
@else
    @yield('content')
@endauth
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('input', function (event) {
        if (!event.target.matches('.js-bmi-weight, .js-bmi-height')) return;
        const form = event.target.closest('form');
        const weight = parseFloat(form.querySelector('.js-bmi-weight')?.value || 0);
        const height = parseFloat(form.querySelector('.js-bmi-height')?.value || 0);
        const result = form.querySelector('.js-bmi-result');
        if (result && weight > 0 && height > 0) {
            result.value = (weight / (height * height)).toFixed(2);
        }
    });
</script>
@stack('scripts')
</body>
</html>
