<?php

namespace App\Models;

use App\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    use BelongsToClinic;
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'description',
        'type',
        'category',
        'amount',
        'status',
        'due_date',
        'paid_at',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public static function typeOptions(): array
    {
        return [
            'revenue' => 'Receita',
            'expense' => 'Despesa',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'overdue' => 'Vencido',
            'cancelled' => 'Cancelado',
        ];
    }

    public static function paymentMethodOptions(): array
    {
        return [
            'pix' => 'Pix',
            'credit_card' => 'Cartao de credito',
            'boleto' => 'Boleto',
            'debit_card' => 'Cartao de debito',
            'cash' => 'Dinheiro',
            'bank_transfer' => 'Transferencia bancaria',
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
}
