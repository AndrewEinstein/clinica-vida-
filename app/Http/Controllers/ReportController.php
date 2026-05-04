<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\FinancialTransaction;
use App\Models\Triage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->hasAnyRole([User::ROLE_ADMIN, User::ROLE_FINANCE]), 403);

        $from = $request->date('from') ?: now()->startOfMonth();
        $to = $request->date('to') ?: now()->endOfMonth();
        $clinicId = $request->integer('clinic_id') ?: null;

        $appointmentQuery = Appointment::query()
            ->when($clinicId, fn ($query) => $query->where('clinic_id', $clinicId))
            ->whereBetween('scheduled_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);

        $financeQuery = FinancialTransaction::query()
            ->when($clinicId, fn ($query) => $query->where('clinic_id', $clinicId))
            ->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);

        return view('reports.index', [
            'from' => Carbon::parse($from)->toDateString(),
            'to' => Carbon::parse($to)->toDateString(),
            'clinicId' => $clinicId,
            'clinics' => Clinic::orderBy('name')->pluck('name', 'id')->toArray(),
            'appointmentsByStatus' => (clone $appointmentQuery)->selectRaw('status, count(*) total')->groupBy('status')->pluck('total', 'status'),
            'triagesByRisk' => Triage::query()
                ->when($clinicId, fn ($query) => $query->where('clinic_id', $clinicId))
                ->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
                ->selectRaw('risk_classification, count(*) total')
                ->groupBy('risk_classification')
                ->pluck('total', 'risk_classification'),
            'revenue' => (clone $financeQuery)->where('type', 'revenue')->where('status', 'paid')->sum('amount'),
            'expenses' => (clone $financeQuery)->where('type', 'expense')->where('status', 'paid')->sum('amount'),
            'recentTransactions' => (clone $financeQuery)->with('patient')->latest()->limit(10)->get(),
        ]);
    }
}
