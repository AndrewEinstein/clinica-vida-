<nav class="py-2">
    <div class="nav-section">Operacao</div>
    @can('dashboard.view')
        <a class="nav-link @activeRoute('dashboard') active @endactiveRoute" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    @endcan
    @can('viewAny', \App\Models\Appointment::class)
        <a class="nav-link @activeRoute('appointments.*') active @endactiveRoute" href="{{ route('appointments.index') }}"><i class="bi bi-calendar2-week"></i> Agenda</a>
    @endcan
    @can('viewAny', \App\Models\Triage::class)
        <a class="nav-link @activeRoute('triages.*') active @endactiveRoute" href="{{ route('triages.index') }}"><i class="bi bi-clipboard2-pulse"></i> Triagem</a>
    @endcan
    @can('viewAny', \App\Models\MedicalRecord::class)
        <a class="nav-link @activeRoute('medical-care.*') active @endactiveRoute" href="{{ route('medical-care.index') }}"><i class="bi bi-heart-pulse"></i> Atendimento</a>
        <a class="nav-link @activeRoute('medical-records.*') active @endactiveRoute" href="{{ route('medical-records.index') }}"><i class="bi bi-journal-medical"></i> Prontuarios</a>
    @endcan

    <div class="nav-section">Cadastros</div>
    @can('viewAny', \App\Models\Clinic::class)
        <a class="nav-link @activeRoute('clinics.*') active @endactiveRoute" href="{{ route('clinics.index') }}"><i class="bi bi-buildings"></i> Clinicas</a>
    @endcan
    @can('viewAny', \App\Models\User::class)
        <a class="nav-link @activeRoute('users.*') active @endactiveRoute" href="{{ route('users.index') }}"><i class="bi bi-person-gear"></i> Usuarios</a>
    @endcan
    @can('viewAny', \App\Models\Doctor::class)
        <a class="nav-link @activeRoute('doctors.*') active @endactiveRoute" href="{{ route('doctors.index') }}"><i class="bi bi-person-badge"></i> Medicos</a>
    @endcan
    @can('viewAny', \App\Models\Patient::class)
        <a class="nav-link @activeRoute('patients.*') active @endactiveRoute" href="{{ route('patients.index') }}"><i class="bi bi-people"></i> Pacientes</a>
    @endcan
    @can('viewAny', \App\Models\InsuranceProvider::class)
        <a class="nav-link @activeRoute('insurance-providers.*') active @endactiveRoute" href="{{ route('insurance-providers.index') }}"><i class="bi bi-shield-check"></i> Convenios</a>
    @endcan

    <div class="nav-section">Clinico e gestao</div>
    @can('viewAny', \App\Models\MedicalCertificate::class)
        <a class="nav-link @activeRoute('medical-certificates.*') active @endactiveRoute" href="{{ route('medical-certificates.index') }}"><i class="bi bi-file-medical"></i> Atestados</a>
    @endcan
    @can('viewAny', \App\Models\Prescription::class)
        <a class="nav-link @activeRoute('prescriptions.*') active @endactiveRoute" href="{{ route('prescriptions.index') }}"><i class="bi bi-capsule"></i> Receita medica</a>
    @endcan
    @can('viewAny', \App\Models\ExamRequest::class)
        <a class="nav-link @activeRoute('exam-requests.*') active @endactiveRoute" href="{{ route('exam-requests.index') }}"><i class="bi bi-file-earmark-medical"></i> Exames</a>
    @endcan
    @can('viewAny', \App\Models\FinancialTransaction::class)
        <a class="nav-link @activeRoute('finance.*') active @endactiveRoute" href="{{ route('finance.index') }}"><i class="bi bi-cash-coin"></i> Financeiro</a>
        <a class="nav-link @activeRoute('reports.*') active @endactiveRoute" href="{{ route('reports.index') }}"><i class="bi bi-bar-chart"></i> Relatorios</a>
    @endcan
    @can('viewAny', \App\Models\ClinicSetting::class)
        <a class="nav-link @activeRoute('settings.*') active @endactiveRoute" href="{{ route('settings.index') }}"><i class="bi bi-sliders"></i> Configuracoes</a>
    @endcan

    @can('viewAny', \App\Models\Role::class)
        <a class="nav-link @activeRoute('roles.*') active @endactiveRoute" href="{{ route('roles.index') }}"><i class="bi bi-diagram-3"></i> Perfis</a>
    @endcan

    @can('role-permissions.manage')
        <a class="nav-link @activeRoute('settings.role-permissions.*') active @endactiveRoute" href="{{ route('settings.role-permissions.index') }}"><i class="bi bi-shield-lock"></i> Perfis e permissoes</a>
    @endcan

    @can('viewAny', \App\Models\ItTicket::class)
        <div class="nav-section">TI</div>
        <a class="nav-link @activeRoute('it-tickets.*') active @endactiveRoute" href="{{ route('it-tickets.index') }}"><i class="bi bi-headset"></i> Chamados TI</a>
    @endcan
</nav>
