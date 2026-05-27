<?php

namespace App\Http\Controllers;

use App\Models\ItTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItTicketDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $this->authorize('viewAny', ItTicket::class);

        $base = ItTicket::query();

        if (! $request->user()?->isSuperAdmin()) {
            $base->where('clinic_id', $request->user()?->clinic_id);
        }

        // Dashboard scope:
        // - TI/Admin (it-tickets.edit) sees all in the clinic
        // - Common user sees only their tickets
        $canSeeAll = $request->user()?->isSuperAdmin() || $request->user()?->hasPermission('it-tickets.edit') || $request->user()?->hasRole(\App\Models\User::ROLE_ADMIN);
        if (! $canSeeAll) {
            $base->where(function ($q) use ($request) {
                $q->where('requester_user_id', $request->user()?->id)
                    ->orWhere('assigned_user_id', $request->user()?->id);
            });
        }

        $totalOpen = (clone $base)->whereIn('status', [ItTicket::STATUS_OPEN, ItTicket::STATUS_IN_PROGRESS, ItTicket::STATUS_WAITING_USER])->count();
        $totalInProgress = (clone $base)->where('status', ItTicket::STATUS_IN_PROGRESS)->count();
        $totalWaitingUser = (clone $base)->where('status', ItTicket::STATUS_WAITING_USER)->count();
        $totalResolved = (clone $base)->where('status', ItTicket::STATUS_RESOLVED)->count();
        $totalOverdue = (clone $base)->whereNull('resolved_at')->whereNotNull('sla_due_at')->where('sla_due_at', '<', now())->count();

        $byStatus = (clone $base)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $byPriority = (clone $base)
            ->select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->orderBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        $byCategory = (clone $base)
            ->select('category', DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'category')
            ->toArray();

        $byTech = (clone $base)
            ->select('assigned_user_id', DB::raw('count(*) as total'))
            ->whereNotNull('assigned_user_id')
            ->groupBy('assigned_user_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->mapWithKeys(fn ($row) => [(string) $row->assigned_user_id => (int) $row->total])
            ->toArray();

        // Calls opened last 14 days
        $openedByDay = (clone $base)
            ->select(DB::raw("to_char(created_at::date, 'YYYY-MM-DD') as day"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => ['day' => $row->day, 'total' => (int) $row->total])
            ->all();

        $avgResolveSeconds = (clone $base)
            ->whereNotNull('resolved_at')
            ->selectRaw('avg(extract(epoch from (resolved_at - created_at))) as avg_seconds')
            ->value('avg_seconds') ?: 0;

        return view('it-tickets.dashboard', [
            'cards' => [
                ['label' => 'Abertos', 'value' => $totalOpen, 'icon' => 'bi-inbox', 'color' => 'primary'],
                ['label' => 'Em atendimento', 'value' => $totalInProgress, 'icon' => 'bi-tools', 'color' => 'info'],
                ['label' => 'Aguardando usuario', 'value' => $totalWaitingUser, 'icon' => 'bi-person-check', 'color' => 'warning'],
                ['label' => 'Resolvidos', 'value' => $totalResolved, 'icon' => 'bi-check2-circle', 'color' => 'success'],
                ['label' => 'Atrasados', 'value' => $totalOverdue, 'icon' => 'bi-exclamation-triangle', 'color' => 'danger'],
                ['label' => 'Tempo medio resolucao', 'value' => round(((float) $avgResolveSeconds) / 3600, 1).'h', 'icon' => 'bi-clock-history', 'color' => 'secondary'],
            ],
            'byStatus' => $byStatus,
            'byPriority' => $byPriority,
            'byCategory' => $byCategory,
            'byTech' => $byTech,
            'openedByDay' => $openedByDay,
        ]);
    }
}
