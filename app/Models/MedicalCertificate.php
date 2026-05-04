<?php

namespace App\Models;

use App\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalCertificate extends Model
{
    use BelongsToClinic;
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'doctor_id',
        'title',
        'content',
        'rest_days',
        'issued_at',
        'status',
        'attachment_path',
        'attachment_name',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public static function statusOptions(): array
    {
        return [
            'draft' => 'Rascunho',
            'issued' => 'Emitido',
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
