<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'key',
        'name',
        'description',
        'is_system',
        'status',
    ];

    public static function statusOptions(): array
    {
        return [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}

