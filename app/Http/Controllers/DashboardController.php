<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\FinancialTransaction;
use App\Models\Patient;
use App\Models\Triage;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();

        return view('dashboard.index', [
            'cards' => [
                ['label' => 'Total de pacientes', 'value' => Patient::count(), 'icon' => 'bi-people', 'color' => 'primary'],
                ['label' => 'Total de medicos', 'value' => Doctor::count(), 'icon' => 'bi-person-badge', 'color' => 'info'],
                ['label' => 'Consultas do dia', 'value' => Appointment::whereDate('scheduled_at', $today)->count(), 'icon' => 'bi-calendar2-check', 'color' => 'success'],
                ['label' => 'Aguardando triagem', 'value' => Triage::where('status', Triage::STATUS_WAITING)->count(), 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                ['label' => 'Triagens em andamento', 'value' => Triage::where('status', Triage::STATUS_IN_PROGRESS)->count(), 'icon' => 'bi-clipboard2-pulse', 'color' => 'info'],
                ['label' => 'Aguardando medico', 'value' => Appointment::where('status', Appointment::STATUS_WAITING_DOCTOR)->count(), 'icon' => 'bi-heart-pulse', 'color' => 'danger'],
                ['label' => 'Receita do mes', 'value' => 'R$ '.number_format((float) FinancialTransaction::where('type', 'revenue')->where('status', 'paid')->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'), 2, ',', '.'), 'icon' => 'bi-cash-stack', 'color' => 'success'],
                ['label' => 'Alertas emergencia', 'value' => Triage::where('risk_classification', Triage::RISK_EMERGENCY)->whereIn('status', [Triage::STATUS_COMPLETED, Triage::STATUS_FORWARDED])->count(), 'icon' => 'bi-exclamation-triangle', 'color' => 'danger'],
            ],
            'appointmentsToday' => Appointment::with(['patient', 'doctor', 'triage'])
                ->whereDate('scheduled_at', $today)
                ->orderBy('scheduled_at')
                ->limit(8)
                ->get(),
            'emergencies' => Triage::with(['patient', 'appointment.doctor'])
                ->where('risk_classification', Triage::RISK_EMERGENCY)
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }
}
