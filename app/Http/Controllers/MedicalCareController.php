<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ExamRequest;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalCareController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MedicalRecord::class);

        $query = Appointment::query()
            ->with(['patient', 'doctor', 'triage'])
            ->whereIn('status', [
                Appointment::STATUS_WAITING_DOCTOR,
                Appointment::STATUS_IN_CARE,
                Appointment::STATUS_COMPLETED,
            ])
            ->orderByDesc('scheduled_at');

        if ($request->filled('q')) {
            $term = trim((string) $request->query('q'));
            $query->whereHas('patient', fn ($sub) => $sub->where('name', 'like', '%'.$term.'%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return view('medical-care.index', [
            'appointments' => $query->paginate(10)->withQueryString(),
            'statusOptions' => Appointment::statusOptions(),
        ]);
    }

    public function show(string $medical_care): View
    {
        $appointment = Appointment::query()
            ->with(['patient.insuranceProvider', 'doctor', 'triage.professional', 'medicalRecord'])
            ->findOrFail($medical_care);

        $this->authorize('view', $appointment);

        return view('medical-care.show', [
            'appointment' => $appointment,
            'prescriptions' => Prescription::query()->where('appointment_id', $appointment->id)->latest()->get(),
            'examRequests' => ExamRequest::query()->where('appointment_id', $appointment->id)->latest()->get(),
        ]);
    }

    public function update(Request $request, string $medical_care): RedirectResponse
    {
        $appointment = Appointment::query()->findOrFail($medical_care);
        $this->authorize('changeStatus', $appointment);

        $action = $request->input('action');

        if ($action === 'start') {
            $appointment->update(['status' => Appointment::STATUS_IN_CARE]);

            return back()->with('success', 'Atendimento iniciado.');
        }

        if ($action === 'finish') {
            $appointment->update(['status' => Appointment::STATUS_COMPLETED]);

            return back()->with('success', 'Atendimento concluido.');
        }

        return back()->with('error', 'Acao de atendimento invalida.');
    }
}
