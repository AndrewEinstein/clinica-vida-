<?php

namespace App\Models;

use App\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamRequest extends Model
{
    use BelongsToClinic;
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'doctor_id',
        'exam_name',
        'indication',
        'priority',
        'status',
        'requested_at',
        'result_notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];

    public static function priorityOptions(): array
    {
        return [
            'routine' => 'Rotina',
            'urgent' => 'Urgente',
            'emergency' => 'Emergencia',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'requested' => 'Solicitado',
            'scheduled' => 'Agendado',
            'completed' => 'Concluido',
            'cancelled' => 'Cancelado',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
