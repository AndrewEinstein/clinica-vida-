<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItTicket extends Model
{
    use HasFactory;

    public const TYPE_ERROR = 'error';
    public const TYPE_IMPROVEMENT = 'improvement';
    public const TYPE_CORRECTION = 'correction';
    public const TYPE_OTHER = 'other';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public const IMPACT_LOW = 'low';
    public const IMPACT_MEDIUM = 'medium';
    public const IMPACT_HIGH = 'high';

    public const URGENCY_LOW = 'low';
    public const URGENCY_MEDIUM = 'medium';
    public const URGENCY_HIGH = 'high';
    public const URGENCY_URGENT = 'urgent';

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_WAITING_USER = 'waiting_user';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'clinic_id',
        'requester_user_id',
        'assigned_user_id',
        'type',
        'priority',
        'urgency',
        'impact',
        'status',
        'category',
        'subcategory',
        'requester_department',
        'subject',
        'description',
        'internal_notes',
        'resolution_notes',
        'sla_due_at',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'sla_due_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_ERROR => 'Erro',
            self::TYPE_IMPROVEMENT => 'Melhoria',
            self::TYPE_CORRECTION => 'Correcao',
            self::TYPE_OTHER => 'Outro',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => 'Baixa',
            self::PRIORITY_MEDIUM => 'Media',
            self::PRIORITY_HIGH => 'Alta',
            self::PRIORITY_URGENT => 'Urgente',
        ];
    }

    public static function impactOptions(): array
    {
        return [
            self::IMPACT_LOW => 'Baixo',
            self::IMPACT_MEDIUM => 'Medio',
            self::IMPACT_HIGH => 'Alto',
        ];
    }

    public static function urgencyOptions(): array
    {
        return [
            self::URGENCY_LOW => 'Baixa',
            self::URGENCY_MEDIUM => 'Media',
            self::URGENCY_HIGH => 'Alta',
            self::URGENCY_URGENT => 'Urgente',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_OPEN => 'Aberto',
            self::STATUS_IN_PROGRESS => 'Em andamento',
            self::STATUS_WAITING_USER => 'Aguardando usuario',
            self::STATUS_RESOLVED => 'Resolvido',
            self::STATUS_CLOSED => 'Fechado',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ItTicketComment::class, 'ticket_id')->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ItTicketAttachment::class, 'ticket_id')->latest();
    }

    public function events(): HasMany
    {
        return $this->hasMany(ItTicketEvent::class, 'ticket_id')->latest();
    }

    public function isOverdue(): bool
    {
        return $this->sla_due_at !== null
            && $this->resolved_at === null
            && $this->closed_at === null
            && now()->greaterThan($this->sla_due_at);
    }
}
