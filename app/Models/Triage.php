<?php

namespace App\Models;

use App\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Triage extends Model
{
    use BelongsToClinic;
    use HasFactory;

    public const RISK_EMERGENCY = 'emergency';
    public const RISK_VERY_URGENT = 'very_urgent';
    public const RISK_URGENT = 'urgent';
    public const RISK_LOW_URGENT = 'low_urgent';
    public const RISK_NOT_URGENT = 'not_urgent';

    public const STATUS_WAITING = 'waiting';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FORWARDED = 'forwarded';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'professional_id',
        'triaged_at',
        'chief_complaint',
        'symptoms',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'oxygen_saturation',
        'weight',
        'height',
        'bmi',
        'blood_glucose',
        'pain_level',
        'allergies',
        'current_medications',
        'pre_existing_conditions',
        'notes',
        'risk_classification',
        'status',
    ];

    protected $casts = [
        'triaged_at' => 'datetime',
        'temperature' => 'decimal:1',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'bmi' => 'decimal:2',
    ];

    public static function riskOptions(): array
    {
        return [
            self::RISK_EMERGENCY => 'Emergencia',
            self::RISK_VERY_URGENT => 'Muito urgente',
            self::RISK_URGENT => 'Urgente',
            self::RISK_LOW_URGENT => 'Pouco urgente',
            self::RISK_NOT_URGENT => 'Nao urgente',
        ];
    }

    public static function riskBadges(): array
    {
        return [
            self::RISK_EMERGENCY => 'danger',
            self::RISK_VERY_URGENT => 'warning',
            self::RISK_URGENT => 'primary',
            self::RISK_LOW_URGENT => 'info',
            self::RISK_NOT_URGENT => 'success',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_WAITING => 'Aguardando triagem',
            self::STATUS_IN_PROGRESS => 'Em triagem',
            self::STATUS_COMPLETED => 'Triagem concluida',
            self::STATUS_FORWARDED => 'Encaminhado ao medico',
            self::STATUS_CANCELLED => 'Cancelado',
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

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function riskLabel(): string
    {
        return self::riskOptions()[$this->risk_classification] ?? $this->risk_classification;
    }

    public function riskBadge(): string
    {
        return self::riskBadges()[$this->risk_classification] ?? 'secondary';
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }
}
