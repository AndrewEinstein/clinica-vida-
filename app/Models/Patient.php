<?php

namespace App\Models;

use App\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use BelongsToClinic;
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'insurance_provider_id',
        'name',
        'cpf',
        'rg',
        'birth_date',
        'sex',
        'phone',
        'whatsapp',
        'email',
        'address',
        'notes',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public static function sexOptions(): array
    {
        return [
            'female' => 'Feminino',
            'male' => 'Masculino',
            'other' => 'Outro',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
        ];
    }

    public function insuranceProvider(): BelongsTo
    {
        return $this->belongsTo(InsuranceProvider::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function triages(): HasMany
    {
        return $this->hasMany(Triage::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
