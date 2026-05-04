<?php

namespace App\Models;

use App\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use BelongsToClinic;
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'doctor_id',
        'medications',
        'instructions',
        'issued_at',
        'status',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public static function statusOptions(): array
    {
        return [
            'draft' => 'Rascunho',
            'issued' => 'Emitida',
            'cancelled' => 'Cancelada',
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
