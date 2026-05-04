<?php

namespace App\Models;

use App\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use BelongsToClinic;
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'user_id',
        'name',
        'cpf',
        'rg',
        'crm',
        'crm_uf',
        'specialty',
        'phone',
        'whatsapp',
        'email',
        'address',
        'consultation_fee',
        'working_hours',
        'status',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'working_hours' => 'array',
    ];

    public static function statusOptions(): array
    {
        return [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function workingHoursText(): string
    {
        return data_get($this->working_hours, 'description', '');
    }
}
