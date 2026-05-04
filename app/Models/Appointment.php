<?php

namespace App\Models;

use App\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use BelongsToClinic;
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_WAITING_TRIAGE = 'waiting_triage';
    public const STATUS_IN_TRIAGE = 'in_triage';
    public const STATUS_WAITING_DOCTOR = 'waiting_doctor';
    public const STATUS_IN_CARE = 'in_care';
    public const STATUS_FINISHED = 'finished';

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'doctor_id',
        'insurance_provider_id',
        'scheduled_at',
        'duration_minutes',
        'type',
        'reason',
        'status',
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Agendada',
            self::STATUS_CONFIRMED => 'Confirmada',
            self::STATUS_WAITING_TRIAGE => 'Aguardando triagem',
            self::STATUS_IN_TRIAGE => 'Em triagem',
            self::STATUS_WAITING_DOCTOR => 'Aguardando medico',
            self::STATUS_IN_CARE => 'Em atendimento',
            self::STATUS_COMPLETED => 'Consulta concluida',
            self::STATUS_FINISHED => 'Finalizada',
            self::STATUS_CANCELLED => 'Cancelada',
        ];
    }

    public static function badgeOptions(): array
    {
        return [
            self::STATUS_SCHEDULED => 'secondary',
            self::STATUS_CONFIRMED => 'primary',
            self::STATUS_WAITING_TRIAGE => 'warning',
            self::STATUS_IN_TRIAGE => 'info',
            self::STATUS_WAITING_DOCTOR => 'danger',
            self::STATUS_IN_CARE => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FINISHED => 'success',
            self::STATUS_CANCELLED => 'dark',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function insuranceProvider(): BelongsTo
    {
        return $this->belongsTo(InsuranceProvider::class);
    }

    public function triage(): HasOne
    {
        return $this->hasOne(Triage::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function statusBadge(): string
    {
        return self::badgeOptions()[$this->status] ?? 'secondary';
    }
}
